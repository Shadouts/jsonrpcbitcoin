<?php
require_once(__DIR__ . '/../JsonRpcBitcoin.php');
require_once('config.php');

class JsonRpcBitcoinTest extends PHPUnit_Framework_TestCase
{
	private $bitcoindConn;

	public function setUp(){
		global $configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort;
		
		$this->bitcoindConn = new JsonRpcBitcoin($configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort);
	}
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
	
	/**
	* @depends testCanAuthenticateToBitcoindWithGoodCred
	*/
	public function testIsOnTestnet() {
		$result = (array)json_decode($this->bitcoindConn->getinfo());
		$this->assertNotFalse($result['result']->testnet);

	}

	/**
	* Nondistructive Chain tests
	*/

	/**
	* @depends testCanAuthenticateToBitcoindWithGoodCred
	*/
	public function testCmdChainGetInfo()
	{	
		$result = (array)json_decode($this->bitcoindConn->getinfo());
		$this->assertNotNull($result['result']);
	}
	
	/**
	* Distructive Chain tests
	*/

	/**
	* @depends testIsOnTestnet
	*/
	public function testCmdChainAddNode()
	{	
		// Stop here and mark this test as incomplete.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	* Nondistructive Wallet tests
	*/

	/**
	* @depends testCanAuthenticateToBitcoindWithGoodCred
	*/
	public function testCmdWalletGetBalance()
	{	
		// Stop here and mark this test as incomplete.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	* Distructive Wallet tests
	*/

	/**
	* @depends testIsOnTestnet
	*/
	public function testCmdWalletAddMultiSigAddress()
	{	
		// Stop here and mark this test as incomplete.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
?>