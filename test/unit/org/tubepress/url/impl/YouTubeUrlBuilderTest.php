<?php

require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/url/impl/YouTubeUrlBuilder.class.php';

class org_tubepress_url_impl_YouTubeUrlBuilderTest extends TubePressUnitTest {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_url_impl_YouTubeUrlBuilder();
	}

	function testSingleVideoUrl()
	{
		$this->assertEquals("http://gdata.youtube.com/feeds/api/videos/1?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg", 
		    $this->_sut->buildSingleVideoUrl($this->getIoc(), 1));
	}
	
	function testBuildGalleryUrlUserMode()
	{
	    $this->setOptions(array(
	       org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::USER
	    ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/users/3hough/uploads?" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->buildGalleryUrl($this->getIoc(), 1));
	}
	
	function testBuildGalleryUrlTopRated()
	{
	    $this->setOptions(array(
           org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::TOP_RATED
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/top_rated?time=today&" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->buildGalleryUrl($this->getIoc(), 1));
	}
	
	function testBuildGalleryUrlPopular()
	{
	    $this->setOptions(array(
           org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::POPULAR
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_viewed?time=today&" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->buildGalleryUrl($this->getIoc(), 1));
	}
	
	function testBuildGalleryUrlPlaylist()
	{
	    $this->setOptions(array(
           org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::PLAYLIST,
           org_tubepress_options_category_Display::ORDER_BY => 'relevance'
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&safeSearch=moderate&format=5", 
		    $this->_sut->buildGalleryUrl($this->getIoc(), 1));
	}
	
	function testBuildGalleryUrlMostResponded()
	{
	    $this->setOptions(array(
           org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::MOST_RESPONDED
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_responded?" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->buildGalleryUrl($this->getIoc(), 1));
	}
	
	function testBuildGalleryUrlMostRecent()
	{
	    $this->setOptions(array(
           org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::MOST_RECENT
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_recent?" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->buildGalleryUrl($this->getIoc(), 1));
	}

	function testBuildGalleryUrlMostLinked()
	{
	    $this->setOptions(array(
           org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::MOST_LINKED
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_linked?" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->buildGalleryUrl($this->getIoc(), 1));
	}
	
	function testBuildGalleryUrlMostDiscussed()
	{
	    $this->setOptions(array(
           org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::MOST_DISCUSSED
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_discussed?" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->buildGalleryUrl($this->getIoc(), 1));
	}
	
	function testBuildGalleryUrlMobile()
	{
	    $this->setOptions(array(
           org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::MOBILE
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/watch_on_mobile?" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->buildGalleryUrl($this->getIoc(), 1));
	}
	
	function testBuildGalleryUrlFavorites()
	{
	    $this->setOptions(array(
           org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::FAVORITES
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/users/mrdeathgod/favorites?" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->buildGalleryUrl($this->getIoc(), 1));
	}
	
	function testBuildGalleryUrlTag()
	{
	    $this->setOptions(array(
           org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::TAG
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart%2Bdaily%2Bshow&" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->buildGalleryUrl($this->getIoc(), 1));
	}
	
	function testBuildGalleryUrlFeatured()
	{
	    $this->setOptions(array(
           org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::FEATURED
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->buildGalleryUrl($this->getIoc(), 1));
	}
	
	private function _standardPostProcessingStuff()
	{
		return "v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&orderby=viewCount&safeSearch=moderate&format=5";
	}
}


?>
