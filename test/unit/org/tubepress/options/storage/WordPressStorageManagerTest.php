<?php
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/storage/WordPressStorageManager.class.php';

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

class org_tubepress_options_storage_WordPressStorageManagerTest extends TubePressUnitTest {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_options_storage_WordPressStorageManager();
		$this->_sut->setContainer($this->getIoc());
	}
	
	function testInit()
	{
		$this->_sut->init();
	}
	
    function testSet()
    {
        $this->_sut->set(org_tubepress_options_category_Advanced::DEBUG_ON, true);
    }
    
    function testNuclear()
    {
        $this->_sut->nuclear();
    }
}
?>