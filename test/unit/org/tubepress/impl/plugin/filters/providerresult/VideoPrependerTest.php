<?php

require_once dirname(__FILE__) . '/../../../../../../../../sys/classes/org/tubepress/impl/plugin/filters/providerresult/VideoPrepender.class.php';

class org_tubepress_impl_plugin_filters_providerresult_VideoPrependerTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_plugin_filters_providerresult_VideoPrepender();
	}
	
	function testNoCustomVideo()
	{
	    $arr = new org_tubepress_api_provider_ProviderResult();
	    $result = $this->_sut->alter_providerResult(new org_tubepress_api_provider_ProviderResult(), 1);
	    $this->assertEquals($arr, $result);
	}
	
}

