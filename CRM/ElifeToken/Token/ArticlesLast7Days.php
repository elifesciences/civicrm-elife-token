<?php

class CRM_ElifeToken_Token_ArticlesLast7Days{


  /**
   * Cache for json requests so we go easy of the elife API
   * @var array
   */
  static $elifeApiCache = [];

  /**
   * Custom field containing interests
   * @var string
   */
  static $subjectsCustomFieldRef = 'interests.subjects';

  /**
   * Gets the html for each recipient
   * @method get
   * @param  int $cid Contact ID
   * @return string HTML of the token
   */
  function get($contactId, $jobId){

    // Get the JSON
    $template = file_get_contents(civicrm_elife_token_get_root_dir()."/pug/last-7-days.pug");
    $subjects = $this->getSubjects($contactId);
    $articles = $this->getArticles($subjects);
    $css = file_get_contents(civicrm_elife_token_get_root_dir()."/css/newsletter.css");

    $civinky = new CRM_ElifeToken_Civinky;
    $html = $civinky->query($template, $articles, $css, true);

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

  function getArticles($subjects){

    // Construct the URL
    $url = 'https://prod--gateway.elifesciences.org/search';

    // Start date is midnight this morning - 7 days
    $startDate = DateTime::createFromFormat('Y-m-d H:i:s', date_format(new DateTime('-7 day'), 'Y-m-d 00:00:00'));
    $url .= '?start-date='.$startDate->format('Y-m-d');

    // End date last second of yesterday
    $endDate = DateTime::createFromFormat('Y-m-d H:i:s', date_format(new DateTime('-1 day'), 'Y-m-d 23:23:59'));
    $url .= '&end-date='.$endDate->format('Y-m-d');

    // Add each subject to the URL
    foreach($subjects as $subject){
      $url .= '&subject[]=' . $subject;
    }

    // Check if we have retrieved this already
    if(!isset(self::$elifeApiCache[$url])){
      self::$elifeApiCache[$url] = file_get_contents($url);
    }

    return self::$elifeApiCache[$url];
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
}
