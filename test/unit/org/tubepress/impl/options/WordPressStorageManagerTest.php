<?php
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/options/WordPressStorageManager.class.php';
require_once dirname(__FILE__) . '/../../../../org/tubepress/impl/env/wordpress/fake_wordpress_functions.inc.php';

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
