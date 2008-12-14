<?php
include_once dirname(__FILE__) . "/../../../tubepress_classloader.php";

class SimpleTubePressThumbnailServiceTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new SimpleTubePressThumbnailService();
	}
	
	function testGetHtml()
	{
		
	}
}
?>