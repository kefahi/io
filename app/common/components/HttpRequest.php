<?php
namespace common\components;

class HttpRequest {
  private $host;
  private $port;
  private $connection_timeout = 30; #seconds
  private $streaming_timeout  = 60; #seconds

  public function __construct($host, $port=80, $connection_timeout=30, $streaming_timeout=60) {
    $this->host = $host;
    $this->port = $port;
    $this->connection_timeout = $connection_timeout;
    $this->streaming_timeout = $streaming_timeout;
  }

  public function get($path) { return $this->call('GET', $path); }
  public function post($path, $data) { return $this->call('POST', $path, $data); }
  public function put($path, $data) { return $this->call('PUT', $path, $data); }
  public function delete($path) { return $this->call('DELETE', $path); }

  private function call($verb, $path, $data=null) {
    $length  = mb_strlen($data, '8bit');
    $request_headers = "$verb $path HTTP/1.1\r\n".
      "Host: {$this->host}\r\n".
      "Content-Type: application/x-www-form-urlencoded\r\n".
      "Connection: close\r\n".
      "Content-Length: $length\r\n\r\n";

    if ($data) {
      $request_headers .= $data;
    }

    $fp = @fsockopen($this->host, $this->port, $errno, $errstr, $this->connection_timeout);
    if (!$fp) {
      throw new Exception("Error while connecting #$errno $errstr");
    }
    stream_set_timeout($fp, $this->streaming_timeout);  #Timeout to pull content

    $response = '';
    fputs($fp, $request_headers);
    while (!@feof($fp)) {
      $response .= @fgets($fp, 128);
    }

    @fclose($fp);

    list($headers, $body) = explode("\r\n\r\n", $response, 2);
    $headers = self::parseResponseHeaders($headers);

    if ($headers['status_code'] == 301 || $headers['status_code'] == 302) {
      return $this->call($verb, $headers['Location'], $data);
    }

    if (!isset($headers['status_code']) || $headers['status_code'] != 200) {
      throw new \Exception("Bad response code {$headers['status_code']} response: $response");
    }

    if (isset($headers['Transfer-Encoding']) && $headers['Transfer-Encoding'] == 'chunked') {
      $real_body = '';
      $position  = 0;
      $body_length = strlen($body);

      while ($position < $body_length) {
        // find next \r\n
        $line_terminator = strpos($body, "\r", $position);

        // from position upto \r is the length of the chunk in hexa
        $chunk_length = substr($body, $position, $line_terminator - $position);
        $chunk_length = hexdec($chunk_length);

        // move cursor to beginning of chunk
        $position     = $line_terminator + 2;

        // read the data, and move cursor to end of \r\n
        $real_body   .= substr($body, $position, $chunk_length);
        $position    += $chunk_length + 2;
      }

      $body = $real_body;
    } else {
      $body_length = mb_strlen($body, '8bit');
      if (isset($headers['Content-Length']) && $headers['Content-Length'] != $body_length) {
        throw new Exception("Content-Length miss match {$headers['Content-Length']} vs received $body_length");
      }
    }

    return $body;
  }

  // TODO: does not take into account responses that contain the same header multiple times
  // Set-Cookie is one such example
  private static function parseResponseHeaders($headers) {
    $response_headers = array();

    list($status, $headers) = explode("\r\n", $headers, 2);
    if (preg_match('/^HTTP\/1.1 (\d+) /', $status, $matches)) {
      $response_headers['status_code'] = $matches[1];
    }

    foreach (explode("\r\n", $headers) as $header) {
      list($field, $value) = explode(':', $header, 2);
      $response_headers[$field] = trim($value);
    }

    return $response_headers;
  }

  public static function selfTest() {
    $http_request = new HttpRequest('maqola.org');
    echo $http_request->get('/');
  }
}
