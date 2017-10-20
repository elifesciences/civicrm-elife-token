<?php

require_once __DIR__.'/../../../../CRM/ElifeToken/Token/ArticlesLast7Days.php';

class CRM_ElifeToken_Token_ArticlesLast7DaysTest extends PHPUnit_Framework_TestCase {
  public function setUp() {
    $this->token = new CRM_ElifeToken_Token_ArticlesLast7Days();
    $this->magazineTypes = ['editorial', 'feature', 'insight'];
  }

  /**
   * @test
   */
  public function smokeTestThePoaContent() {
    $content = $this->token->getArticles('poa');
    $this->checkDataStructure($content);
    foreach ($content['items'] as $item) {
      $this->assertArrayHasKey('status', $item);
      $this->assertSame('poa', $item['status']);
    }
  }

  /**
   * @test
   */
  public function smokeTestTheVorContent() {
    $content = $this->token->getArticles('vor');
    $this->checkDataStructure($content);
    foreach ($content['items'] as $item) {
      $this->assertSame('vor', $item['status']);
      $this->assertNotContains($item['type'], $this->magazineTypes, "No magazine types should be duplicated in VOR");
    }
  }

  /**
   * @test
   */
  public function smokeTestTheMagazineContent() {
    $content = $this->token->getArticles('magazine');
    $this->checkDataStructure($content);
    foreach ($content['items'] as $item) {
      $this->assertArrayHasKey('status', $item);
      $this->assertSame('vor', $item['status']);
      $this->assertContains($item['type'], $this->magazineTypes);
    }
  }

  private function checkDataStructure($content) {
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
    }
  }
}
