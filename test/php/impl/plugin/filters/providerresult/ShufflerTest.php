<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/providerresult/Shuffler.class.php';

class org_tubepress_impl_plugin_filters_providerresult_ShufflerTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_plugin_filters_providerresult_Shuffler();
	}

	function testYouTubeFavorites()
	{
	    $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context      = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn(org_tubepress_api_const_options_values_OrderByValue::RANDOM);

        $videoArray = array('x', 'y');

        $providerResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');
        $providerResult->shouldReceive('getVideoArray')->once()->andReturn($videoArray);
        $providerResult->shouldReceive('setVideoArray')->once();

        $this->assertEquals($providerResult, $this->_sut->alter_providerResult($providerResult, 'provider-name'));
	}

}

