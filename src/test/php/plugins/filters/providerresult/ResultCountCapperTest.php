<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/providerresult/ResultCountCapper.class.php';

class org_tubepress_impl_plugin_filters_providerresult_ResultCountCapperTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_plugin_filters_providerresult_ResultCountCapper();
	}

	function testYouTubeFavorites()
	{
	    $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context      = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP)->andReturn(888);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE)->andReturn(org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES);

        $videoArray = array('x', 'y');

        $providerResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');
        $providerResult->shouldReceive('getEffectiveTotalResultCount')->once()->andReturn(999);
        $providerResult->shouldReceive('getVideoArray')->once()->andReturn($videoArray);
        $providerResult->shouldReceive('setEffectiveTotalResultCount')->once()->with(50);

        $this->assertEquals($providerResult, $this->_sut->alter_providerResult($providerResult, 'provider-name'));
	}

}

