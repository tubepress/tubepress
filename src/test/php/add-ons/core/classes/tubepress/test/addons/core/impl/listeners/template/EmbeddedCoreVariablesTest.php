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
 * @covers tubepress_addons_core_impl_listeners_template_EmbeddedCoreVariables
 */
class tubepress_test_addons_core_impl_listeners_template_EmbeddedCoreVariablesTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_template_EmbeddedCoreVariables
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
        $this->_mockEnvironmentDetector = ehough_mockery_Mockery::mock(tubepress_api_environment_EnvironmentInterface::_);
        $this->_sut = new tubepress_addons_core_impl_listeners_template_EmbeddedCoreVariables($this->_mockEnvironmentDetector);

        $this->_mockExecutionContext    = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
    }

    public function testAlter()
    {
        $mockBaseUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockBaseUrl->shouldReceive('toString')->once()->andReturn('<tubepress_base_url>');
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);

        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::AUTOPLAY)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH)->andReturn(660);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT)->andReturn(732);

        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::EMBEDDED_DATA_URL, 'dddd');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL, '<tubepress_base_url>');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::EMBEDDED_AUTOSTART, 'false');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::EMBEDDED_WIDTH, 660);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::EMBEDDED_HEIGHT, 732);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::VIDEO_ID, 'video-id');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::VIDEO_DOM_ID, ehough_mockery_Mockery::on(function ($arg) {

            return preg_match('/^tubepress-video-object-[0-9]+$/', $arg) === 1;
        }));
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::EMBEDDED_IMPL_NAME, 'embedded-impl-name');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::VIDEO_PROVIDER_NAME, 'video-provider-name');

        $mockDataUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $mockDataUrl->shouldReceive('toString')->once()->andReturn('dddd');

        $event = new tubepress_spi_event_EventBase($mockTemplate);
        $event->setArguments(array(

            'dataUrl' => $mockDataUrl,
            'videoId' => 'video-id',
            'providerName' => 'video-provider-name',
            'embeddedImplementationName' => 'embedded-impl-name'
        ));

        $this->_sut->onEmbeddedTemplate($event);
        $this->assertEquals($mockTemplate, $event->getSubject());
    }
}