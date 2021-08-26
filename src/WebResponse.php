<?php namespace FaulkJ\WebClient;
   /*
    *WebResponse Class v1.0
    *
    * Kopimi 2021 Joshua Faulkenberry
    * Unlicensed under The Unlicense
    * http://unlicense.org/
    */

   class WebResponse {

      private $code    = null;
      private $headers = array();
      private $body    = null;

      public function __construct($code, array $headers, $body) {
         $this->code    = $code;
         $this->headers = $headers;
         if($body) $this->body = $body;
      }

      public function __get($prop) {
         if(property_exists($this, $prop)) return $this->$prop;
         trigger_error("'$prop' does not exist");
      }

      public function __set($item, $val) {
         trigger_error("Can't modify a response");
      }

   }
?>