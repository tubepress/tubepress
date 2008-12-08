<?php
class SimpleTubePressUrlBuilderTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new SimpleTubePressUrlBuilder();
	}
	
	function testBuildGalleryUrlUserMode()
	{
		$tpom = $this->getMock("TubePressOptionsManager");
		$tpom->expects($this->exactly(8))
			 ->method("get")
			 ->will($this->returnCallback("userModeCallback"));
		$this->_sut->setOptionsManager($tpom);
		
		$qss = $this->getMock("TubePressQueryStringService");
		$qss->expects($this->once())
			->method("getPageNum")
			->will($this->returnValue(1));
		$this->_sut->setQueryStringService($qss);
		
		$this->assertEquals("http://gdata.youtube.com/feeds/api/users/3hough/uploads?start-index=1&amp;max-results=3&amp;racy=exclude&amp;orderby=relevance&amp;client=clientkey&amp;key=devkey", $this->_sut->buildGalleryUrl());
	}
}

function userModeCallback()
{
	$args = func_get_args();
   	$vals = array(
		TubePressGalleryOptions::MODE => TubePressGallery::USER,
		TubePressGalleryOptions::USER_VALUE => "3hough",
		TubePressDisplayOptions::RESULTS_PER_PAGE => 3,
        TubePressAdvancedOptions::FILTER => true,
        TubePressDisplayOptions::ORDER_BY => "relevance",
        TubePressGalleryOptions::MODE => TubePressGallery::USER,
        TubePressAdvancedOptions::CLIENT_KEY => "clientkey",
        TubePressAdvancedOptions::DEV_KEY => "devkey"
	);
	return $vals[$args[0]]; 
}
?>