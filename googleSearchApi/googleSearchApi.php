<? 

class googleSearchApi{
  
  protected $apiKey;
  protected $searchId;
  protected $apiUrl = 'https://www.googleapis.com/customsearch/v1';
  protected $params = array();
  protected $rawResponse = null;
  protected $response = array(
    'raw' => null,
    'json' => null
  );
  
  protected $settings = array(
    'safeSearch' => 'medium'  // off, medium, high
  );
  
  public $lastError = '';
  public $totalResults = 0;
  
  function __construct($apiKey, $searchId){
    $this->apiKey = $apiKey;
    $this->searchId = $searchId;
  }
  
  public function rawOptions($options){
    
    if( !is_array($options) ){
      throw new Exception('"options" argument must be an associative array');
    }
    
    $this->params = array_merge($this->params, $options);
    return $this;
  }
  
  public function safeSearch($mode){
    
    $allowedTypes = ['off', 'medium', 'high'];
    
    if( !in_array($mode, $allowedTypes) ){
      throw new Exception('Incorrect "mode" given: ' . $mode . '. Allowed mode types: ' . implode(',', $allowedTypes));
    }
    
    $this->settings['safeSearch'] = $mode;
    
    return $this;
  }
  
  public function get(){
    
    $this->request();
    
    if( $this->total() === 0){
      return array();
    }
    
    return $this->items();
  }
  
  public function page($num){
    $this->params['start'] = ($num - 1) * 10 + 1;
    return $this;    
  }
  
  public function searchImages($query){
    
    $this->query($query)->imagesOnly()->request();
    
    if( $this->total() === 0 ){
      return array();
    }
    
    return $this->items();
  }
  
  public function total(){
    
    if( $this->responseIsEmpty() ){
      return null;
    }
    
    return intval( $this->response['json']->searchInformation->totalResults );
  }
  
  public function items(){
    
    if( $this->responseIsEmpty() ){
      return null;
    }
    
    return $this->response['json']->items;    
  }
  
  public function raw(){
    return $this->response['raw'];
  }
  
  public function imagesOnly(){
    $this->params['searchType'] = 'image';
    return $this;
  }
  
  // @code: code like NL, RU, US etc
  public function country($code){
     
    if( mb_strlen($code, 'UTF-8') > 2 ){
      throw new Exception('Country code must be like: NL, US, RU. Given value: ' . $code);
    } 
     
    $codeFromatted = mb_strtoupper($code, 'UTF-8');
    $this->params['cr'] = 'country' . $codeFromatted;
    return $this;
  } 
  
  public function query($query){
    $this->params['q'] = $query;
    return $this;
  }
  
  protected function responseIsEmpty(){
    return $this->response['raw'] === null;
  }
  
  protected function buildUrl(){
    
    $apiParams = array(
      'key' => $this->apiKey,
      'cx' => $this->searchId,
      'safe' => $this->settings['safeSearch']
    );
    
    $allParams = array_merge($apiParams, $this->params);
    
    $url = $this->apiUrl . '?' . http_build_query($allParams);
    
    return $url;
  }
  
  protected function saveResponse($response){
    $this->response['raw'] = $response;
    $this->response['json'] = json_decode($response);
    return $this;
  }
  
  protected function resetResponse(){
    array_walk($this->response, function(&$item, $key){
      $item = null;
    });    
    return $this;
  }
  
  protected function request(){
    
    $this->resetResponse();
    
    $url = $this->buildUrl();
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_HEADER,0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_URL, $url);
    $response = curl_exec($curl);
    
    if(!$response){
      $this->lastError = 'Connection error';
      return $this;
    }
    
    $this->saveResponse($response);
        
    return $this;
  }  
  
}

?>