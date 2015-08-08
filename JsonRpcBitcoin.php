<?php

class JsonRpcBitcoin {
	private $rpcUser = '';
	private $rpcPass = '';
	private $rpcHost = '127.0.0.1';
	private $rpcPort = 8332;
	
	public $result;
	
	function __construct($user, $pass, $host, $port) {

		if (is_string($user)) {
			$this->rpcUser = $user;
		} 
		else {
			throw new Exception('Username is not a string');
		}	

		if (is_string($pass)) {
			$this->rpcPass = $pass;
		} 
		else {
			throw new Exception('Password is not a string');
		}	

		if (!empty($host) && is_string($host)) {
			$this->rpcHost = $host;
		} 
		else {
			throw new Exception('Host is not a string');
		}	

		if (!empty($port) && is_numeric($port)) {
			$this->rpcPort = $port;
		} 
		else {
			throw new Exception('Port is not numaric');
		}	
	}
	
	function send($method){
		$postdata = array(
			'method' => $method,
			'params' => array(),
			'id' => 1
		);
		
		$bitcoinAuth = base64_encode($this->rpcUser . ':' . $this->rpcPass);
		
		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'header'  => "Authorization: Basic $bitcoinAuth\r\n"
					. 'Content-type: application/json',
				'content' => json_encode($postdata)
			)
		);
		
		$context  = stream_context_create($opts);

		$this->result = file_get_contents('http://' . $this->rpcHost . ':' . $this->rpcPort, false, $context);
		
		echo $this->result;
	}
}
?>
