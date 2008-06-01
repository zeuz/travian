<?PHP
if(!function_exists('http_build_query')) {
    function http_build_query( $formdata, $numeric_prefix = null, $key = null ) {
        $res = array();
        foreach ((array)$formdata as $k=>$v) {
            $tmp_key = urlencode(is_int($k) ? $numeric_prefix.$k : $k);
            if ($key) $tmp_key = $key.'['.$tmp_key.']';
            if ( is_array($v) || is_object($v) ) {
                $res[] = http_build_query($v, null /* or $numeric_prefix if you want to add numeric_prefix to all indexes in array*/, $tmp_key);
            } else {
                $res[] = $tmp_key."=".urlencode($v);
            }
            /*
            If you want, you can write this as one string:
            $res[] = ( ( is_array($v) || is_object($v) ) ? http_build_query($v, null, $tmp_key) : $tmp_key."=".urlencode($v) );
            */
       }
       $separator = ini_get('arg_separator.output');
       return implode($separator, $res);
   }
}

class Curl {
  var $timeout= '500';  
  var $proxy  = '127.0.0.1:8118';
  var $cookie_file = 'curl_cookie.txt';
  var $headers = array();
  var $options = array();
  var $referer = '';
  var $user_agent = '';
  
  # Protected
  var $error = '';
  var $handle;
  var $cookies;
  function Curl() {
  // $this->user_agent = $_SERVER['HTTP_USER_AGENT'];
   $ua = array('Mozilla','Opera','Microsoft Internet Explorer','ia_archiver');
   $op = array('Windows','Windows XP','Linux','Windows NT','Windows 2000','OSX');
   $this->user_agent  = $ua[rand(0,3)].'/'.rand(1,8).'.'.rand(0,9).' ('.$op[rand(0,5)].' '.rand(1,7).'.'.rand(0,9).'; en-US;)'; 

 }
 
  function delete($url, $vars = array()) {
    return $this->request('DELETE', $url, $vars);
  }
 
  function error() {
    return $this->error;
  }
 
  function get($url, $vars = array()) {
    if (!empty($vars)) {
      $url .= (stripos($url, '?') !== false) ? '&' : '?';
      $url .= http_build_query($vars);
    }
    return $this->request('GET', $url);
  }
 
  function post($url, $vars = array()) {
    return $this->request('POST', $url, $vars);
  }
 
  function put($url, $vars = array()) {
    return $this->request('PUT', $url, $vars);
  }
 
  # Protected
  function request($method, $url, $vars = array()) {
    $this->handle = curl_init();
    
    # Set some default CURL options
    curl_setopt ($this->handle, CURLOPT_TIMEOUT, $this->timeout);
    curl_setopt($this->handle, CURLOPT_PROXY, $this->proxy); 
    //para este caso no se necesita cookie_file
//    curl_setopt($this->handle, CURLOPT_COOKIEFILE, $this->cookie_file);
//    curl_setopt($this->handle, CURLOPT_COOKIEJAR, $this->cookie_file);
    curl_setopt($this->handle, CURLOPT_FOLLOWLOCATION,false);// true);
    curl_setopt($this->handle, CURLOPT_HEADER, true);
    curl_setopt($this->handle, CURLOPT_POSTFIELDS, http_build_query($vars));
    curl_setopt($this->handle, CURLOPT_REFERER, $this->referer);
    curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->handle, CURLOPT_URL, $url);
    curl_setopt($this->handle, CURLOPT_USERAGENT, $this->user_agent);
    
    # Format custom headers for this request and set CURL option
    $headers = array();
    foreach ($this->headers as $key => $value) {
      $headers[] = $key.': '.$value;
    }
    curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
    $post=0;
    # Determine the request method and set the correct CURL option
    switch ($method) {
      case 'GET':
        curl_setopt($this->handle, CURLOPT_COOKIE,  $this->cookies);
        curl_setopt($this->handle, CURLOPT_HTTPGET, true);
        break;
      case 'POST':
          $post=1;
        curl_setopt($this->handle, CURLOPT_POST, true);
        break;
      default:
        curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $method);
    }
    
    # Set any custom CURL options
    foreach ($this->options as $option => $value) {
      curl_setopt($this->handle, constant('CURLOPT_'.str_replace('CURLOPT_', '', strtoupper($option))), $value);
    }
    
    $response = curl_exec($this->handle);
   
    if ($response) {
       //nofurula bien en php 4
      //$response = new CurlResponse($response);
        if($post){ //parche feo para cookies en el get 
        preg_match_all('|Set-Cookie: (.*);|U', $response, $results);    
        $this->cookies = implode(';', $results[1]);
          }
        $pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';
        $response= preg_replace($pattern, '', $response);
     
    } else {
      $this->error = curl_errno($this->handle).' - '.curl_error($this->handle);
    }
    curl_close($this->handle);
    return $response;
  }
 
}
 
class CurlResponse {
  var $body = '';
  var $headers = array();
 
  function __construct($response) {
//  function CurlRespose($response){ 
   # Extract headers from response
    $pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';
    preg_match_all($pattern, $response, $matches);
    $headers = split("\r\n", str_replace("\r\n\r\n", '', array_pop($matches[0])));
    
    # Extract the version and status from the first header
    $version_and_status = array_shift($headers);
    preg_match('#HTTP/(\d\.\d)\s(\d\d\d)\s(.*)#', $version_and_status, $matches);
    $this->headers['Http-Version'] = $matches[1];
    $this->headers['Status-Code'] = $matches[2];
    $this->headers['Status'] = $matches[2].' '.$matches[3];
    
    # Convert headers into an associative array
    foreach ($headers as $header) {
      preg_match('#(.*?)\:\s(.*)#', $header, $matches);
      $this->headers[$matches[1]] = $matches[2];
    }
    
    # Remove the headers from the response body
    $this->body = preg_replace($pattern, '', $response);
    echo "$this->body";  
}
 
  function __toString() {
    return $this->body;
  }
 
}
?>