<?php
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/options/WordPressStorageManager.class.php';

if (!function_exists('get_option')) {
    function get_option($optionName) {
        return "";
    }
}

if (!function_exists('update_option')) {
    function update_option($optionName) {
        return "";
    }
}

if (!function_exists('get_alloptions')) {
    function get_alloptions() {
        return array('foo' => 'bar');
    }
}

class org_tubepress_impl_options_WordPressStorageManagerTest extends TubePressUnitTest {
    
	private $_sut;
	
	function setUp()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_options_WordPressStorageManager();
	}
	
	function testInit()
	{
		$this->_sut->init();
	}
	
    function testSet()
    {
        $this->_sut->set(org_tubepress_api_const_options_Advanced::DEBUG_ON, true);
    }
    
    function testNuclear()
    {
        $this->_sut->nuclear();
    }
}
?>
