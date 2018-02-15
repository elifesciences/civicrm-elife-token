<?php

/**
 * Repsonsible for querying Civinky
 */
class CRM_ElifeToken_Civinky{

  static $cache = [];

  public static function Instance(){
      static $inst = null;
      if ($inst === null) {
          $inst = new CRM_ElifeToken_Civinky;
      }
      return $inst;
  }

  private function __construct($civinkyUrl = 'http://localhost:30649/generate'){
    $this->civinkyUrl = $civinkyUrl;
  }

  function query($pug = '', $json = [], $css = '', $snippet = false){

    $data = json_encode([
      'pug' => $pug,
      'json' => $json,
      'css' => $css,
      'snippet' => $snippet
    ]);

    if(!isset(self::$cache[$data])){
      $options = ['http' => [
        'method'  => 'POST',
        'header'  => 'Content-type: application/json',
        'content' => $data
        ]];

        $context = stream_context_create($options);
        self::$cache[$data] = file_get_contents($this->civinkyUrl, false, $context);
    }

    return self::$cache[$data];
  }
}
