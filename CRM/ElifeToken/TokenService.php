<?php
/**
 * [CRM_ElifeToken_TokenService description]
 */
class CRM_ElifeToken_TokenService{

  function getDefinitions(){
    $definitions = ['articles.last_7_days' => 'Articles published in the last 7 days'];
    $definitions = ['articles.ga_tracking' => 'Google analytics tracking params for elife newsletters'];
    return $definitions;
  }

  function getValue($token, $cid, $job){
    switch($token) {
      case 'last_7_days':{
        $token = new CRM_ElifeToken_Token_ArticlesLast7Days;
        $value = $token->get($cid, $job);
        break;
      }
      case 'ga_tracking':{
        $token = new CRM_ElifeToken_Token_GATracking;
        $value = $token->get();
        break;
      }
    }
    return $value;
  }
}
