<?php
/**
 * [CRM_ElifeToken_TokenService description]
 */
class CRM_ElifeToken_TokenService{

  function getDefinitions(){
    $definitions = ['articles.poa_last_7_days' => 'POA articles published in the last 7 days'];
    $definitions = ['articles.poa_marketing' => 'POA marketing message'];
    $definitions = ['articles.vor_last_7_days' => 'VOR articles published in the last 7 days'];
    $definitions = ['articles.vor_marketing' => 'VOR marketing message'];
    $definitions = ['articles.magazine_last_7_days' => 'Magazine articles published in the last 7 days'];
    $definitions = ['articles.ga_tracking' => 'Google analytics tracking params for elife newsletters'];
    return $definitions;
  }

  function getValue($token, $cid, $job){
    switch($token) {
      case 'poa_last_7_days':{
        $token = new CRM_ElifeToken_Token_ArticlesLast7Days;
        $value = $token->get($cid, $job, 'poa');
        break;
      }
      case 'vor_last_7_days':{
        $token = new CRM_ElifeToken_Token_ArticlesLast7Days;
        $value = $token->get($cid, $job, 'vor');
        break;
      }
      case 'magazine_last_7_days':{
        $token = new CRM_ElifeToken_Token_ArticlesLast7Days;
        $value = $token->get($cid, $job, 'magazine');
        break;
      }
      case 'ga_tracking':{
        $token = new CRM_ElifeToken_Token_GATracking;
        $value = $token->get();
        break;
      }
      case 'poa_marketing':{
        $token = CRM_ElifeToken_Token_MarketingMessage::Instance();
        $value = $token->get('poa');
        break;
      }
      case 'vor_marketing':{
        $token = CRM_ElifeToken_Token_MarketingMessage::Instance();
        $value = $token->get('vor');
        break;
      }
    }
    return $value;
  }
}
