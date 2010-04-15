<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/url/VimeoUrlBuilder.class.php';

class org_tubepress_url_VimeoUrlBuilderTest extends PHPUnit_Framework_TestCase {
    
    const PRE = "/http:\/\/vimeo.com\/api\/rest\/v2\?";
    const POST = "&format=php&oauth_consumer_key=86a1a3af34044829c435b2e0b03a8e6e&oauth_nonce=[a-zA-Z0-9]+&oauth_signature_method=HMAC-SHA1&oauth_timestamp=[0-9]+&oauth_version=1.0&oauth_signature=[a-zA-Z0-9]+/";
    
	private $_sut;
	private $_tpom;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_url_VimeoUrlBuilder();
		$this->_tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$this->_sut->setOptionsManager($this->_tpom);
	}

	function testSingleVideoUrl()
	{
		$this->assertTrue($this->urlMatches('method=vimeo.videos.getInfo&video_id=videoid', $this->_sut->buildSingleVideoUrl('videoid')));
	}
	
	function testBuildGalleryUrlGroup()
	{
		$this->_tpom->expects($this->exactly(4))
			 ->method("get")
			 ->will($this->returnCallback(array($this, 'groupCallback')));
		$this->assertTrue($this->urlMatches('method=vimeo.groups.getVideos&group_id=eric&full_response=true&page=1&per_page=99&sort=random', $this->_sut->buildGalleryUrl(1)));
	}
	
	function testBuildGalleryUrlAlbum()
	{
		$this->_tpom->expects($this->exactly(3))
			 ->method("get")
			 ->will($this->returnCallback(array($this, 'albumCallback')));
		$this->assertTrue($this->urlMatches('method=vimeo.albums.getVideos&album_id=eric&full_response=true&page=1&per_page=99', $this->_sut->buildGalleryUrl(1)));
	}
	
	function testBuildGalleryUrlChannel()
	{
		$this->_tpom->expects($this->exactly(3))
			 ->method("get")
			 ->will($this->returnCallback(array($this, 'channelCallback')));
		$this->assertTrue($this->urlMatches('method=vimeo.channels.getVideos&channel_id=eric&full_response=true&page=1&per_page=99', $this->_sut->buildGalleryUrl(1)));
	}
	
	function testBuildGalleryUrlCreditedTo()
	{
		$this->_tpom->expects($this->exactly(4))
			 ->method("get")
			 ->will($this->returnCallback(array($this, 'creditedToCallback')));
		$this->assertTrue($this->urlMatches('method=vimeo.videos.getAll&user_id=eric&full_response=true&page=1&per_page=99&sort=most_played', $this->_sut->buildGalleryUrl(1)));
	}
	
	function testBuildGalleryUrlSearch()
	{
		$this->_tpom->expects($this->exactly(4))
			 ->method("get")
			 ->will($this->returnCallback(array($this, 'searchCallback')));
		$this->assertTrue($this->urlMatches('method=vimeo.videos.search&query=eric&full_response=true&page=1&per_page=99&sort=relevant', $this->_sut->buildGalleryUrl(1)));
	}
	
	function testBuildGalleryUrlAppearsIn()
	{
		$this->_tpom->expects($this->exactly(4))
			 ->method("get")
			 ->will($this->returnCallback(array($this, 'appearsInCallback')));
		$this->assertTrue($this->urlMatches('method=vimeo.videos.getAppearsIn&user_id=eric&full_response=true&page=1&per_page=99&sort=oldest', $this->_sut->buildGalleryUrl(1)));
	}
	
	function testBuildGalleryUrlLikes()
	{
		$this->_tpom->expects($this->exactly(4))
			 ->method("get")
			 ->will($this->returnCallback(array($this, 'likesCallback')));
		$this->assertTrue($this->urlMatches('method=vimeo.videos.getLikes&user_id=eric&full_response=true&page=1&per_page=99&sort=most_liked', $this->_sut->buildGalleryUrl(1)));
	}
	
	function testBuildGalleryUrlUploadedBy()
	{
		$this->_tpom->expects($this->exactly(4))
			 ->method("get")
			 ->will($this->returnCallback(array($this, 'uploadedByCallback')));
		$this->assertTrue($this->urlMatches('method=vimeo.videos.getUploaded&user_id=eric&full_response=true&page=1&per_page=99&sort=most_commented', $this->_sut->buildGalleryUrl(1)));
	}
		
	private function urlMatches($url, $full)
	{
		$pattern = org_tubepress_url_VimeoUrlBuilderTest::PRE . $url . org_tubepress_url_VimeoUrlBuilderTest::POST;
		$result = 1 === preg_match($pattern, $full);
		if (!$result) {
			echo "\n$full does not match $pattern\n";
		}
		return $result;
	}
	
	public function groupCallback()
	{
		$args = func_get_args();
	   	$vals = array(
			org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::VIMEO_GROUP,
			org_tubepress_options_category_Gallery::VIMEO_GROUP_VALUE => 'eric',
			org_tubepress_options_category_Display::RESULTS_PER_PAGE => 99,
			org_tubepress_options_category_Display::ORDER_BY => 'random'
		);
		return $vals[$args[0]]; 
	}
	
	public function albumCallback()
	{
		$args = func_get_args();
	   	$vals = array(
			org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::VIMEO_ALBUM,
			org_tubepress_options_category_Gallery::VIMEO_ALBUM_VALUE => 'eric',
			org_tubepress_options_category_Display::RESULTS_PER_PAGE => 99,
			org_tubepress_options_category_Display::ORDER_BY => 'something'
		);
		return $vals[$args[0]]; 
	}
	
	public function channelCallback()
	{
		$args = func_get_args();
	   	$vals = array(
			org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::VIMEO_CHANNEL,
			org_tubepress_options_category_Gallery::VIMEO_CHANNEL_VALUE => 'eric',
			org_tubepress_options_category_Display::RESULTS_PER_PAGE => 99,
			org_tubepress_options_category_Display::ORDER_BY => 'something'
		);
		return $vals[$args[0]]; 
	}
	
	public function creditedToCallback()
	{
		$args = func_get_args();
	   	$vals = array(
			org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::VIMEO_CREDITED,
			org_tubepress_options_category_Gallery::VIMEO_CREDITED_VALUE => 'eric',
			org_tubepress_options_category_Display::RESULTS_PER_PAGE => 99,
			org_tubepress_options_category_Display::ORDER_BY => 'viewCount'
		);
		return $vals[$args[0]]; 
	}
	
	public function searchCallback()
	{
		$args = func_get_args();
	   	$vals = array(
			org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::VIMEO_SEARCH,
			org_tubepress_options_category_Gallery::VIMEO_SEARCH_VALUE => 'eric',
			org_tubepress_options_category_Display::RESULTS_PER_PAGE => 99,
			org_tubepress_options_category_Display::ORDER_BY => 'relevance'
		);
		return $vals[$args[0]]; 
	}
	
	public function appearsInCallback()
	{
		$args = func_get_args();
	   	$vals = array(
			org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::VIMEO_APPEARS_IN,
			org_tubepress_options_category_Gallery::VIMEO_APPEARS_IN_VALUE => 'eric',
			org_tubepress_options_category_Display::RESULTS_PER_PAGE => 99,
			org_tubepress_options_category_Display::ORDER_BY => 'oldest'
		);
		return $vals[$args[0]]; 
	}
	
	public function likesCallback()
	{
		$args = func_get_args();
	   	$vals = array(
			org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::VIMEO_LIKES,
			org_tubepress_options_category_Gallery::VIMEO_LIKES_VALUE => 'eric',
			org_tubepress_options_category_Display::RESULTS_PER_PAGE => 99,
			org_tubepress_options_category_Display::ORDER_BY => 'rating'
		);
		return $vals[$args[0]]; 
	}
	
	public function uploadedByCallback()
	{
		$args = func_get_args();
	   	$vals = array(
			org_tubepress_options_category_Gallery::MODE => org_tubepress_gallery_TubePressGallery::VIMEO_UPLOADEDBY,
			org_tubepress_options_category_Gallery::VIMEO_UPLOADEDBY_VALUE => 'eric',
			org_tubepress_options_category_Display::RESULTS_PER_PAGE => 99,
			org_tubepress_options_category_Display::ORDER_BY => 'commentCount'
		);
		return $vals[$args[0]]; 
	}
}

?>
