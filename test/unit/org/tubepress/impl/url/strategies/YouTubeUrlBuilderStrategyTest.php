<?php

require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/url/strategies/YouTubeUrlBuilderStrategy.class.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/api/provider/Provider.class.php';

class org_tubepress_impl_url_strategies_YouTubeUrlBuilderStrategyTest extends TubePressUnitTest {
    
	private $_sut;
	
	function setUp()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_url_strategies_YouTubeUrlBuilderStrategy();
	}

	public function getMock($className)
	{
		$mock = parent::getMock($className);

		switch ($className) {
			case 'org_tubepress_api_provider_ProviderCalculator':
				$mock->expects($this->any())
					->method('calculateProviderOfVideoId')
					->will($this->returnValue(org_tubepress_api_provider_Provider::YOUTUBE));
		}

		return $mock;
	}

	function testSingleVideoUrl()
	{
		$this->assertEquals("http://gdata.youtube.com/feeds/api/videos/dfsdkjerufd?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg", 
		$this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, true, 'dfsdkjerufd'));
	}
	
	function testexecuteUserMode()
	{
	    $this->setOptions(array(
	       org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::USER
	    ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/users/3hough/uploads?" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, false, 1));
	}
	
	function testexecuteTopRated()
	{
	    $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::TOP_RATED
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/top_rated?time=today&" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, false, 1));
	}
	
	function testexecutePopular()
	{
	    $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::POPULAR
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_viewed?time=today&" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, false, 1));
	}
	
	function testexecutePlaylist()
	{
	    $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST,
           org_tubepress_api_const_options_names_Display::ORDER_BY => 'relevance'
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/playlists/D2B04665B213AE35?v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&safeSearch=moderate&format=5", 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, false, 1));
	}
	
	function testexecuteMostResponded()
	{
	    $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::MOST_RESPONDED
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_responded?" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, false, 1));
	}
	
	function testexecuteMostRecent()
	{
	    $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::MOST_RECENT
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_recent?" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, false, 1));
	}

	function testexecuteTopFavorites()
	{
	    $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::TOP_FAVORITES
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/top_favorites?" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, false, 1));
	}
	
	function testexecuteMostDiscussed()
	{
	    $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::MOST_DISCUSSED
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/most_discussed?" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, false, 1));
	}
	
	function testexecuteFavorites()
	{
	    $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::FAVORITES
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/users/mrdeathgod/favorites?" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, false, 1));
	}
	
    function testexecuteTagWithDoubleQuotes()
    {
        $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE      => org_tubepress_api_const_options_values_ModeValue::TAG,
           org_tubepress_api_const_options_names_Output::TAG_VALUE => '"stewart daily" -show' 
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=%22stewart%2Bdaily%22%2B-show&" . $this->_standardPostProcessingStuff(), 
            $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, false, 1));
    }
	
    function testexecuteTagWithExclusion()
    {
        $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE      => org_tubepress_api_const_options_values_ModeValue::TAG,
           org_tubepress_api_const_options_names_Output::TAG_VALUE => 'stewart daily -show' 
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart%2Bdaily%2B-show&" . $this->_standardPostProcessingStuff(), 
            $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, false, 1));
    }
	
    function testexecuteTagWithPipes()
    {
        $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE      => org_tubepress_api_const_options_values_ModeValue::TAG,
           org_tubepress_api_const_options_names_Output::TAG_VALUE => 'stewart|daily|show' 
        ));
        $this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart%7Cdaily%7Cshow&" . $this->_standardPostProcessingStuff(), 
            $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, false, 1));
    }
	
	function testexecuteTag()
	{
	    $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::TAG
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart%2Bdaily%2Bshow&" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, false, 1));
	}
	
	function testexecuteTagWithUser()
	{
	    $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::TAG,
org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '3hough'
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/videos?q=stewart%2Bdaily%2Bshow&author=3hough&" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, false, 1));
	}

	function testexecuteFeatured()
	{
	    $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::FEATURED
        ));
		$this->assertEquals("http://gdata.youtube.com/feeds/api/standardfeeds/recently_featured?" . $this->_standardPostProcessingStuff(), 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, false, 1));
	}
	
	private function _standardPostProcessingStuff()
	{
		return "v=2&key=AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg&start-index=1&max-results=20&orderby=viewCount&safeSearch=moderate&format=5";
	}
}



