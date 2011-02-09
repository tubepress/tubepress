<?php

require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/url/strategies/VimeoUrlBuilderStrategy.class.php';

class org_tubepress_impl_url_strategies_VimeoUrlBuilderStrategyTest extends TubePressUnitTest {
    
    const PRE = "/http:\/\/vimeo.com\/api\/rest\/v2\?";
    const POST = "&format=php&oauth_consumer_key=vimeokey&oauth_nonce=[a-zA-Z0-9]+&oauth_signature_method=HMAC-SHA1&oauth_timestamp=[0-9]+&oauth_version=1.0&oauth_signature=[a-zA-Z0-9%]+/";
    
	private $_sut;
	
	function setUp()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_url_strategies_VimeoUrlBuilderStrategy();
	}

	public function getMock($className)
	{
		$mock = parent::getMock($className);

		switch ($className) {
			case 'org_tubepress_api_provider_ProviderCalculator':
				$mock->expects($this->any())
					->method('calculateProviderOfVideoId')
					->will($this->returnValue(org_tubepress_api_provider_Provider::VIMEO));
		}

		return $mock;
	}

	function testSingleVideoUrl()
	{
	    $this->setOptions(array(
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
		$this->assertTrue($this->urlMatches('method=vimeo.videos.getInfo&video_id=444333', 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::VIMEO, true, '444333')));
	}
	
	function testexecuteGroup()
	{
	    $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_GalleryContentMode::VIMEO_GROUP,
           org_tubepress_api_const_options_values_GalleryContentModeValue::VIMEO_GROUP_VALUE => 'eric',
           org_tubepress_api_const_options_names_Display::ORDER_BY => 'random',
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
             
		$this->assertTrue($this->urlMatches('method=vimeo.groups.getVideos&group_id=eric&full_response=true&page=1&per_page=20&sort=random', 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::VIMEO, false, 1)));
	}
	
	function testexecuteAlbum()
	{
        $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_GalleryContentMode::VIMEO_ALBUM,
           org_tubepress_api_const_options_values_GalleryContentModeValue::VIMEO_ALBUM_VALUE => 'eric',
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
        
		$this->assertTrue($this->urlMatches('method=vimeo.albums.getVideos&album_id=eric&full_response=true&page=1&per_page=20', 
		   $this->_sut->execute(org_tubepress_api_provider_Provider::VIMEO, false, 1)));
	}
	
	function testexecuteChannel()
	{
        $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_GalleryContentMode::VIMEO_CHANNEL,
           org_tubepress_api_const_options_values_GalleryContentModeValue::VIMEO_CHANNEL_VALUE => 'eric',
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
             
		$this->assertTrue($this->urlMatches('method=vimeo.channels.getVideos&channel_id=eric&full_response=true&page=1&per_page=20', 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::VIMEO, false, 1)));
	}
	
	function testexecuteCreditedTo()
	{
        $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_GalleryContentMode::VIMEO_CREDITED,
           org_tubepress_api_const_options_values_GalleryContentModeValue::VIMEO_CREDITED_VALUE => 'eric',
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
        
		$this->assertTrue($this->urlMatches('method=vimeo.videos.getAll&user_id=eric&full_response=true&page=1&per_page=20&sort=most_played', 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::VIMEO, false, 1)));
	}
	
	function testexecuteSearch()
	{
        $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_GalleryContentMode::VIMEO_SEARCH,
           org_tubepress_api_const_options_values_GalleryContentModeValue::VIMEO_SEARCH_VALUE => 'eric hough',
           org_tubepress_api_const_options_names_Display::ORDER_BY => 'relevance',
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
        $result = $this->_sut->execute(org_tubepress_api_provider_Provider::VIMEO, false, 1);
		$this->assertTrue($this->urlMatches('method=vimeo.videos.search&query=eric\+hough&full_response=true&page=1&per_page=20&sort=relevant', 
		    $result));
	}

	function testexecuteSearchWithUser()
	{
        $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_GalleryContentMode::VIMEO_SEARCH,
           org_tubepress_api_const_options_values_GalleryContentModeValue::VIMEO_SEARCH_VALUE => 'eric hough',
           org_tubepress_api_const_options_names_Display::ORDER_BY => 'relevance',
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret',
           org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => 'ehough'
        ));
        $result = $this->_sut->execute(org_tubepress_api_provider_Provider::VIMEO, false, 1);
		$this->assertTrue($this->urlMatches('method=vimeo.videos.search&query=eric\+hough&user_id=ehough&full_response=true&page=1&per_page=20&sort=relevant', 
		    $result));
	}
	
	function testexecuteAppearsIn()
	{
        $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_GalleryContentMode::VIMEO_APPEARS_IN,
           org_tubepress_api_const_options_values_GalleryContentModeValue::VIMEO_APPEARS_IN_VALUE => 'eric',
           org_tubepress_api_const_options_names_Display::ORDER_BY => 'oldest',
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
        
		$this->assertTrue($this->urlMatches('method=vimeo.videos.getAppearsIn&user_id=eric&full_response=true&page=1&per_page=20&sort=oldest', 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::VIMEO, false, 1)));
	}
	
	function testexecuteLikes()
	{
        $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_GalleryContentMode::VIMEO_LIKES,
           org_tubepress_api_const_options_values_GalleryContentModeValue::VIMEO_LIKES_VALUE => 'eric',
           org_tubepress_api_const_options_names_Display::ORDER_BY => 'rating',
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
        
		$this->assertTrue($this->urlMatches('method=vimeo.videos.getLikes&user_id=eric&full_response=true&page=1&per_page=20&sort=most_liked', 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::VIMEO, false, 1)));
	}
	
	function testexecuteUploadedBy()
	{
        $this->setOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_GalleryContentMode::VIMEO_UPLOADEDBY,
           org_tubepress_api_const_options_values_GalleryContentModeValue::VIMEO_UPLOADEDBY_VALUE => 'eric',
           org_tubepress_api_const_options_names_Display::ORDER_BY => 'commentCount',
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
	    
		$this->assertTrue($this->urlMatches('method=vimeo.videos.getUploaded&user_id=eric&full_response=true&page=1&per_page=20&sort=most_commented', 
		    $this->_sut->execute(org_tubepress_api_provider_Provider::VIMEO, false, 1)));
	}
		
	private function urlMatches($url, $full)
	{
		$pattern = self::PRE . $url . self::POST;
		$result = 1 === preg_match($pattern, $full);
		if (!$result) {
			echo "\n\n$full\n    does not match\n$pattern\n\n";
		}
		return $result;
	}
}

?>
