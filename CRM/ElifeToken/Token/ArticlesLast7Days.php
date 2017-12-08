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
    $title = ($type == 'magazine') ? 'MAGAZINE' : 'LATEST';
    $articles = $this->getArticles($type);

    if(!count($articles['items'])){
      return '';
    }

    $gaToken = $this->getGAToken();
    $css = file_get_contents(CIVICRM_UF_BASEURL."/sites/all/libraries/elife-newsletter-assets/newsletter.css");

    $civinky = new CRM_ElifeToken_Civinky;
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
    // var_dump($type);

    // Construct the URL
    $path = 'https://prod--gateway.elifesciences.org/search';

    // Start date is midnight this morning - 7 days
    $startDate = DateTime::createFromFormat('Y-m-d H:i:s', date_format(new DateTime('-7 day'), 'Y-m-d 19:00:00'));
    $query[] = 'start-date='.$startDate->format('Y-m-d');

    // End date is the time of sending
    $endDate = new DateTime('now');
    $query[] = 'end-date='.$endDate->format('Y-m-d');

    $query[] = 'per-page=100';


    // TODO Filter based on contact subject preferences - paused for now
    // foreach($subjects as $subject){
    //   $query[] = '&subject[]=' . $subject;
    // }

    $url = $path.'?'.implode('&', $query);

    // Check if we have retrieved this already
    if(!isset(self::$elifeApiCache[$url])){
      self::$elifeApiCache[$url] = json_decode(file_get_contents($url), true);
    }

    $result = self::$elifeApiCache[$url];

    $filtered = call_user_func([$this, 'filter'.ucfirst($type)], $result);

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
        return $item['status'] == 'poa' and !in_array($item['type'], self::$excludeTypes);;
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
      return in_array($item['type'], self::$magazineVorTypes);
    });
    return $content;
  }
}
