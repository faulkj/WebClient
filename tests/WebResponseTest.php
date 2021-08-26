<?php
use PHPUnit\Framework\TestCase,
    FaulkJ\WebClient\WebResponse;

class WebResponseTest extends TestCase {

   public function testIsThereAnySyntaxError() {
      $var = new WebResponse(200, ["Header"=>"header"], "This is a test");
      $this->assertTrue(is_object($var));
      unset($var);
   }

   public function testResponse() {
      $var = new WebResponse(200, ["Header"=>"header"], "This is a test");
      $this->assertTrue($var->code == 200);
      unset($var);
   }

}