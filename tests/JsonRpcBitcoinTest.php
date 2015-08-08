<?php
require_once(__DIR__ . '/../JsonRpcBitcoin.php');
require_once('config.php');

class JsonRpcBitcoinTest extends PHPUnit_Framework_TestCase
{
	public function setUp(){ }
	public function tearDown(){ }

	public function testCanConnectToBitcoindWithDefaultHostAndPort()
	{	
		global $configRpcUser, $configRpcPass;
		
		$connObj = new JsonRpcBitcoin($configRpcUser, $configRpcPass);
		$result = (array)json_decode($connObj->send('getinfo'));
		$this->assertNull($result['error'], null);
	}

	public function testCanConnectToBitcoind()
	{	
		global $configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort;
		
		$connObj = new JsonRpcBitcoin($configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort);
		$result = (array)json_decode($connObj->send('getinfo'));
		$this->assertNull($result['error'], null);
	}

	/**
	* @depends testCanConnectToBitcoind
	*/
	public function testCanAuthenticateToBitcoindWithBadCred()
	{	
		global $configRpcHost, $configRpcPort;
		
		$connObj = new JsonRpcBitcoin('xxx', 'xxx', $configRpcHost, $configRpcPort);
		$this->assertJsonStringEqualsJsonString(
			$connObj->send('getinfo'), 
			json_encode(array(
				'result' => null, 
				'error' => array(
					'code' => 401, 
					'message' => 'Unable to authenticate to bitcoind'
				),
				'id' => 'jsonrpcbitcoin'
			))
		);
	}

	/**
	* @depends testCanConnectToBitcoind
	*/
	public function testCanAuthenticateToBitcoindWithGoodCred()
	{	
		global $configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort;
		
		$connObj = new JsonRpcBitcoin($configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort);
		$result = (array)json_decode($connObj->send('getinfo'));
		$this->assertNotNull($result['result']);
	}

	/**
	* @depends testCanAuthenticateToBitcoindWithGoodCred
	*/
	public function testCanSendWithBadParams()
	{	
		global $configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort;
		
		$connObj = new JsonRpcBitcoin($configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort);
		$result = (array)json_decode($connObj->send('getblockhash', array('NotABlockHeight')));
		$this->assertNotNull($result['error']);
	}

/**
	* @depends testCanAuthenticateToBitcoindWithGoodCred
	*/
	public function testCanSendWithGoodParams()
	{	
		global $configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort;
		
		$connObj = new JsonRpcBitcoin($configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort);
		$result = (array)json_decode($connObj->send('getblockhash', array(20)));
		$this->assertNotNull($result['result']);
	}
}
?>