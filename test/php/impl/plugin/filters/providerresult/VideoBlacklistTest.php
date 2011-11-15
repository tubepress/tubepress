<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/providerresult/VideoBlacklist.class.php';

class org_tubepress_impl_plugin_filters_providerresult_VideoBlacklistTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_plugin_filters_providerresult_VideoBlacklist();
	}

	function testYouTubeFavorites()
	{
	    $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context      = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::VIDEO_BLACKLIST)->andReturn('xxx');

        $mockVideo1 = \Mockery::mock('org_tubepress_api_video_Video');
        $mockVideo1->shouldReceive('getId')->once()->andReturn('p');

        $mockVideo2 = \Mockery::mock('org_tubepress_api_video_Video');
        $mockVideo2->shouldReceive('getId')->once()->andReturn('y');

        $mockVideo3 = \Mockery::mock('org_tubepress_api_video_Video');
        $mockVideo3->shouldReceive('getId')->once()->andReturn('xxx');

        $mockVideo4 = \Mockery::mock('org_tubepress_api_video_Video');
        $mockVideo4->shouldReceive('getId')->once()->andReturn('yyy');

        $videoArray = array($mockVideo1, $mockVideo2, $mockVideo3, $mockVideo4);

        $providerResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');
        $providerResult->shouldReceive('getVideoArray')->once()->andReturn($videoArray);
        $providerResult->shouldReceive('setVideoArray')->once()->with(array($mockVideo1, $mockVideo2, $mockVideo4));

        $this->assertEquals($providerResult, $this->_sut->alter_providerResult($providerResult, 'provider-name'));
	}

}

