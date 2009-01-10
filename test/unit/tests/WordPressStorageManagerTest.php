<?php
function get_option($optionName) {
    return "";
}

class WordPressStorageManagerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new WordPressStorageManager();
	}
	
	function testInit()
	{
		$this->_sut->init();
	}
    
}
?>