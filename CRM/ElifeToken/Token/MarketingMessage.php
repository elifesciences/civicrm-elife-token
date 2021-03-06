<?php

class CRM_ElifeToken_Token_MarketingMessage{

  var $token;

  public static function Instance(){
      static $inst = null;
      if ($inst === null) {
          $inst = new CRM_ElifeToken_Token_MarketingMessage;
      }
      return $inst;
  }

  private function __construct(){
  }

  function get($type){
    if(!isset($this->token[$type])){

      $civinky = CRM_ElifeToken_Civinky::Instance();
      $css = file_get_contents(CIVICRM_UF_BASEURL."/sites/all/libraries/elife-newsletter-assets/newsletter.css");
      $html = "columns: p.promo ".json_decode(file_get_contents(CIVICRM_UF_BASEURL."/newsletters/marketing-message/$type"), true)['html'];

      $trackingToken = CRM_ElifeToken_Token_GATracking::Instance();
      $html = str_replace('{track}', $trackingToken->get(), $html);

      $this->token[$type] = $civinky->query($html, [], $css, true);
    }
    return $this->token[$type];
  }
}
