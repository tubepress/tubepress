<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class org_tubepress_impl_plugin_filters_embeddedtemplate_CoreVariablesTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockExecutionContext;

	function setup()
	{
        global $tubepress_base_url;

        $tubepress_base_url = '<tubepress_base_url>';

		$this->_sut = new tubepress_plugins_core_filters_embeddedtemplate_CoreVariables();

        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
	}

	function testAlter()
	{
	    $mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::FULLSCREEN)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_COLOR)->andReturn('999999');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT)->andReturn('FFFFFF');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::AUTOPLAY)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH)->andReturn(660);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT)->andReturn(732);

        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_DATA_URL, 'http://tubepress.org');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL, '<tubepress_base_url>');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_AUTOSTART, 'false');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_WIDTH, 660);
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_HEIGHT, 732);
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_COLOR_PRIMARY, '999999');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_COLOR_HIGHLIGHT, 'FFFFFF');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_FULLSCREEN, 'true');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::VIDEO_ID, 'video-id');

        $event = new tubepress_api_event_EmbeddedTemplateConstruction($mockTemplate);
        $event->setArguments(array(

            tubepress_api_event_EmbeddedTemplateConstruction::ARGUMENT_DATA_URL => new ehough_curly_Url('http://tubepress.org'),
            tubepress_api_event_EmbeddedTemplateConstruction::ARGUMENT_VIDEO_ID => 'video-id',
            tubepress_api_event_EmbeddedTemplateConstruction::ARGUMENT_PROVIDER_NAME => 'video-provider-name',
            tubepress_api_event_EmbeddedTemplateConstruction::ARGUMENT_EMBEDDED_IMPLEMENTATION_NAME => 'embedded-impl-name'
        ));

        $this->_sut->onEmbeddedTemplate($event);
	    $this->assertEquals($mockTemplate, $event->getSubject());
	}

    function onTearDown()
    {
        global $tubepress_base_url;

        unset($tubepress_base_url);
    }
}