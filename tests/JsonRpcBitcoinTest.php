<?php
require_once(__DIR__ . '/../src/JsonRpcBitcoin/JsonRpcBitcoin.php');
use \JsonRpcBitcoin\JsonRpcBitcoin;

require_once('config.php');

$blockhash;

class JsonRpcBitcoinTest extends PHPUnit_Framework_TestCase
{
	public function setUp(){
		global $configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort;
		
		$this->bitcoindConn = new JsonRpcBitcoin($configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort);
		$this->blockHash = '';
	}
	public function tearDown(){ }

	public function testCanConnectToBitcoind()
	{	
		global $configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort;
		
		$connObj = new JsonRpcBitcoin($configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort);
		$result = (array)json_decode($connObj->sendRaw('getinfo'));
		$this->assertEquals((array)$result, array());
	}

	/**
	* @depends testCanConnectToBitcoind
	*/
	public function testCanAuthenticateToBitcoindWithBadCred()
	{	
		global $configRpcHost, $configRpcPort;
		
		$connObj = new JsonRpcBitcoin('xxx', 'xxx', $configRpcHost, $configRpcPort);
		$this->assertJsonStringEqualsJsonString(
			$connObj->sendRaw('getinfo'), 
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
		$result = (array)json_decode($connObj->sendRaw('getinfo'));
		$this->assertNotNull($result['result']);
	}

	/**
	* @depends testCanAuthenticateToBitcoindWithGoodCred
	*/
	public function testCanSendWithBadParams()
	{	
		global $configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort;
		
		$connObj = new JsonRpcBitcoin($configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort);
		$result = (array)json_decode($connObj->sendRaw('getblockhash', array('NotABlockHeight')));
		$this->assertNotNull($result['error']);
	}

	/**
	* @depends testCanAuthenticateToBitcoindWithGoodCred
	*/
	public function testCanSendWithGoodParams()
	{	
		global $configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort;
		
		$connObj = new JsonRpcBitcoin($configRpcUser, $configRpcPass, $configRpcHost, $configRpcPort);
		$result = (array)json_decode($connObj->sendRaw('getblockhash', array(5)));
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
	* @depends testCanAuthenticateToBitcoindWithGoodCred
	*/
	public function testCmdGetBlockHash()
	{	
		$result = (array)json_decode($this->bitcoindConn->getblockhash(20));
		$this->assertNotNull($result['result']);
		return $result['result']; // the block hash
	}

	/**
	* @depends testCmdGetBlockHash
	*/
	public function testCmdGetBlock($blockHash)
	{	
		$result = (array)json_decode($this->bitcoindConn->getblock($blockHash));
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