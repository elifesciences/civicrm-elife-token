<?php
class CRM_ElifeArticleToken_ArticleTokenGenerator{

  // Cache for json requests
  static $jsonCache = [];

  static $subjectsCustomFieldRef = 'interests.subjects';

  static $civinkyUrl = 'http://localhost:3000/generate';

  function getDefinitions(){
    $definitions = ['articles.last_7_days' => 'Articles published in the last 7 days'];
    return $definitions;
  }

  function getValue($token, $cid){
    switch($token) {
      case 'last_7_days':{
        $value = $this->getLast7Days($cid);
      }
    }
    return $value;
  }

  function getLast7Days($cid){

    // Get the JSON
    $subjects = $this->getSubjects($cid);
    $startDate = DateTime::createFromFormat('Y-m-d H:i:s', date_format(new DateTime('-7 day'), 'Y-m-d 00:00:00'));
    $endDate = DateTime::createFromFormat('Y-m-d H:i:s', date_format(new DateTime('-1 day'), 'Y-m-d 23:23:59'));

    // Construct the URL
    $url = 'https://prod--gateway.elifesciences.org/search';
    $url .= '?start-date='.$startDate->format('Y-m-d');
    $url .= '&end-date='.$endDate->format('Y-m-d');
    foreach($subjects as $subject){
      $url .= '&subject[]=' . $subject;
    }
    echo $url;
    $data = [
      'pug' => $this->getPug('last-7-days'),
      'json' => $this->getJson($url),
      'css' => $this->getCss('newsletter'),
      'snippet' => 'true'
    ];
    return $this->queryCivinky($data);
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

  function getJson($url){
    if(!isset(self::$jsonCache[$url])){
      self::$jsonCache[$url] = file_get_contents($url);
    }
    return self::$jsonCache[$url];
  }

  function getPug($fileName){
    return file_get_contents(civicrm_elife_article_token_get_root_dir()."/pug/{$fileName}.pug");
  }

  function getCss($fileName){
    return file_get_contents(civicrm_elife_article_token_get_root_dir()."/css/{$fileName}.css");
  }

  function queryCivinky($data){
    $curl = curl_init(self::$civinkyUrl);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
  }


}
