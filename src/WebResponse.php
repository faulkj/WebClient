<?php

namespace FaulkJ\WebClient;
/*
 * WebResponse Class v1.1
 *
 * Kopimi 2024 Joshua Faulkenberry
 * Unlicensed under The Unlicense
 * http://unlicense.org/
 */

use \Exception;

class WebResponse {

   private $code    = null;
   private $headers = [];
   private $body    = null;

   public function __construct($code, array $headers, $body) {
      $this->code    = $code;
      $this->headers = $headers;
      if ($body) $this->body = $body;
   }

   public function __get($prop) {
      if (property_exists($this, $prop)) return $this->$prop;
      throw new Exception("'$prop' does not exist");
   }

   public function __set($item, $val) {
      throw new Exception("Can't modify a response");
   }
}
