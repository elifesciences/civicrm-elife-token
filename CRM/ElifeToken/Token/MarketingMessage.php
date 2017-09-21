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

      $civinky = new CRM_ElifeToken_Civinky;

      $css = file_get_contents(CIVICRM_UF_BASEURL."sites/all/libraries/elife-newsletter-assets/newsletter.css");
      $html = 'columns: p.promo '.json_decode(file_get_contents(CIVICRM_UF_BASEURL."newsletters/marketing-message/$type"), true)['html'];

      $this->token[$type] = $civinky->query($html, $css, true);
    }
    return $this->token[$type];
  }
}
