<?php

/**
 * Repsonsible for querying Civinky
 */
class CRM_ElifeToken_ElifeApi{

  CONST BASE_URL = 'https://prod--gateway.elifesciences.org/';

  static $cache = [];

  public static function Instance(){
      static $inst = null;
      if ($inst === null) {
          $inst = new self;
      }
      return $inst;
  }

  private function __construct(){
  }


  function query($path){
    if(!isset(self::$cache[$path])){
      $fullPath = self::BASE_URL.$path;
      self::$cache[$path] = json_decode(file_get_contents($fullPath), true);
    }else{
    }
    return self::$cache[$path];
  }

}
