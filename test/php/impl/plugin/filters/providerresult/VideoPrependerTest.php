<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/providerresult/VideoPrepender.class.php';

class org_tubepress_impl_plugin_filters_providerresult_VideoPrependerTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_plugin_filters_providerresult_VideoPrepender();
	}

    function testCustomVideo()
	{
	    $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();

        $qss = $ioc->get('org_tubepress_api_querystring_QueryStringService');
        $qss->shouldReceive('getCustomVideo')->once()->andReturn('custom-video');

        $video = \Mockery::mock('org_tubepress_api_video_Video');
        $video->shouldReceive('getId')->once()->andReturn('video-id');

        $providerResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');
        $providerResult->shouldReceive('getVideoArray')->once()->andReturn(array($video));
        $providerResult->shouldReceive('setVideoArray')->once()->andReturn(array('x', $video));

        $provider = $ioc->get('org_tubepress_api_provider_Provider');
        $provider->shouldReceive('getSingleVideo')->once()->with('custom-video')->andReturn('x');

        $this->assertEquals($providerResult, $this->_sut->alter_providerResult($providerResult, 'provider-name'));
	}

	function testNoCustomVideo()
	{
	    $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();

        $qss = $ioc->get('org_tubepress_api_querystring_QueryStringService');
        $qss->shouldReceive('getCustomVideo')->once()->andReturn('');

        $providerResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');

        $this->assertEquals($providerResult, $this->_sut->alter_providerResult($providerResult, 'provider-name'));
	}

}

