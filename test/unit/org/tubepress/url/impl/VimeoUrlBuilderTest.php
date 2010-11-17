<?php

require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/url/impl/VimeoUrlBuilder.class.php';

class org_tubepress_url_impl_VimeoUrlBuilderTest extends TubePressUnitTest {
    
    const PRE = "/http:\/\/vimeo.com\/api\/rest\/v2\?";
    const POST = "&format=php&oauth_consumer_key=vimeokey&oauth_nonce=[a-zA-Z0-9]+&oauth_signature_method=HMAC-SHA1&oauth_timestamp=[0-9]+&oauth_version=1.0&oauth_signature=[a-zA-Z0-9%]+/";
    
	private $_sut;
	
	function setUp()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_url_impl_VimeoUrlBuilder();
	}

	public function getMock($className)
	{
		$mock = parent::getMock($className);

		switch ($className) {
			case 'org_tubepress_api_provider_Provider':
				$mock->expects($this->any())
					->method('calculateProviderOfVideoId')
					->will($this->returnValue(org_tubepress_api_provider_Provider::VIMEO));
		}

		return $mock;
	}

	function testSingleVideoUrl()
	{
	    $this->setOptions(array(
           org_tubepress_api_const_options_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
		$this->assertTrue($this->urlMatches('method=vimeo.videos.getInfo&video_id=444333', 
		    $this->_sut->buildSingleVideoUrl('444333')));
	}
	
	function testBuildGalleryUrlGroup()
	{
	    $this->setOptions(array(
           org_tubepress_api_const_options_Gallery::MODE => org_tubepress_api_gallery_Gallery::VIMEO_GROUP,
           org_tubepress_api_const_options_Gallery::VIMEO_GROUP_VALUE => 'eric',
           org_tubepress_api_const_options_Display::ORDER_BY => 'random',
           org_tubepress_api_const_options_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
             
		$this->assertTrue($this->urlMatches('method=vimeo.groups.getVideos&group_id=eric&full_response=true&page=1&per_page=20&sort=random', 
		    $this->_sut->buildGalleryUrl(1)));
	}
	
	function testBuildGalleryUrlAlbum()
	{
        $this->setOptions(array(
           org_tubepress_api_const_options_Gallery::MODE => org_tubepress_api_gallery_Gallery::VIMEO_ALBUM,
           org_tubepress_api_const_options_Gallery::VIMEO_ALBUM_VALUE => 'eric',
           org_tubepress_api_const_options_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
        
		$this->assertTrue($this->urlMatches('method=vimeo.albums.getVideos&album_id=eric&full_response=true&page=1&per_page=20', 
		   $this->_sut->buildGalleryUrl(1)));
	}
	
	function testBuildGalleryUrlChannel()
	{
        $this->setOptions(array(
           org_tubepress_api_const_options_Gallery::MODE => org_tubepress_api_gallery_Gallery::VIMEO_CHANNEL,
           org_tubepress_api_const_options_Gallery::VIMEO_CHANNEL_VALUE => 'eric',
           org_tubepress_api_const_options_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
             
		$this->assertTrue($this->urlMatches('method=vimeo.channels.getVideos&channel_id=eric&full_response=true&page=1&per_page=20', 
		    $this->_sut->buildGalleryUrl(1)));
	}
	
	function testBuildGalleryUrlCreditedTo()
	{
        $this->setOptions(array(
           org_tubepress_api_const_options_Gallery::MODE => org_tubepress_api_gallery_Gallery::VIMEO_CREDITED,
           org_tubepress_api_const_options_Gallery::VIMEO_CREDITED_VALUE => 'eric',
           org_tubepress_api_const_options_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
        
		$this->assertTrue($this->urlMatches('method=vimeo.videos.getAll&user_id=eric&full_response=true&page=1&per_page=20&sort=most_played', 
		    $this->_sut->buildGalleryUrl(1)));
	}
	
	function testBuildGalleryUrlSearch()
	{
        $this->setOptions(array(
           org_tubepress_api_const_options_Gallery::MODE => org_tubepress_api_gallery_Gallery::VIMEO_SEARCH,
           org_tubepress_api_const_options_Gallery::VIMEO_SEARCH_VALUE => 'eric hough',
           org_tubepress_api_const_options_Display::ORDER_BY => 'relevance',
           org_tubepress_api_const_options_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
        
		$this->assertTrue($this->urlMatches('method=vimeo.videos.search&query=eric\+hough&full_response=true&page=1&per_page=20&sort=relevant', 
		    $this->_sut->buildGalleryUrl(1)));
	}
	
	function testBuildGalleryUrlAppearsIn()
	{
        $this->setOptions(array(
           org_tubepress_api_const_options_Gallery::MODE => org_tubepress_api_gallery_Gallery::VIMEO_APPEARS_IN,
           org_tubepress_api_const_options_Gallery::VIMEO_APPEARS_IN_VALUE => 'eric',
           org_tubepress_api_const_options_Display::ORDER_BY => 'oldest',
           org_tubepress_api_const_options_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
        
		$this->assertTrue($this->urlMatches('method=vimeo.videos.getAppearsIn&user_id=eric&full_response=true&page=1&per_page=20&sort=oldest', 
		    $this->_sut->buildGalleryUrl(1)));
	}
	
	function testBuildGalleryUrlLikes()
	{
        $this->setOptions(array(
           org_tubepress_api_const_options_Gallery::MODE => org_tubepress_api_gallery_Gallery::VIMEO_LIKES,
           org_tubepress_api_const_options_Gallery::VIMEO_LIKES_VALUE => 'eric',
           org_tubepress_api_const_options_Display::ORDER_BY => 'rating',
           org_tubepress_api_const_options_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
        
		$this->assertTrue($this->urlMatches('method=vimeo.videos.getLikes&user_id=eric&full_response=true&page=1&per_page=20&sort=most_liked', 
		    $this->_sut->buildGalleryUrl(1)));
	}
	
	function testBuildGalleryUrlUploadedBy()
	{
        $this->setOptions(array(
           org_tubepress_api_const_options_Gallery::MODE => org_tubepress_api_gallery_Gallery::VIMEO_UPLOADEDBY,
           org_tubepress_api_const_options_Gallery::VIMEO_UPLOADEDBY_VALUE => 'eric',
           org_tubepress_api_const_options_Display::ORDER_BY => 'commentCount',
           org_tubepress_api_const_options_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_Feed::VIMEO_SECRET => 'vimeosecret'
        ));
	    
		$this->assertTrue($this->urlMatches('method=vimeo.videos.getUploaded&user_id=eric&full_response=true&page=1&per_page=20&sort=most_commented', 
		    $this->_sut->buildGalleryUrl(1)));
	}
		
	private function urlMatches($url, $full)
	{
		$pattern = org_tubepress_url_impl_VimeoUrlBuilderTest::PRE . $url . org_tubepress_url_impl_VimeoUrlBuilderTest::POST;
		$result = 1 === preg_match($pattern, $full);
		if (!$result) {
			echo "\n\n$full\n    does not match\n$pattern\n\n";
		}
		return $result;
	}
}

?>
