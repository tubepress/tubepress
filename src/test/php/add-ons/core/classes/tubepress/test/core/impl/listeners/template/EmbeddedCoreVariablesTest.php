<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_core_impl_listeners_template_EmbeddedCoreVariables
 */
class tubepress_test_core_impl_listeners_template_EmbeddedCoreVariablesTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_listeners_template_EmbeddedCoreVariables
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    public function onSetup()
    {
        $this->_mockEnvironmentDetector = $this->mock(tubepress_core_api_environment_EnvironmentInterface::_);

        $this->_mockExecutionContext    = $this->mock(tubepress_core_api_options_ContextInterface::_);
        $this->_sut = new tubepress_core_impl_listeners_template_EmbeddedCoreVariables(
            $this->_mockExecutionContext,
            $this->_mockEnvironmentDetector
        );
    }

    public function testAlter()
    {
        $mockBaseUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockBaseUrl->shouldReceive('toString')->once()->andReturn('<tubepress_base_url>');
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);

        $mockTemplate = $this->mock('tubepress_core_api_template_TemplateInterface');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::AUTOPLAY)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::EMBEDDED_WIDTH)->andReturn(660);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::EMBEDDED_HEIGHT)->andReturn(732);

        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::EMBEDDED_DATA_URL, 'dddd');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::TUBEPRESS_BASE_URL, '<tubepress_base_url>');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::EMBEDDED_AUTOSTART, 'false');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::EMBEDDED_WIDTH, 660);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::EMBEDDED_HEIGHT, 732);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::VIDEO_ID, 'video-id');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::VIDEO_DOM_ID, ehough_mockery_Mockery::on(function ($arg) {

            return preg_match('/^tubepress-video-object-[0-9]+$/', $arg) === 1;
        }));
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::EMBEDDED_IMPL_NAME, 'embedded-impl-name');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::VIDEO_PROVIDER_NAME, 'video-provider-name');

        $mockDataUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockDataUrl->shouldReceive('toString')->once()->andReturn('dddd');

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn($mockTemplate);
        $args = array(

            'dataUrl' => $mockDataUrl,
            'videoId' => 'video-id',
            'providerName' => 'video-provider-name',
            'embeddedImplementationName' => 'embedded-impl-name'
        );
        foreach ($args as $k => $v) {
            $event->shouldReceive('getArgument')->once()->with($k)->andReturn($v);
        }

        $this->_sut->onEmbeddedTemplate($event);

        $this->assertTrue(true);
    }

    public function testBadColor()
    {
        $this->assertEquals('ff88dd', tubepress_core_impl_listeners_template_EmbeddedCoreVariables::getSafeColorValue('badcolor', 'ff88dd'));
    }

    public function testGoodColor()
    {
        $this->assertEquals('eecc33', tubepress_core_impl_listeners_template_EmbeddedCoreVariables::getSafeColorValue('eecc33', 'ff88dd'));
    }

    public function testBooleanToString()
    {
        $this->assertEquals('true', tubepress_core_impl_listeners_template_EmbeddedCoreVariables::booleanToString(true));
        $this->assertEquals('false', tubepress_core_impl_listeners_template_EmbeddedCoreVariables::booleanToString(false));
    }
}