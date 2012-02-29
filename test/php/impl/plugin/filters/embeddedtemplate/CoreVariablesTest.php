<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/embeddedtemplate/CoreVariables.class.php';

class org_tubepress_impl_plugin_filters_embeddedtemplate_CoreVariablesTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_plugin_filters_embeddedtemplate_CoreVariables();
	}

	function testAlter()
	{
	    $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');

	    $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

	    $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::FULLSCREEN)->andReturn(true);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::PLAYER_COLOR)->andReturn('999999');
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT)->andReturn('FFFFFF');
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::AUTOPLAY)->andReturn(false);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH)->andReturn(660);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT)->andReturn(732);

        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_DATA_URL, 'http://tubepress.org');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL, '<tubepress_base_url>');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_AUTOSTART, 'false');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_WIDTH, 660);
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_HEIGHT, 732);
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_COLOR_PRIMARY, '999999');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_COLOR_HIGHLIGHT, 'FFFFFF');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_FULLSCREEN, 'true');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::VIDEO_ID, 'video-id');


	    $result = $this->_sut->alter_embeddedTemplate($mockTemplate, 'video-id', 'video-provider-name', new org_tubepress_api_url_Url('http://tubepress.org'), 'embedded-impl-name');

	    $this->assertEquals($mockTemplate, $result);
	}
}