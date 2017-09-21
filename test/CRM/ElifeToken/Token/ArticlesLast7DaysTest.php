<?php

require_once __DIR__.'/../../../../CRM/ElifeToken/Token/ArticlesLast7Days.php';

class CRM_ElifeToken_Token_ArticlesLast7DaysTest extends PHPUnit_Framework_TestCase {
  public static function possibleTypes() {
    return [
        // not sure these are the right inputs, but they would 
        // be a good naming convention
        ['poa'],
        ['vor'],
    ];
  }
  /**
   * @dataProvider possibleTypes
   * @test
   */
  public function smokeTestTheContentFilteredFor($type) {
    $token = new CRM_ElifeToken_Token_ArticlesLast7Days();
    $content = $token->getArticles($type);
    $this->assertInternalType('array', $content);
    $this->assertArrayHasKey('total', $content);
    $this->assertInternalType('integer', $content['total']);
    $this->assertGreaterThan(0, $content['total']);
    $this->assertArrayHasKey('items', $content);
    $items = $content['items'];
    $this->assertInternalType('array', $items);
    foreach ($items as $item) {
        $this->assertInternalType('array', $item);
        $this->assertArrayHasKey('status', $item);
        $this->assertSame($type, $item['status']);
    }
  } 
}
