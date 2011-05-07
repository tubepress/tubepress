<?php

require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/provider/SimpleProvider.class.php';
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/api/video/Video.class.php';

class org_tubepress_impl_provider_ProviderTest extends TubePressUnitTest
{
    private $_sut;
    private static $_totalResultCount;
    private static $_queryResultCount;
    private $_fakeVideo;
    private $_fakeCount;

    function setup()
    {
        self::$_totalResultCount = 100;
        self::$_queryResultCount = 20;
        $this->initFakeIoc();
        $this->_fakeCount = new org_tubepress_api_provider_ProviderResult();
        $this->_fakeCount->setEffectiveTotalResultCount(self::$_totalResultCount);
        $this->_sut = new org_tubepress_impl_provider_SimpleProvider();
        org_tubepress_impl_log_Log::setEnabled(false, array());
        $this->_fakeVideo = new org_tubepress_api_video_Video();
    }

    public function getMock($className)
    {
        $mock = parent::getMock($className);
        if ($className == 'org_tubepress_api_feed_FeedInspector') {
            $mock->expects($this->once())
            ->method('getTotalResultCount')
            ->will($this->returnValue($this->_fakeCount));
        }
        if ($className == 'org_tubepress_api_factory_VideoFactory') {
            $mock->expects($this->any())
            ->method('feedToVideoArray')
            ->will($this->returnValue(array($this->_fakeVideo)));
        }
        return $mock;
    }

    //    function testCurrentProviderDirectory()
    //    {
    //	$this->setOptions(array(org_tubepress_api_const_options_names_Output::MODE => 'directorySomething'));
    //	$tpom = org_tubepress_impl_ioc_IocContainer::getInstance()->get('org_tubepress_api_options_OptionsManager');
    //	$this->assertTrue($this->_sut->calculateCurrentVideoProvider($tpom) == org_tubepress_api_provider_Provider::DIRECTORY);
    //    }
    //
    //    function testCurrentProviderVimeo()
    //    {
    //	$this->setOptions(array(org_tubepress_api_const_options_names_Output::MODE => 'vimeoSomething'));
    //	$tpom = org_tubepress_impl_ioc_IocContainer::getInstance()->get('org_tubepress_api_options_OptionsManager');
    //	$this->assertTrue($this->_sut->calculateCurrentVideoProvider($tpom) == org_tubepress_api_provider_Provider::VIMEO);
    //    }
    //
    //    function testCurrentProviderWithVideoIdSet()
    //    {
    //	$this->setOptions(array(org_tubepress_api_const_options_names_Output::VIDEO => 'something'));
    //	$tpom = org_tubepress_impl_ioc_IocContainer::getInstance()->get('org_tubepress_api_options_OptionsManager');
    //	$this->assertTrue($this->_sut->calculateCurrentVideoProvider($tpom) == org_tubepress_api_provider_Provider::YOUTUBE);
    //    }
    //
    //    function testCalculatesYouTubeIdsCorrectly()
    //    {
    //	$this->assertTrue($this->_sut->calculateProviderOfVideoId('dffo0343r03r') == org_tubepress_api_provider_Provider::YOUTUBE);
    //	$this->assertTrue($this->_sut->calculateProviderOfVideoId('sdf783jfj39f') == org_tubepress_api_provider_Provider::YOUTUBE);
    //	$this->assertFalse($this->_sut->calculateProviderOfVideoId('12345') == org_tubepress_api_provider_Provider::YOUTUBE);
    //    }
    //
    //    function testCalculatesLocalIdsCorrectly()
    //    {
    //	$this->assertTrue($this->_sut->calculateProviderOfVideoId('test.mov') == org_tubepress_api_provider_Provider::DIRECTORY);
    //	$this->assertTrue($this->_sut->calculateProviderOfVideoId('something/something/poo.wmv') == org_tubepress_api_provider_Provider::DIRECTORY);
    //	$this->assertFalse($this->_sut->calculateProviderOfVideoId('12345') == org_tubepress_api_provider_Provider::DIRECTORY);
    //    }
    //
    //    function testCalculatesVimeoIdsCorrectly()
    //    {
    //	$this->assertTrue($this->_sut->calculateProviderOfVideoId(445533434) == org_tubepress_api_provider_Provider::VIMEO);
    //	$this->assertTrue($this->_sut->calculateProviderOfVideoId('445533434') == org_tubepress_api_provider_Provider::VIMEO);
    //	$this->assertFalse($this->_sut->calculateProviderOfVideoId('testing') == org_tubepress_api_provider_Provider::VIMEO);
    //    }

    function testGetSingleVideo()
    {
        $result = $this->_sut->getSingleVideo('something');
        $this->assertEquals($this->_fakeVideo, $result);
    }

    function testGetMultipleVideos()
    {
        $this->setOptions(array(org_tubepress_api_const_options_names_Display::ORDER_BY => 'random'));
        $result = $this->_sut->getMultipleVideos();
        $this->assertTrue(is_a($result, 'org_tubepress_api_provider_ProviderResult'));
    }
}

