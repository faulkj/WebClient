<?php
use PHPUnit\Framework\TestCase,
    FaulkJ\WebClient\WebRequest;

class WebRequestTest extends TestCase {

   public function testIsThereAnySyntaxError() {
      $var = new WebRequest("https://github.com/");
      $this->assertTrue(is_object($var));
      unset($var);
   }

   public function testRequest() {
      $var = new WebRequest("https://github.com/");
      $this->assertTrue($var->submit()->code > 1);
      unset($var);
   }

}