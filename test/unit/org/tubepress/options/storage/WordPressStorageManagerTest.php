<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/storage/WordPressStorageManager.class.php';

function get_option($optionName) {
    return "";
}

function update_option($optionName) {
    return "";
}

class org_tubepress_options_storage_WordPressStorageManagerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_options_storage_WordPressStorageManager();
	}
	
	function testInit()
	{
		//$this->_sut->init();
	}
    
}
?>