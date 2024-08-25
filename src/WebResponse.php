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

   public function __construct(int $code, array $headers, string $body) {
      $this->code    = $code;
      $this->headers = $headers;
      if ($body) $this->body = $body;
   }

   public function __get(string $prop) {
      if (property_exists($this, $prop)) return $this->$prop;
      throw new Exception("'$prop' does not exist");
   }

   public function __set(String $item, $val) {
      throw new Exception("Can't modify a response");
   }
}
