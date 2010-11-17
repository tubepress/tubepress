<?php

require_once dirname(__FILE__) . '/../../../../../../../test/unit/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/video/feed/provider/SimpleProvider.class.php';

class org_tubepress_video_feed_provider_ProviderTest extends TubePressUnitTest
{
	private $_sut;
	private static $_totalResultCount;
	private static $_queryResultCount;

    function setup()
    {
	self::$_totalResultCount = 100;
	self::$_queryResultCount = 20;
	$this->initFakeIoc();
	$this->_sut = new org_tubepress_video_feed_provider_SimpleProvider();
        org_tubepress_log_Log::setEnabled(false, array());
    }

	public function getMock($className)
	{
		$mock = parent::getMock($className);
		if ($className == 'org_tubepress_api_feed_FeedInspector') {
			$mock->expects($this->once())
				->method('getTotalResultCount')
				->will($this->returnValue(self::$_totalResultCount));
			$mock->expects($this->once())
				->method('getQueryResultCount')
				->will($this->returnValue(self::$_queryResultCount));
		}
		if ($className == 'org_tubepress_api_feed_VideoFactory') {
			$mock->expects($this->any())
				->method('feedToVideoArray')
				->will($this->returnValue(array()));
			$mock->expects($this->any())
				->method('convertSingleVideo')
				->will($this->returnValue('foobar'));
		}	
		return $mock;
	}

    function testCurrentProviderDirectory()
    {
	$this->setOptions(array(org_tubepress_options_category_Gallery::MODE => 'directorySomething'));
	$tpom = org_tubepress_ioc_IocContainer::getInstance()->get('org_tubepress_options_manager_OptionsManager');
	$this->assertTrue($this->_sut->calculateCurrentVideoProvider($tpom) == org_tubepress_video_feed_provider_Provider::DIRECTORY);
    }

    function testCurrentProviderVimeo()
    {
	$this->setOptions(array(org_tubepress_options_category_Gallery::MODE => 'vimeoSomething'));
	$tpom = org_tubepress_ioc_IocContainer::getInstance()->get('org_tubepress_options_manager_OptionsManager');
	$this->assertTrue($this->_sut->calculateCurrentVideoProvider($tpom) == org_tubepress_video_feed_provider_Provider::VIMEO);
    }

    function testCurrentProviderWithVideoIdSet()
    {
	$this->setOptions(array(org_tubepress_options_category_Gallery::VIDEO => 'something'));
	$tpom = org_tubepress_ioc_IocContainer::getInstance()->get('org_tubepress_options_manager_OptionsManager');
	$this->assertTrue($this->_sut->calculateCurrentVideoProvider($tpom) == org_tubepress_video_feed_provider_Provider::YOUTUBE);
    }
    
    function testCalculatesYouTubeIdsCorrectly()
    {
	$this->assertTrue($this->_sut->calculateProviderOfVideoId('dffo0343r03r') == org_tubepress_video_feed_provider_Provider::YOUTUBE);
	$this->assertTrue($this->_sut->calculateProviderOfVideoId('sdf783jfj39f') == org_tubepress_video_feed_provider_Provider::YOUTUBE);
	$this->assertFalse($this->_sut->calculateProviderOfVideoId('12345') == org_tubepress_video_feed_provider_Provider::YOUTUBE);
    }

    function testCalculatesLocalIdsCorrectly()
    {
	$this->assertTrue($this->_sut->calculateProviderOfVideoId('test.mov') == org_tubepress_video_feed_provider_Provider::DIRECTORY);
	$this->assertTrue($this->_sut->calculateProviderOfVideoId('something/something/poo.wmv') == org_tubepress_video_feed_provider_Provider::DIRECTORY);
	$this->assertFalse($this->_sut->calculateProviderOfVideoId('12345') == org_tubepress_video_feed_provider_Provider::DIRECTORY);
    }

    function testCalculatesVimeoIdsCorrectly()
    {
	$this->assertTrue($this->_sut->calculateProviderOfVideoId(445533434) == org_tubepress_video_feed_provider_Provider::VIMEO);
	$this->assertTrue($this->_sut->calculateProviderOfVideoId('445533434') == org_tubepress_video_feed_provider_Provider::VIMEO);
	$this->assertFalse($this->_sut->calculateProviderOfVideoId('testing') == org_tubepress_video_feed_provider_Provider::VIMEO);
    }

    function testGetSingleVideo()
    {
        $result = $this->_sut->getSingleVideo('something');
        $this->assertEquals('f', $result);
    }

    function testGetMultipleVideos()
    {
        $this->setOptions(array(org_tubepress_options_category_Display::ORDER_BY => 'random'));
        $result = $this->_sut->getMultipleVideos();
        $this->assertTrue(is_a($result, 'org_tubepress_api_feed_FeedResult'));
        $this->assertTrue($result->getEffectiveDisplayCount() === 20);
    }
}
?>
