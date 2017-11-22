<?php

class CRM_ElifeToken_Token_GATracking{

  var $token;

  public static function Instance(){
      static $inst = null;
      if ($inst === null) {
          $inst = new CRM_ElifeToken_Token_GATracking();
      }
      return $inst;
  }

  private function __construct(){
  }

  function get(){
    if(!isset($this->token)){
      $this->token = 'utm_source=content_alert&utm_medium=email&utm_content=fulltext&utm_campaign=' . date('j-F-y') . '-elife-alert';
    }
    return $this->token;
  }
}
