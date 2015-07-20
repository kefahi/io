<?php
namespace common\components;
use \yii\base\Component;
class Elastic extends Component {
	public $index='edraj';
  public $type='entry';
	public $host = '127.0.0.1';
	public $port = 9200;
	private $http_request;

	public function init() {
		$this->http_request = new HttpRequest($this->host, $this->port);
	}

	public static function buildMapping($attributes_mapping) {
		$mapping = array();
		foreach($attributes_mapping as $key => $value) {
			if(isset($value['type']) && is_string($value['type']) && $value['type'] == 'array_all') continue;
			if(isset($value['type']) && is_string($value['type']) && $value['type'] != 'array_all') {
				$mapping[$key] = $value;
			} else{ // This is an array lets call-self
				$mapping[$key] = array('properties' => self::buildMapping($value));
      }
		}
		return $mapping;
	}

  protected static function check($response) {
//		if(empty($response)) throw new \Exception("No response from server");

		if(preg_match('/"error[s]?":true/',$response,$match)) 
			throw new \Exception($response);
	}

	public static function is_associative ($arr) { $a = array_keys($arr); return ($a !== array_keys($a)); }

	public static function mapData($mapping, $doc) {
		$casted_data = array();
		foreach ($mapping as $key => $type) {
			if (!isset($doc[$key]) || (empty($doc[$key]) && !is_bool($doc[$key]) && $doc[$key] !== '0' && $doc[$key] !== 0)) continue;
      if(isset($type['type']) && is_string($type['type'])) $type = $type['type']; // Detect the map end point and just consider its type here.
			if (is_array($type)) {
				if(self::is_associative($doc[$key])) {
					$value = self::mapData($type, $doc[$key]);
				} else {
					$value = array();
					foreach($doc[$key] as $inner_value) {
						$value [] = self::mapData($type, $inner_value);
					}
				}
			} elseif ($type == 'date') {
				$value = $doc[$key];
				$type = 'string';
			}elseif($type == 'array_all'){
          $value = self::array_all($doc[$key]);
				}else{
				$value = $doc[$key];
			}

			if(!is_array($doc[$key]) && !is_array($type))
				setType($doc[$key], $type);
			$casted_data[$key] = $value;
		}

		return $casted_data;
	}
  static public function array_all($doc){
  	$value = array();
    foreach($doc as $k1 => $v1){
      if(!is_array($v1)){
      	setType($doc[$k1], 'string');
      	$value[$k1] = $doc[$k1];
      } else {
       	$value[$k1] = self::array_all($v1);
      }
    }
    return $value;
  }
	public function delete($type) {
    $url = "/{$this->index}/";
    if(isset($type)) $url .= "$type/_mapping";
		$response = $this->http_request->delete($url);
    self::check($response);
	}

	public function createIndex() {
		$request = '{"settings":{"number_of_shards":1,"number_of_replicas":0}}';
		$response = $this->http_request->put("/{$this->index}", $request);
    self::check($response);
	}

	public function map($type, $mapping) {
		$mapping_data = array($type=>array('dynamic'=>'strict', 'properties'=>self::buildMapping($mapping)));
		$response = $this->http_request->put("/{$this->index}/{$type}/_mapping", json_encode($mapping_data));
    self::check($response);
	}

	public function search($type, $term) {
		$query = '{"query":{"text":{"_all":"TERM"}}}';
		$request = str_replace('TERM', $term, $query);
		$response = $this->http_request->post("/{$this->index}/{$type}/_search", $request);
    self::check($response);
		return $response;
	}
  public function update($type, $id, $doc){  
    $request = '{"doc" : '.$doc.'}';
  	$response = $this->http_request->post("/{$this->index}/{$type}/{$id}/_update", $request);
  	self::check($response);
  	return $response;
  }
 
  public function insert($type, $document, $mapping){
      $bulk = '';
      $docs = array();
      $data = self::mapData($mapping,$document);
      $data['_id'] = $document['_id'];
      $docs [] = $data;
      $this->import($type, $docs);


   }

  public function insertDoc($type, $document, $mapping){
  	$bulk = '';
  	$insert_doc = array();
  	foreach($doc as $key => $value){
  		if (is_array($value)) {
				if(self::is_associative($doc[$key])) {
					foreach($doc[$key]  as $k1 => $v1){
						$data_type = $this->checkmap($mapping, $key, $k1);
						if($data_type == 'date'){
		  	    	$insert_doc[$key][$k1] = date('c', strtotime($doc[$key][$k1]));
		      	}else{
		  	      setType($doc[$key][$k1], $data_type);
		  	      $insert_doc[$key][$k1] = $doc[$key][$k1];
		        }
					}
				} else {
					$value = array();
					$data_type = $this->checkmap($mapping, $key);
					foreach($doc[$key] as $inner_value) {
					   setType($inner_value, $data_type);
					   $value [] = $inner_value;
					}
					$insert_doc[$key] = $value;
				}
			}else{
		  	$data_type = $this->checkmap($mapping, $key);
		  	if($data_type == 'date'){
		  		$insert_doc[$key] = date('c', strtotime($doc[$key]));
		  	}else{
		  	  setType($doc[$key], $data_type);
		  	  $insert_doc[$key] = $doc[$key];
		    }
	  	}
  	}
  	$id = $doc['_id'];
  	$bulk .= "{\"index\":{\"_id\":\"{$id}\"}}" . PHP_EOL . json_encode($doc) . PHP_EOL;
		$response = $this->http_request->put("/{$this->index}/{$type}/_bulk", $bulk);
    self::check($response);
  }

  public function CheckMap($mapping, $key, $key2=null){

    if(isset($key2)){
	    if( isset( $mapping[$key][$key2] ) ){
	      $return_type = $mapping[$key][$key2]['type'];
	    }else {
	  		$return_type = 'string';
	  	}
	  }else{
	  	if( isset( $mapping[$key] ) ){
	      $return_type = $mapping[$key]['type'];
	    }else {
	  		$return_type = 'string';
	  	}	
	  }
	  echo "KEY1 : $key , KEY2: $key2 , RETURN: $return_type  \n";
  	return $return_type;
  }

  public function import($type, $docs) {
    $bulk = '';
		foreach($docs as $doc) {
      $id = $doc['_id'];
      unset($doc['_id']);
			$bulk .= "{\"index\":{\"_id\":\"{$id}\"}}" . PHP_EOL . json_encode($doc) . PHP_EOL;
		}
		$response = $this->http_request->put("/{$this->index}/{$type}/_bulk", $bulk);
    self::check($response);
	}
}
