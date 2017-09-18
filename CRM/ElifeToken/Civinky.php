<?php

/**
 * Repsonsible for querying Civinky
 */
class CRM_ElifeToken_Civinky{

  function __construct($civinkyUrl = 'http://localhost:30649/generate'){
    $this->civinkyUrl = $civinkyUrl;
  }

  function query($pug = '', $json = '{}', $css = '', $snippet = false){

    $data = [
      'pug' => $pug,
      'json' => $json,
      'css' => $css,
      'snippet' => $snippet
    ];
    $options = ['http' => [
      'method'  => 'POST',
      'header'  => 'Content-type: application/json',
      'content' => json_encode($data)
    ]];

    $context = stream_context_create($options);
    $result = file_get_contents($this->civinkyUrl, false, $context);

    return $result;

  }
}
