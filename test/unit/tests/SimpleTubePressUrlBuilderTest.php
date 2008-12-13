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
		
		$this->_sut->setQueryStringService($this->_setupQss());
		
		$this->assertEquals("http://gdata.youtube.com/feeds/api/users/3hough/uploads?" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl());
	}
	
	function testBuildGalleryUrlTopRated()
	{
		$tpom = $this->getMock("TubePressOptionsManager");
		$tpom->expects($this->exactly(8))
			 ->method("get")
			 ->will($this->returnCallback("topRatedModeCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->_sut->setQueryStringService($this->_setupQss());
		
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/top_rated?time=today&" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl());
	}
	
	function testBuildGalleryUrlPopular()
	{
		$tpom = $this->getMock("TubePressOptionsManager");
		$tpom->expects($this->exactly(8))
			 ->method("get")
			 ->will($this->returnCallback("popularModeCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->_sut->setQueryStringService($this->_setupQss());
		
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_viewed?time=today&" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl());
	}
	
	function testBuildGalleryUrlPlaylist()
	{
		$tpom = $this->getMock("TubePressOptionsManager");
		$tpom->expects($this->exactly(8))
			 ->method("get")
			 ->will($this->returnCallback("playlistModeCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->_sut->setQueryStringService($this->_setupQss());
		
		$this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/fakeplaylist?start-index=1&max-results=3&racy=exclude&client=clientkey&key=devkey", $this->_sut->buildGalleryUrl());
	}
	
	function testBuildGalleryUrlMostResponded()
	{
		$tpom = $this->getMock("TubePressOptionsManager");
		$tpom->expects($this->exactly(7))
			 ->method("get")
			 ->will($this->returnCallback("mostRespondedModeCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->_sut->setQueryStringService($this->_setupQss());
		
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_responded?" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl());
	}
	
	function testBuildGalleryUrlMostRecent()
	{
		$tpom = $this->getMock("TubePressOptionsManager");
		$tpom->expects($this->exactly(7))
			 ->method("get")
			 ->will($this->returnCallback("mostRecentCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->_sut->setQueryStringService($this->_setupQss());
		
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_recent?" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl());
	}
	
	private function _setupQss()
	{
		$qss = $this->getMock("TubePressQueryStringService");
		$qss->expects($this->once())
			->method("getPageNum")
			->will($this->returnValue(1));
		return $qss;
	}
	
	private function _standardPostProcessingStuff()
	{
		return "start-index=1&max-results=3&racy=exclude&orderby=relevance&client=clientkey&key=devkey";
	}
}

function mostRecentCallback()
{
	$args = func_get_args();
   	$vals = array(
		TubePressGalleryOptions::MODE => TubePressGallery::MOST_RECENT,
		TubePressDisplayOptions::RESULTS_PER_PAGE => 3,
        TubePressAdvancedOptions::FILTER => true,
        TubePressDisplayOptions::ORDER_BY => "relevance",
        TubePressGalleryOptions::MODE => TubePressGallery::MOST_RECENT,
        TubePressAdvancedOptions::CLIENT_KEY => "clientkey",
        TubePressAdvancedOptions::DEV_KEY => "devkey"
	);
	return $vals[$args[0]]; 
}

function mostRespondedModeCallback()
{
	$args = func_get_args();
   	$vals = array(
		TubePressGalleryOptions::MODE => TubePressGallery::MOST_RESPONDED,
		TubePressDisplayOptions::RESULTS_PER_PAGE => 3,
        TubePressAdvancedOptions::FILTER => true,
        TubePressDisplayOptions::ORDER_BY => "relevance",
        TubePressGalleryOptions::MODE => TubePressGallery::MOST_RESPONDED,
        TubePressAdvancedOptions::CLIENT_KEY => "clientkey",
        TubePressAdvancedOptions::DEV_KEY => "devkey"
	);
	return $vals[$args[0]]; 
}

function playlistModeCallback()
{
	$args = func_get_args();
   	$vals = array(
		TubePressGalleryOptions::MODE => TubePressGallery::PLAYLIST,
		TubePressGalleryOptions::PLAYLIST_VALUE => "fakeplaylist",
		TubePressDisplayOptions::RESULTS_PER_PAGE => 3,
        TubePressAdvancedOptions::FILTER => true,
        TubePressDisplayOptions::ORDER_BY => "relevance",
        TubePressGalleryOptions::MODE => TubePressGallery::PLAYLIST,
        TubePressAdvancedOptions::CLIENT_KEY => "clientkey",
        TubePressAdvancedOptions::DEV_KEY => "devkey"
	);
	return $vals[$args[0]]; 
}

function popularModeCallback()
{
	$args = func_get_args();
   	$vals = array(
		TubePressGalleryOptions::MODE => TubePressGallery::POPULAR,
		TubePressGalleryOptions::MOST_VIEWED_VALUE => "today",
		TubePressDisplayOptions::RESULTS_PER_PAGE => 3,
        TubePressAdvancedOptions::FILTER => true,
        TubePressDisplayOptions::ORDER_BY => "relevance",
        TubePressGalleryOptions::MODE => TubePressGallery::POPULAR,
        TubePressAdvancedOptions::CLIENT_KEY => "clientkey",
        TubePressAdvancedOptions::DEV_KEY => "devkey"
	);
	return $vals[$args[0]]; 
}

function topRatedModeCallback()
{
	$args = func_get_args();
   	$vals = array(
		TubePressGalleryOptions::MODE => TubePressGallery::TOP_RATED,
		TubePressGalleryOptions::TOP_RATED_VALUE => "today",
		TubePressDisplayOptions::RESULTS_PER_PAGE => 3,
        TubePressAdvancedOptions::FILTER => true,
        TubePressDisplayOptions::ORDER_BY => "relevance",
        TubePressGalleryOptions::MODE => TubePressGallery::TOP_RATED,
        TubePressAdvancedOptions::CLIENT_KEY => "clientkey",
        TubePressAdvancedOptions::DEV_KEY => "devkey"
	);
	return $vals[$args[0]]; 
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