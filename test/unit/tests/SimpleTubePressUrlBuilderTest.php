<?php
include_once dirname(__FILE__) . "/../../../tubepress_classloader.php";

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

	function testBuildGalleryUrlMostLinked()
	{
		$tpom = $this->getMock("TubePressOptionsManager");
		$tpom->expects($this->exactly(7))
			 ->method("get")
			 ->will($this->returnCallback("mostLinkedCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->_sut->setQueryStringService($this->_setupQss());
		
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_linked?" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl());
	}
	
	function testBuildGalleryUrlMostDiscussed()
	{
		$tpom = $this->getMock("TubePressOptionsManager");
		$tpom->expects($this->exactly(7))
			 ->method("get")
			 ->will($this->returnCallback("mostDiscussedCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->_sut->setQueryStringService($this->_setupQss());
		
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_discussed?" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl());
	}
	
	function testBuildGalleryUrlMobile()
	{
		$tpom = $this->getMock("TubePressOptionsManager");
		$tpom->expects($this->exactly(7))
			 ->method("get")
			 ->will($this->returnCallback("mobileCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->_sut->setQueryStringService($this->_setupQss());
		
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/watch_on_mobile?" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl());
	}
	
	function testBuildGalleryUrlFavorites()
	{
		$tpom = $this->getMock("TubePressOptionsManager");
		$tpom->expects($this->exactly(8))
			 ->method("get")
			 ->will($this->returnCallback("favoritesCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->_sut->setQueryStringService($this->_setupQss());
		
		$this->assertEquals("http://gdata.youtube.com/feeds/api/users/3hough/favorites?" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl());
	}
	
	function testBuildGalleryUrlTag()
	{
		$tpom = $this->getMock("TubePressOptionsManager");
		$tpom->expects($this->exactly(8))
			 ->method("get")
			 ->will($this->returnCallback("tagCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->_sut->setQueryStringService($this->_setupQss());
		
		$this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=foo%2Bbar&" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl());
	}
	
	function testBuildGalleryUrlFeatured()
	{
		$tpom = $this->getMock("TubePressOptionsManager");
		$tpom->expects($this->exactly(7))
			 ->method("get")
			 ->will($this->returnCallback("featuredCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->_sut->setQueryStringService($this->_setupQss());
		
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl());
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

function mostLinkedCallback()
{
	$args = func_get_args();
   	$vals = array(
		TubePressGalleryOptions::MODE => TubePressGallery::USER,
		TubePressGalleryOptions::USER_VALUE => "3hough",
		TubePressDisplayOptions::RESULTS_PER_PAGE => 3,
        TubePressAdvancedOptions::FILTER => true,
        TubePressDisplayOptions::ORDER_BY => "relevance",
        TubePressGalleryOptions::MODE => TubePressGallery::MOST_LINKED,
        TubePressAdvancedOptions::CLIENT_KEY => "clientkey",
        TubePressAdvancedOptions::DEV_KEY => "devkey"
	);
	return $vals[$args[0]]; 
}

function mostDiscussedCallback()
{
	$args = func_get_args();
   	$vals = array(
		TubePressGalleryOptions::MODE => TubePressGallery::USER,
		TubePressGalleryOptions::USER_VALUE => "3hough",
		TubePressDisplayOptions::RESULTS_PER_PAGE => 3,
        TubePressAdvancedOptions::FILTER => true,
        TubePressDisplayOptions::ORDER_BY => "relevance",
        TubePressGalleryOptions::MODE => TubePressGallery::MOST_DISCUSSESD,
        TubePressAdvancedOptions::CLIENT_KEY => "clientkey",
        TubePressAdvancedOptions::DEV_KEY => "devkey"
	);
	return $vals[$args[0]]; 
}

function mobileCallback()
{
	$args = func_get_args();
   	$vals = array(
		TubePressGalleryOptions::MODE => TubePressGallery::USER,
		TubePressGalleryOptions::USER_VALUE => "3hough",
		TubePressDisplayOptions::RESULTS_PER_PAGE => 3,
        TubePressAdvancedOptions::FILTER => true,
        TubePressDisplayOptions::ORDER_BY => "relevance",
        TubePressGalleryOptions::MODE => TubePressGallery::MOBILE,
        TubePressAdvancedOptions::CLIENT_KEY => "clientkey",
        TubePressAdvancedOptions::DEV_KEY => "devkey"
	);
	return $vals[$args[0]]; 
}

function favoritesCallback()
{
	$args = func_get_args();
   	$vals = array(
		TubePressGalleryOptions::MODE => TubePressGallery::USER,
		TubePressGalleryOptions::USER_VALUE => "3hough",
		TubePressDisplayOptions::RESULTS_PER_PAGE => 3,
        TubePressAdvancedOptions::FILTER => true,
        TubePressDisplayOptions::ORDER_BY => "relevance",
        TubePressGalleryOptions::MODE => TubePressGallery::FAVORITES,
        TubePressGalleryOptions::FAVORITES_VALUE => "3hough",
        TubePressAdvancedOptions::CLIENT_KEY => "clientkey",
        TubePressAdvancedOptions::DEV_KEY => "devkey"
	);
	return $vals[$args[0]]; 
}

function tagCallback()
{
	$args = func_get_args();
   	$vals = array(
		TubePressGalleryOptions::MODE => TubePressGallery::USER,
		TubePressGalleryOptions::USER_VALUE => "3hough",
		TubePressDisplayOptions::RESULTS_PER_PAGE => 3,
        TubePressAdvancedOptions::FILTER => true,
        TubePressDisplayOptions::ORDER_BY => "relevance",
        TubePressGalleryOptions::MODE => TubePressGallery::TAG,
        TubePressAdvancedOptions::CLIENT_KEY => "clientkey",
        TubePressAdvancedOptions::DEV_KEY => "devkey",
        TubePressGalleryOptions::TAG_VALUE => "foo bar"
	);
	return $vals[$args[0]]; 
}

function featuredCallback()
{
	$args = func_get_args();
   	$vals = array(
		TubePressGalleryOptions::MODE => TubePressGallery::USER,
		TubePressGalleryOptions::USER_VALUE => "3hough",
		TubePressDisplayOptions::RESULTS_PER_PAGE => 3,
        TubePressAdvancedOptions::FILTER => true,
        TubePressDisplayOptions::ORDER_BY => "relevance",
        TubePressGalleryOptions::MODE => TubePressGallery::FEATURED,
        TubePressAdvancedOptions::CLIENT_KEY => "clientkey",
        TubePressAdvancedOptions::DEV_KEY => "devkey",
        TubePressGalleryOptions::TAG_VALUE => "foo bar"
	);
	return $vals[$args[0]]; 
}
?>