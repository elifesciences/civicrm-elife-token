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
    if(!isset($this->$token)){
      // TODO file_get_contents() a marketing message
      $this->token = '<p>marketing!</p>';
    }
    return $this->token;
  }
}
