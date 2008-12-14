<?php
include_once dirname(__FILE__) . "/../../../tubepress_classloader.php";

class WordPressStorageManagerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new WordPressStorageManager();
	}
	
	function testInit()
	{
		
	}
    
}
?>