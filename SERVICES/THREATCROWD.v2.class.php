<?php
/**
 * THREATCROWD2_API
 * PHP Wrapper for https://github.com/threatcrowd/ApiV2 
 *
 * @author	Jason Medland<jason.medland@gmail.com>
 * @package	JCORE\SERVICE\WRAPPER
 * 
 */

namespace JCORE\SERVICE\WRAPPER;
use JCORE\TRANSPORT\SOA\SOA_BASE as SOA_BASE;


/**
 * class THREATCROWD2_API
 * Simply comment out SOA_BASE if you are not using this 
 * in the just-core framework
 * 
 *
 * @package JCORE\SERVICE\WRAPPER
*/
class THREATCROWD2_API extends SOA_BASE{
	/**
	* serviceRequest
	* 
	* @access protected 
	* @var string
	*/
	protected $serviceRequest = null;
	/**
	* serviceResponse
	* 
	* @access public 
	* @var string
	*/
	public $serviceResponse = null;
	/**
	* error
	* 
	* @access public 
	* @var string
	*/
	public $error = null;
	
	/**
	* END_POINT
	* 
	* @access public 
	* @var string
	*/
	public $END_POINT = 'https://www.threatcrowd.org/';
	/**
	* SEARCH
	* 
	* @access public 
	* @var string
	*/
	public $SEARCH = 'searchApi/v2/';
	/**
	* VOTE
	* 
	* @access public 
	* @var string
	*/
	public $VOTE = 'vote.php?';
	/**
	* requestURL
	* 
	* @access public 
	* @var string
	*/
	public $requestURL = '';
	
	/**
	* TYPES
	* 
	* @access public 
	* @var string
	*/
	public $TYPES = array(
		'email' => 'email',
		'domain' => 'domain',
		'ip' => 'ip',
		'antivirus' => 'antivirus',
		'file' => 'resource',
	);
	/**
	* extensions
	* 
	* @access protected 
	* @var array
	*/
	protected $extensions  = array(
		'.js'   => 'Content-type: text/javascript',
		'.json' =>  'Content-type: application/json',
		'.flexigrid' => 'Content-type: text/plain',
		'.cookie' => 'Content-type: text/html',
		'.xml'  => 'Content-type: text/xml',
		'.txt'  => 'Content-type: text/plain',
		'.html' => 'Content-type: text/html',
		'.php'  => 'Content-type: text/html',
		'.pdf'  => 'Content-type: application/pdf',
		'.gif'  => 'Content-type: image/gif',
		'.jpg'  => 'Content-type: image/jpeg',
		'.png'  => 'Content-type: image/png'
	);
	/**
	* DESCRIPTOR: an empty constructor, the service MUST be called with 
	* the service name and the service method name specified in the 
	* in the method property of the JSONRPC request in this format
	* 		""method":"AJAX_STUB.aServiceMethod"
	* 
	* @param null 
	* @return null  
	*/
	public function __construct(){
		if(isset($mimeType)){
			$this->mimeType = $mimeType;
		}else{
			$this->mimeType = $this->extensions[".json"];
		}
		return;
	}
	/**
	* DESCRIPTOR: init
	* 
	* @access public 
	* @param array args
	* @return null
	*/
	public function init($args){
		#echo __METHOD__.__LINE__.'$args<pre>['.var_export($args, true).']</pre>'.'<br>'; 
		return;
	}

	/**
	* DESCRIPTOR:getReport
	* get a report from the threat crowd API
	* params is an array containing 
	* 	TYPE from the this->TYPES array 
	* 		email, domain, ip, antivirus, file
	* 	and 
	* 	VALUE  the actual search value
	* 
	* @access public 
	* @param array params
	* @return bool
	*/
	public function getReport($params = null){
		if(!isset($params["TYPE"]) || !isset($this->TYPES[strtolower($params["TYPE"])])){
			return $params;
		}
		if(!isset($params["VALUE"])){
			return $params;
		}
		
		$reportURI = '/report/?';
		/*
		*/
		switch(strtolower($params["TYPE"])){//authType
			case "file":
				$this->requestURL = $this->END_POINT.$this->SEARCH.strtolower($params["TYPE"]).$reportURI.$this->$TYPES[strtolower($params["TYPE"])].'='.$params["VALUE"];
				break;
			default:
				$this->requestURL = $this->END_POINT.$this->SEARCH.strtolower($params["TYPE"]).$reportURI.strtolower($params["TYPE"]).'='.$params["VALUE"];
				break;
		}
		$this->serviceResponse = $this->makeCall();

		if(isset($this->serviceResponse) ){ ///&& 'OK' == $this->serviceResponse["status"]
			return $this->serviceResponse;
		}
		return false;
	}
	
	
	
	
	/**
	* DESCRIPTOR: up vote 
	* https://github.com/threatcrowd/ApiV2#votes
	* 
	* 
	* @access public 
	* @param array params
	* @return bool
	*/
	public function upVote($value=null){
		if(null === $value){
			return false;
		}
		$requestURI = 'vote=1&value='.$value;
		$this->requestURL = $this->END_POINT.$this->VOTE.$requestURI;
		$this->serviceResponse = $this->makeCall();
		if(isset($this->serviceResponse) ){
			return $this->serviceResponse;
		}
		return false;
	}
	/**
	* DESCRIPTOR: down vote
	* https://github.com/threatcrowd/ApiV2#votes
	* 
	* 
	* @access public 
	* @param array params
	* @return bool
	*/
	public function downVote($value=null){
		if(null === $value){
			return false;
		}
		$requestURI = 'vote=0&value='.$value;
		$this->requestURL = $this->END_POINT.$this->VOTE.$requestURI;
		echo __METHOD__.__LINE__.'$this->requestURL<pre>['.var_export($this->requestURL, true).']</pre>'.'<br>'; 
		$this->serviceResponse = $this->makeCall();
		echo __METHOD__.__LINE__.'$this->serviceResponse<pre>['.var_export($this->serviceResponse, true).']</pre>'.'<br>'; 

		if(isset($this->serviceResponse) ){ /// isset($this->serviceResponse["response_code"]) && 1 == $this->serviceResponse["response_code"])
			return $this->serviceResponse;
		}
		return false;
	}
	
	
	/**
	* DESCRIPTOR: make Curl Call :
	* 
	* @access public 
	* @param array params
	* @return bool
	*/
	public function makeCall(){
	
		$header[] = $this->mimeType;
		
		$CURLOPT_URL = $this->requestURL;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $CURLOPT_URL);
		curl_setopt($ch, CURLOPT_TIMEOUT, 900);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		
		/**
			curl_setopt($ch, CURLOPT_USERAGENT, 'XtraDoh xAgent');
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, "$this->login:$this->password"); 
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->reqestType);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->reqestMessage);

		*/
		$data = curl_exec($ch);
		
		$info = curl_getinfo($ch);
		
		$this->requestURL = '';
		return $data;
	}
	

	
}

?>