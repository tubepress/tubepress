<?php
include_once dirname(__FILE__) . "/../../../tubepress_classloader.php";
class TubePressWidgetGalleryTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new TubePressWidgetGallery();
	}
	
	function testGenerate()
	{
		
	}
}
?>