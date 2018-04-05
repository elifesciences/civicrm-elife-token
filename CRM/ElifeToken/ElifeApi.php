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
    echo "created new instance\n";
  }


  function query($path){
    var_dump();
    if(!isset(self::$cache[$path])){
      echo "{$path} missed cache\n";
      $fullPath = self::BASE_URL.$path;
      self::$cache[$path] = json_decode(file_get_contents($fullPath), true);
    }else{
      echo "{$path} hit cache\n";
    }
    return self::$cache[$path];
  }

}
