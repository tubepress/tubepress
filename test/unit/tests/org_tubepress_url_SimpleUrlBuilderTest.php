<?php
class org_tubepress_url_SimpleUrlBuilderTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_url_SimpleUrlBuilder();
	}
	
	function testBuildGalleryUrlUserMode()
	{
		$tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$tpom->expects($this->exactly(9))
			 ->method("get")
			 ->will($this->returnCallback("userModeCallback"));
		$this->_sut->setOptionsManager($tpom);
		
		$this->assertEquals("http://gdata.youtube.com/feeds/api/users/3hough/uploads?" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl(1));
	}
	
	function testBuildGalleryUrlTopRated()
	{
		$tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$tpom->expects($this->exactly(9))
			 ->method("get")
			 ->will($this->returnCallback("topRatedModeCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/top_rated?time=today&" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl(1));
	}
	
	function testBuildGalleryUrlPopular()
	{
		$tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$tpom->expects($this->exactly(9))
			 ->method("get")
			 ->will($this->returnCallback("popularModeCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_viewed?time=today&" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl(1));
	}
	
	function testBuildGalleryUrlPlaylist()
	{
		$tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$tpom->expects($this->exactly(9))
			 ->method("get")
			 ->will($this->returnCallback("playlistModeCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/fakeplaylist?start-index=1&max-results=3&racy=exclude&orderby=relevance&client=clientkey&key=devkey&format=5", $this->_sut->buildGalleryUrl(1));
	}
	
	function testBuildGalleryUrlMostResponded()
	{
		$tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$tpom->expects($this->exactly(8))
			 ->method("get")
			 ->will($this->returnCallback("mostRespondedModeCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_responded?" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl(1));
	}
	
	function testBuildGalleryUrlMostRecent()
	{
		$tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$tpom->expects($this->exactly(8))
			 ->method("get")
			 ->will($this->returnCallback("mostRecentCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_recent?" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl(1));
	}

	function testBuildGalleryUrlMostLinked()
	{
		$tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$tpom->expects($this->exactly(8))
			 ->method("get")
			 ->will($this->returnCallback("mostLinkedCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_linked?" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl(1));
	}
	
	function testBuildGalleryUrlMostDiscussed()
	{
		$tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$tpom->expects($this->exactly(8))
			 ->method("get")
			 ->will($this->returnCallback("mostDiscussedCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_discussed?" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl(1));
	}
	
	function testBuildGalleryUrlMobile()
	{
		$tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$tpom->expects($this->exactly(8))
			 ->method("get")
			 ->will($this->returnCallback("mobileCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/watch_on_mobile?" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl(1));
	}
	
	function testBuildGalleryUrlFavorites()
	{
		$tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$tpom->expects($this->exactly(9))
			 ->method("get")
			 ->will($this->returnCallback("favoritesCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->assertEquals("http://gdata.youtube.com/feeds/api/users/3hough/favorites?" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl(1));
	}
	
	function testBuildGalleryUrlTag()
	{
		$tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$tpom->expects($this->exactly(9))
			 ->method("get")
			 ->will($this->returnCallback("tagCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=foo%2Bbar&" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl(1));
	}
	
	function testBuildGalleryUrlFeatured()
	{
		$tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$tpom->expects($this->exactly(8))
			 ->method("get")
			 ->will($this->returnCallback("featuredCallback"));
		$this->_sut->setOptionsManager($tpom);

		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?" . $this->_standardPostProcessingStuff(), $this->_sut->buildGalleryUrl(1));
	}
	
	private function _standardPostProcessingStuff()
	{
		return "start-index=1&max-results=3&racy=exclude&orderby=relevance&client=clientkey&key=devkey&format=5";
	}
}

function mostRecentCallback()
{
	$args = func_get_args();
   	$vals = array(
		org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::MOST_RECENT,
		org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3,
        org_tubepress_options_category_Feed::FILTER => true,
        org_tubepress_options_category_Display::ORDER_BY => "relevance",
        org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::MOST_RECENT,
        org_tubepress_options_category_Feed::CLIENT_KEY => "clientkey",
        org_tubepress_options_category_Feed::DEV_KEY => "devkey",
        org_tubepress_options_category_Feed::EMBEDDABLE_ONLY => true
	);
	return $vals[$args[0]]; 
}

function mostRespondedModeCallback()
{
	$args = func_get_args();
   	$vals = array(
		org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::MOST_RESPONDED,
		org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3,
        org_tubepress_options_category_Feed::FILTER => true,
        org_tubepress_options_category_Display::ORDER_BY => "relevance",
        org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::MOST_RESPONDED,
        org_tubepress_options_category_Feed::CLIENT_KEY => "clientkey",
        org_tubepress_options_category_Feed::DEV_KEY => "devkey",
        org_tubepress_options_category_Feed::EMBEDDABLE_ONLY => true
	);
	return $vals[$args[0]]; 
}

function playlistModeCallback()
{
	$args = func_get_args();
   	$vals = array(
		org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::PLAYLIST,
		org_tubepress_options_category_Gallery::PLAYLIST_VALUE => "fakeplaylist",
		org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3,
        org_tubepress_options_category_Feed::FILTER => true,
        org_tubepress_options_category_Display::ORDER_BY => "relevance",
        org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::PLAYLIST,
        org_tubepress_options_category_Feed::CLIENT_KEY => "clientkey",
        org_tubepress_options_category_Feed::DEV_KEY => "devkey",
        org_tubepress_options_category_Feed::EMBEDDABLE_ONLY => true
	);
	return $vals[$args[0]]; 
}

function popularModeCallback()
{
	$args = func_get_args();
   	$vals = array(
		org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::POPULAR,
		org_tubepress_options_category_Gallery::MOST_VIEWED_VALUE => "today",
		org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3,
        org_tubepress_options_category_Feed::FILTER => true,
        org_tubepress_options_category_Display::ORDER_BY => "relevance",
        org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::POPULAR,
        org_tubepress_options_category_Feed::CLIENT_KEY => "clientkey",
        org_tubepress_options_category_Feed::DEV_KEY => "devkey",
        org_tubepress_options_category_Feed::EMBEDDABLE_ONLY => true
	);
	return $vals[$args[0]]; 
}

function topRatedModeCallback()
{
	$args = func_get_args();
   	$vals = array(
		org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::TOP_RATED,
		org_tubepress_options_category_Gallery::TOP_RATED_VALUE => "today",
		org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3,
        org_tubepress_options_category_Feed::FILTER => true,
        org_tubepress_options_category_Display::ORDER_BY => "relevance",
        org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::TOP_RATED,
        org_tubepress_options_category_Feed::CLIENT_KEY => "clientkey",
        org_tubepress_options_category_Feed::DEV_KEY => "devkey",
        org_tubepress_options_category_Feed::EMBEDDABLE_ONLY => true
	);
	return $vals[$args[0]]; 
}

function userModeCallback()
{
	$args = func_get_args();
   	$vals = array(
		org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::USER,
		org_tubepress_options_category_Gallery::USER_VALUE => "3hough",
		org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3,
        org_tubepress_options_category_Feed::FILTER => true,
        org_tubepress_options_category_Display::ORDER_BY => "relevance",
        org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::USER,
        org_tubepress_options_category_Feed::CLIENT_KEY => "clientkey",
        org_tubepress_options_category_Feed::DEV_KEY => "devkey",
        org_tubepress_options_category_Feed::EMBEDDABLE_ONLY => true
	);
	return $vals[$args[0]]; 
}

function mostLinkedCallback()
{
	$args = func_get_args();
   	$vals = array(
		org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::USER,
		org_tubepress_options_category_Gallery::USER_VALUE => "3hough",
		org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3,
        org_tubepress_options_category_Feed::FILTER => true,
        org_tubepress_options_category_Display::ORDER_BY => "relevance",
        org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::MOST_LINKED,
        org_tubepress_options_category_Feed::CLIENT_KEY => "clientkey",
        org_tubepress_options_category_Feed::DEV_KEY => "devkey",
        org_tubepress_options_category_Feed::EMBEDDABLE_ONLY => true
	);
	return $vals[$args[0]]; 
}

function mostDiscussedCallback()
{
	$args = func_get_args();
   	$vals = array(
		org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::USER,
		org_tubepress_options_category_Gallery::USER_VALUE => "3hough",
		org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3,
        org_tubepress_options_category_Feed::FILTER => true,
        org_tubepress_options_category_Display::ORDER_BY => "relevance",
        org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::MOST_DISCUSSESD,
        org_tubepress_options_category_Feed::CLIENT_KEY => "clientkey",
        org_tubepress_options_category_Feed::DEV_KEY => "devkey",
        org_tubepress_options_category_Feed::EMBEDDABLE_ONLY => true
	);
	return $vals[$args[0]]; 
}

function mobileCallback()
{
	$args = func_get_args();
   	$vals = array(
		org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::USER,
		org_tubepress_options_category_Gallery::USER_VALUE => "3hough",
		org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3,
        org_tubepress_options_category_Feed::FILTER => true,
        org_tubepress_options_category_Display::ORDER_BY => "relevance",
        org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::MOBILE,
        org_tubepress_options_category_Feed::CLIENT_KEY => "clientkey",
        org_tubepress_options_category_Feed::DEV_KEY => "devkey",
        org_tubepress_options_category_Feed::EMBEDDABLE_ONLY => true
	);
	return $vals[$args[0]]; 
}

function favoritesCallback()
{
	$args = func_get_args();
   	$vals = array(
		org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::USER,
		org_tubepress_options_category_Gallery::USER_VALUE => "3hough",
		org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3,
        org_tubepress_options_category_Feed::FILTER => true,
        org_tubepress_options_category_Display::ORDER_BY => "relevance",
        org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::FAVORITES,
        org_tubepress_options_category_Gallery::FAVORITES_VALUE => "3hough",
        org_tubepress_options_category_Feed::CLIENT_KEY => "clientkey",
        org_tubepress_options_category_Feed::DEV_KEY => "devkey",
        org_tubepress_options_category_Feed::EMBEDDABLE_ONLY => true
	);
	return $vals[$args[0]]; 
}

function tagCallback()
{
	$args = func_get_args();
   	$vals = array(
		org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::USER,
		org_tubepress_options_category_Gallery::USER_VALUE => "3hough",
		org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3,
        org_tubepress_options_category_Feed::FILTER => true,
        org_tubepress_options_category_Display::ORDER_BY => "relevance",
        org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::TAG,
        org_tubepress_options_category_Feed::CLIENT_KEY => "clientkey",
        org_tubepress_options_category_Feed::DEV_KEY => "devkey",
        org_tubepress_options_category_Gallery::TAG_VALUE => "foo bar",
        org_tubepress_options_category_Feed::EMBEDDABLE_ONLY => true
	);
	return $vals[$args[0]]; 
}

function featuredCallback()
{
	$args = func_get_args();
   	$vals = array(
		org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::USER,
		org_tubepress_options_category_Gallery::USER_VALUE => "3hough",
		org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3,
        org_tubepress_options_category_Feed::FILTER => true,
        org_tubepress_options_category_Display::ORDER_BY => "relevance",
        org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_Gallery::FEATURED,
        org_tubepress_options_category_Feed::CLIENT_KEY => "clientkey",
        org_tubepress_options_category_Feed::DEV_KEY => "devkey",
        org_tubepress_options_category_Gallery::TAG_VALUE => "foo bar",
        org_tubepress_options_category_Feed::EMBEDDABLE_ONLY => true
	);
	return $vals[$args[0]]; 
}
?>