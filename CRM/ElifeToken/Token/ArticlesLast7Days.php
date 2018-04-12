<?php

class CRM_ElifeToken_Token_ArticlesLast7Days{


  /**
   * Cache for json requests so we go easy of the elife API
   * @var array
   */
  static $elifeApiCache = [];
  static $gaTokenCache;

  /**
   * Custom field containing interests
   * @var string
   */
  static $subjectsCustomFieldRef = 'interests.subjects';

  static $magazineVorTypes = ['editorial', 'feature', 'insight'];

  static $excludeTypes = ['correction'];

  public static function Instance(){
      static $inst = null;
      if ($inst === null) {
          $inst = new CRM_ElifeToken_Token_ArticlesLast7Days;
      }
      return $inst;
  }

  private function __construct(){
  }

  /**
   * Gets the html for each recipient
   * @method get
   * @param  int $cid Contact ID
   * @return string HTML of the token
   */
  function get($contactId, $jobId, $type){
    // Get the JSON
    $template = file_get_contents(civicrm_elife_token_get_root_dir()."/pug/articles.pug");

    // TODO For now, return all subjects and do not filter
    // $subjects = $this->getSubjects($contactId);
    // $articles = $this->getArticles($type, $subjects);
    $title = ($type == 'magazine') ? 'MAGAZINE' : 'LATEST RESEARCH';
    $articles = $this->getArticles($type);

    if(!count($articles['items'])){
      return '';
    }

    $gaToken = $this->getGAToken();
    $css = file_get_contents(CIVICRM_UF_BASEURL."/sites/all/libraries/elife-newsletter-assets/newsletter.css");

    $civinky = CRM_ElifeToken_Civinky::Instance();
    $html = $civinky->query($template, [
      'title' => $title,
      'articles' => $articles,
      'gaToken' => $gaToken
    ], $css, true);

    if($jobId){
      $html = $this->trackUrls($html, $contactId, $jobId);
    }
    return $html;
  }


  function getSubjects($cid){
    $refs = explode('.', self::$subjectsCustomFieldRef);
    $subjectsCustomField = civicrm_api3('CustomField', 'getsingle', ['custom_group_id' => $refs[0], 'name' => $refs[1]]);
    $options = civicrm_api3('OptionValue', 'get', ['option_group_id' => $subjectsCustomField['option_group_id']]);
    foreach($options['values'] as $option){
      $translate[$option['value']] = $option['label'];
    }
    $subjects = civicrm_api3('Contact', 'getvalue', ['return' => 'custom_'.$subjectsCustomField['id'], 'id' => $cid]);
    foreach($subjects as $k => $subject){
      $subjects[$k] = $translate[$subject];
    }
    return $subjects;
  }

  function getArticles($type, $subjects = null){

    // Start date is midnight starting the day of 6 days ago
    // which means if we execute this at 7PM we cover 6.8 days
    $startDate = new DateTime('-6 day');
    $query[] = 'start-date='.$startDate->format('Y-m-d');
    // Don't use any end date, get everything that is available

    $query[] = 'per-page=100';

    $url = 'search?'.implode('&', $query);
    $elifeApi = CRM_ElifeToken_ElifeApi::Instance();
    $result = $elifeApi->query($url);
    $filtered = call_user_func([$this, 'filter'.ucfirst($type)], $result);

    foreach($filtered['items'] as &$item) {
      $article = $elifeApi->query("articles/{$item['id']}");
      if(count($article['authors']) < 4){
        $authors = [];
        foreach($article['authors'] as $author){
          $authors[] = $author['name']['preferred'];
        }
        $item['authorLineAlternate'] = implode(", ", $authors);
      } else {
        $item['authorLineAlternate'] =
          $article['authors'][0]['name']['preferred'] . ', ' .
          $article['authors'][1]['name']['preferred'] . ' ... ' .
          end($article['authors'])['name']['preferred'];
      }
    }
    return $filtered;
  }

  function getGAToken(){

    if(!isset(self::$gaTokenCache)){
      $token = CRM_ElifeToken_Token_GATracking::Instance();
      self::$gaTokenCache = $token->get();
    }

    return self::$gaTokenCache;
  }

  function trackUrls($html, $contactId, $jobId){

    // Find all URLs
    preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $html, $matches);

    // We need to find the mailingId and the eventQueueId
    $mailingJob = civicrm_api3('MailingJob', 'getsingle', ['id' => $jobId]);
    $mailingEventQueue = civicrm_api3('MailingEventQueue', 'getsingle', [ 'job_id' => $jobId, 'contact_id' => $contactId]);

    foreach($matches[0] as $url){
      $replacements[$url] = CRM_Mailing_BAO_TrackableURL::getTrackerURL($url, $mailingJob['mailing_id'], $mailingEventQueue['id']);
    }

    $html = str_replace(array_keys($replacements), $replacements, $html);

    return $html;
  }

  function filterPoa($content){
    $content['items'] = array_filter($content['items'], function($item){
      if(isset($item['status'])){
        return $item['status'] == 'poa' and !in_array($item['type'], array_merge(self::$magazineVorTypes, self::$excludeTypes));
      }
    });
    return $content;
  }

  function filterVor($content){
    $content['items'] = array_filter($content['items'], function($item){
      if(isset($item['status'])){
        return $item['status'] == 'vor' and !in_array($item['type'], array_merge(self::$magazineVorTypes, self::$excludeTypes));
      }
    });
    return $content;
  }

  function filterMagazine($content){
    $content['items'] = array_filter($content['items'], function($item){
      return array_key_exists('status', $item) and $item['status'] == 'vor' and in_array($item['type'], self::$magazineVorTypes);
    });
    return $content;
  }
}
