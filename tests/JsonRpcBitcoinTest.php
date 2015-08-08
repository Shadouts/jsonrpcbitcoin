<?php
require_once(__DIR__ . '/../JsonRpcBitcoin.php');

class JsonRpcBitcoinTest extends PHPUnit_Framework_TestCase
{
  public function setUp(){ }
  public function tearDown(){ }

  public function testConnectionIsValid()
  {
    // test to ensure that the object from an fsockopen is valid
    $connObj = new JsonRpcBitcoin('', '', '127.0.0.1', 8332);
    $this->assertTrue($connObj->send('getinfo') !== false);
  }
}
?>