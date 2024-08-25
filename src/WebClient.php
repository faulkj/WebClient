<?php namespace FaulkJ;
/*
 * WebClient Class v1.1
 *
 * Kopimi 2024 Joshua Faulkenberry
 * Unlicensed under The Unlicense
 * http://unlicense.org/
 */

 use
    FaulkJ\WebClient\WebRequest,
    FaulkJ\WebClient\WebResponse;

class WebClient {

   const     version    = "1.1";

   protected $protocol  = "https";
   protected $debug     = false;
   protected $domain    = null;
   protected $host      = null;
   protected $user      = null;
   protected $password  = null;

   protected $startTime;
   protected $request;
   protected $response;

   public function __construct(string $host, ?string $user = null, ?string $password = null, ?string $domain = null) {
      if (strpos($host, "://") !== false) list($this->protocol, $this->host) = explode("://", $host);
      else $this->host = $host;
      $this->user = $user;
      $this->password = $password;
      $this->domain = $domain;
   }

   public function debug(?bool $dbg = null): bool|self {
      if ($dbg !== null) {
         $this->debug = $dbg != false;
         return $this;
      }
      return $this->debug;
   }

   public function request(object|array $params = null): WebResponse {
      $mil = explode(".", strval(microtime(true)));
      $mil = count($mil) > 1 ? substr($mil[1], 0, 3) : "000";
      $this->startTime = date("Y-m-d H:i:s.$mil");

      if ($this->debug && !headers_sent()) header("Content-Type: text/plain");
      $params = (object) $params;

      $url = $this->protocol . "://";
      if (isset($params->target)) {
         $url .= str_replace("//", "/", "{$this->host}/{$params->target}");
         unset($params->target);
      } else $url .= $this->host;
      if (isset($params->qs)) {
         $url .= "?{$params->qs}";
         unset($params->qs);
      }
      $url = str_replace(" ", "%20", $url);

      if ($this->user) $params->credentials = $this->user;
      if ($this->password) $params->credentials .= ":{$this->password}";
      if (is_string($this->domain)) $params->credentials = "{$this->domain}\\{$params->credentials}";

      $this->request = new WebRequest($url, $params, $this->debug);
      $this->response = $this->request->submit();

      return $this->response;
   }

   protected function error() {}
}
