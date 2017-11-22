<?php
/**
 * [CRM_ElifeToken_TokenService description]
 */
class CRM_ElifeToken_TokenService{

  function getDefinitions(){
    $definitions = [
      'articles.poa_last_7_days' => 'POA articles published in the last 7 days',
      'articles.poa_marketing' => 'POA marketing message',
      'articles.vor_last_7_days' => 'VOR articles published in the last 7 days',
      'articles.vor_marketing' => 'VOR marketing message',
      'articles.magazine_last_7_days' => 'Magazine articles published in the last 7 days',
      'articles.ga_tracking' => 'Google analytics tracking params for elife newsletters',
      'articles.date' => 'Date the newsletter is sent'
    ];
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
        $token = CRM_ElifeToken_Token_GATracking::Instance();
        $value = $token->get();
        break;
      }
      case 'marketing_message':{
        $token = CRM_ElifeToken_Token_MarketingMessage::Instance();
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
      case 'date':{
        $value = date('F j, Y');
        break;
      }
    }
    return $value;
  }
}
