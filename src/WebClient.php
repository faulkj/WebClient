<?php namespace FaulkJ\WebClient;
   /*
    *WebClient Class v1.0
    *
    * Kopimi 2021 Joshua Faulkenberry
    * Dual licensed under the MIT and GPL licenses.
    */

   class WebClient {

      const     version    = "1.0";

      protected $protocol  = "https";
      protected $domain    = null;
      protected $host      = null;
      protected $user      = null;
      protected $password  = null;
      protected $debug     = false;

      protected $startTime;
      protected $request;
      protected $response;

      public function __construct($host, $user = null, $password = null, $domain = null) {
         if(strpos($host, "://") !== false) list($this->protocol, $this->host) = explode("://", $host);
         else $this->host = $host;
         $this->user = $user;
         $this->password = $password;
         $this->domain = $domain;

      }

      public function debug($dbg) {
         if(isset($dbg)) $this->debug = $dbg != false;
         else return $this->debug;
         return $this;
      }

      public function request($params = null) {
         $mil = explode(".", strval(microtime(true)));
         $mil = count($mil) > 1 ? substr($mil[1], 0, 3) : "000";
         $this->startTime = date("Y-m-d H:i:s.$mil");

         if($this->debug && !headers_sent()) header("Content-Type: text/plain");
         $params = (object) $params;

         $url = $this->protocol . "://";
         if(isset($params->target)) {
            $url .= str_replace("//", "/", "{$this->host}/{$params->target}");
            unset($params->target);
         }
         else $url .= $this->host;
         if(isset($params->qs)) {
            $url .= "?{$params->qs}";
            unset($params->qs);
         }
         $url = str_replace(" ", "%20", $url);

         if($this->user) $params->credentials = $this->user;
         if($this->password) $params->credentials .= ":{$this->password}";
         if(is_string($this->domain)) $params->credentials = "{$this->domain}\\{$params->credentials}";

         $this->request = new WebRequest($url, $params, $this->debug);
         $this->response = $this->request->submit();

         if($this->response->code != 200) $this->error();

         return $this->response;
      }

      protected function error() {
         //throw new Exception("WebClient Error: Response Code {$this->response->code}", E_USER_WARNING);
      }

   }
?>