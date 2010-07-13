<?php
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/cache/SimpleCacheService.class.php';

class org_tubepress_cache_SimpleCacheServiceTest extends PHPUnit_Framework_TestCase {

	private $_sut;

	function setUp()
	{
		$this->_sut = new org_tubepress_cache_SimpleCacheService();
	}

	function testSetGet()
	{
		$key = $this->_randomString();
		$data = $this->_randomString();
		$this->_sut->save($key, $data);
		$this->assertTrue($this->_sut->has($key));
		$this->assertFalse($this->_sut->has('fakekey'));
		$this->assertEquals($data, $this->_sut->get($key));
	}

	/**
     * @expectedException Exception
     */
	function testSetNonStringData()
	{
		$this->_sut->save("fake", 3);
	}

	private function _randomString() {
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pass = '' ;

		while ($i <= 7) {
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		return $pass;
	}
}
?>