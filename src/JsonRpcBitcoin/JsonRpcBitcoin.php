<?php
namespace JsonRpcBitcoin;

class JsonRpcBitcoin {
	protected $rpcUser;
	protected $rpcPass;
	protected $rpcHost;
	protected $rpcPort;
	
	public $result;
	
	public function __construct($user, $pass, $host, $port) {

		if (is_string($user)) {
			$this->rpcUser = $user;
		} 
		else {
			return $this->build_json_error(0, 'Unable to connect to bitcoind: username is not a string');
		}	

		if (is_string($pass)) {
			$this->rpcPass = $pass;
		} 
		else {
			return $this->build_json_error(0, 'Unable to connect to bitcoind: password is not a string');
		}	

		if (!empty($host) && is_string($host)) {
			$this->rpcHost = $host;
		} 
		else {
			return $this->build_json_error(0, 'Unable to connect to bitcoind: host is not a string');
		}	

		if (!empty($port) && is_numeric($port)) {
			$this->rpcPort = $port;
		} 
		else {
			return $this->build_json_error(0, 'Unable to connect to bitcoind: port not numeric');
		}	
	}
	
	public function getbalance() {
		return $this->build_json_error(0, 'Not yet supported');
	}

	public function getblockhash($blockheight) {
		return $this->sendRaw('getblockhash', array($blockheight));
	}

	public function getblock($blockhash) {
		return $this->sendRaw('getblock', array($blockhash));
	}

	public function getinfo() {
		return $this->sendRaw('getinfo');
	}
	
	public function getrawtransaction($blockhash, $verbose=0) {
		return $this->sendRaw('getrawtransaction', array($blockhash, $verbose));
	}

	public function sendRaw($method, $params = array()) {
		return $this->send($method, $params);
	}

	private function send($method, $params = array()){
		/* method and params were passed */
		if (func_num_args() == 2){
			$postdata = array(
				'method' => func_get_arg(0),
				'params' => func_get_arg(1),
				'id' => 1
			);
		}
		/* only method was passed */
		else if (func_num_args() == 1){
			$postdata = array(
				'method' => func_get_arg(0),
				'params' => array(),
				'id' => 1
			);
		} 
		/* either too many of not enough arguments were passed, error */
		else {
			return $this->build_json_error(0, 'Invalid number of arguments passed to send');
		}

		$postdata_json = json_encode($postdata);
		
		$bitcoinAuth = base64_encode($this->rpcUser . ':' . $this->rpcPass);
		
		$request_headers = array();
		$request_headers[] = 'Authorization: Basic '. $bitcoinAuth;
		$request_headers[] = 'Content-type: application/json';
	
		//open connection
		$ch = curl_init();
	
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, 'http://' . $this->rpcHost . ':' . $this->rpcPort);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
		curl_setopt($ch, CURLOPT_POST, count($postdata));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata_json);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		//execute post
		$result = curl_exec($ch);
		
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 401) {
			return $this->build_json_error(401, 'Unable to authenticate to bitcoind');
		}
		
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
			return $this->build_json_error(curl_getinfo($ch, CURLINFO_HTTP_CODE), $result);
		}
		
		$this->result = $result;

		//close connection
		curl_close($ch);
	
		return $result;
	}
	
	private function build_json_error($code, $message) {
		$result = json_encode(array(
			'result' => null, 
			'error' => array(
				'code' => $code, 
				'message' => $message
			),
			'id' => 'jsonrpcbitcoin'
		));
		return $result;
	}
}
?>
