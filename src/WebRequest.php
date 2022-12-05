<?php namespace FaulkJ\WebClient;
   /*
    *WebRequest Class v1.1
    *
    * Kopimi 2022 Joshua Faulkenberry
    * Unlicensed under The Unlicense
    * http://unlicense.org/
    */

   class WebRequest {

      private $url;
      private $credentials;
      private $method;
      private $accept;
      private $data;
      private $type;
      private $headers;
      private $debug;

      public function __construct($url, $params = null, $debug = false) {
         $this->url         = $url;
         $this->credentials = isset($params->credentials) ? $params->credentials: null;
         $this->method      = isset($params->method)  ? strtoupper($params->method) : "GET";
         $this->accept      = isset($params->accept)  ? $params->accept : "application/json";
         $this->data        = isset($params->data)    ? $params->data   : null;
         $this->type        = isset($params->type)    ? $params->type   : null;
         $this->headers     = isset($params->headers) ? (array) $params->headers : [];
         $this->debug       = $debug != false;
      }

      public function submit() {
         $headers = [
            "Accept: {$this->accept}"
         ];
         if($this->type) array_push($this->headers, "Content-Type: " . $this->type);

         if($this->debug) {
            echo(date("H:i:s") .  " Submitting {$this->method} Request...\n");
            echo("         URL: {$this->url}\n\n");
         }
         $start = explode(" ", microtime());
         $headers = array_merge($headers, $this->headers);

         $ch = curl_init($this->url);
         curl_setopt($ch, CURLOPT_HEADER, 1);
         curl_setopt($ch, CURLOPT_VERBOSE, false);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
         if($this->credentials) curl_setopt($ch, CURLOPT_USERPWD, $this->credentials);
         if(($this->method == "POST" || $this->method == "PUT")) {
            if($this->data) {
               if($this->debug) {
                  echo("POST Data:\n\n");
                  print_r($this->data);
                  echo("\n\n");
               }
               if(!$this->type) $this->type = "";
               if(strpos($this->type, "application/json") !== false) {
                  $data = is_string($this->data) || is_numeric($this->data) ? $this->data : json_encode($this->data);
                  array_push($headers, 'Content-Length: ' . strlen($data));
                  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
               }
               elseif($this->type && strpos($this->type, "application/xml") !== false) {
                  array_push($headers, 'Content-Length: ' . strlen($this->data));
                  curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
               }
               else{
                  if(!is_array($this->data)) parse_str($this->data, $data);
                  else $data = $this->data;
                  curl_setopt($ch, CURLOPT_POST, count($data));
                  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
               }
            }
            else array_push($headers, 'Content-Length: 0');
         }
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         if($this->debug) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
         }

         $response = curl_exec($ch);
         $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
         $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

         if(curl_errno($ch)) {
            if($this->debug) echo(date("H:i:s") . ' Curl error: ' . curl_error($ch) . "\n\n");
            return new WebResponse(
               strpos(curl_error($ch), "Timed out") !== false ? 408 : (intval($code) ? intval($code) : 0),
               [],
               'Curl error: ' . curl_error($ch)
            );
         }

         curl_close($ch);

         if($this->debug) {
            $end = explode(" ", microtime());
            $time = number_format(((float)$end[0] + (float)$end[1]) - ((float)$start[0] + (float)$start[1]), 2);
            echo(date("H:i:s") .  " Response returned in $time seconds.\n\n");

            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);
            echo date("H:i:s") .  " Request Details:\n\n$verboseLog\n\n" . date("H:i:s") .  " Response Body:\n\n" . substr($response, $header_size). "\n\n";
         }

         $hdrs = [];
         foreach(explode("\n", substr($response, 0, $header_size)) as $hdr) {
            $hdr = array_map('trim', explode(':', $hdr, 2));
            if(count($hdr) > 1) $hdrs[$hdr[0]] = $hdr[1];
         }

         return new WebResponse(
            $code,
            $hdrs,
            substr($response, $header_size)
         );
      }

   }
?>