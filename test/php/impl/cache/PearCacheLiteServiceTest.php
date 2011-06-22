<?php
require_once dirname(__FILE__) . '/../../../../sys/classes/org/tubepress/impl/cache/PearCacheLiteCacheService.class.php';

class org_tubepress_impl_cache_PearCacheLiteCacheServiceTest extends TubePressUnitTest {

	private $_sut;

	function setUp()
	{
	    parent::setUp();
		$this->_sut = new org_tubepress_impl_cache_PearCacheLiteCacheService();
	}

	function testSetGet()
	{
	    $this->_setupMocks();

	    $this->_sut->clean();
		$key = $this->_randomString();
		$data = $this->_randomString();
		$this->_sut->save($key, $data);
		$this->assertEquals($data, $this->_sut->get($key));
	}

	function testSetNonStringData()
	{
		$this->assertFalse($this->_sut->save("fake", 3));
	}

	private function _setupMocks()
	{
	    $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

		$context  = $ioc->get('org_tubepress_api_exec_ExecutionContext');
		$context->shouldReceive('get')->with(org_tubepress_api_const_options_names_Advanced::CACHE_DIR)->times(6)->andReturn('');
		$context->shouldReceive('get')->with(org_tubepress_api_const_options_names_Advanced::CACHE_LIFETIME_SECONDS)->twice()->andReturn(1);
		$context->shouldReceive('get')->with(org_tubepress_api_const_options_names_Advanced::CACHE_CLEAN_FACTOR)->once()->andReturn(1);

        $fs = $ioc->get('org_tubepress_api_filesystem_Explorer');
        $fs->shouldReceive('getSystemTempDirectory')->times(6)->andReturnUsing( function () {
            return sys_get_temp_dir();
        });
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