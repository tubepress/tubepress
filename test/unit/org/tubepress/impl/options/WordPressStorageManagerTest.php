<?php
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/options/WordPressStorageManager.class.php';

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
        $this->_sut->set(org_tubepress_api_const_options_names_Advanced::DEBUG_ON, true);
    }
    
    function testNuclear()
    {
        $this->_sut->nuclear();
    }
}

