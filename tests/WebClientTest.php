<?php
use PHPUnit\Framework\TestCase,
FaulkJ\WebClient\WebClient;

class WebClientTest extends TestCase {

   public function testIsThereAnySyntaxError() {
      $var = new WebClient("https://github.com/");
      $this->assertTrue(is_object($var));
      unset($var);
   }

   public function testRequest() {
      $var = new WebClient("https://github.com/");
      $this->assertTrue($var->debug(false)->request()->code > 1);
      unset($var);
   }

}