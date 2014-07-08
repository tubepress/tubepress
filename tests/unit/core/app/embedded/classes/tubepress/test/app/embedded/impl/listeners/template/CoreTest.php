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
 * @covers tubepress_app_embedded_impl_listeners_template_Core
 */
class tubepress_test_app_embedded_impl_listeners_template_CoreTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_embedded_impl_listeners_template_Core
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
        $this->_mockEnvironmentDetector = $this->mock(tubepress_app_environment_api_EnvironmentInterface::_);

        $this->_mockExecutionContext    = $this->mock(tubepress_app_options_api_ContextInterface::_);
        $this->_sut = new tubepress_app_embedded_impl_listeners_template_Core(
            $this->_mockExecutionContext,
            $this->_mockEnvironmentDetector
        );
    }

    public function testAlter()
    {
        $mockBaseUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockBaseUrl->shouldReceive('toString')->once()->andReturn('<tubepress_lib_url>');
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);

        $mockTemplate = $this->mock('tubepress_lib_template_api_TemplateInterface');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_embedded_api_Constants::OPTION_AUTOPLAY)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_WIDTH)->andReturn(660);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT)->andReturn(732);

        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_app_embedded_api_Constants::TEMPLATE_VAR_DATA_URL, 'dddd');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_app_embedded_api_Constants::TEMPLATE_VAR_TUBEPRESS_BASE_URL, '<tubepress_lib_url>');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_app_embedded_api_Constants::TEMPLATE_VAR_AUTOSTART, 'false');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_app_embedded_api_Constants::TEMPLATE_VAR_WIDTH, 660);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_app_embedded_api_Constants::TEMPLATE_VAR_HEIGHT, 732);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_app_embedded_api_Constants::TEMPLATE_VAR_MEDIA_ITEM_ID, 'video-id');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_app_embedded_api_Constants::TEMPLATE_VAR_MEDIA_ITEM_DOM_ID, ehough_mockery_Mockery::on(function ($arg) {

            return preg_match('/^tubepress-media-object-[0-9]+$/', $arg) === 1;
        }));
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_app_embedded_api_Constants::TEMPLATE_VAR_IMPL_NAME, 'embedded-impl-name');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_app_embedded_api_Constants::TEMPLATE_VAR_MEDIA_PROVIDER_NAME, 'video-provider-name');

        $mockDataUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockDataUrl->shouldReceive('toString')->once()->andReturn('dddd');

        $mockMediaProvider = $this->mock(tubepress_app_media_provider_api_MediaProviderInterface::_);
        $mockMediaProvider->shouldReceive('getName')->twice()->andReturn('video-provider-name');

        $mockEmbeddedProvider = $this->mock(tubepress_app_embedded_api_EmbeddedProviderInterface::_);
        $mockEmbeddedProvider->shouldReceive('getName')->once()->andReturn('embedded-impl-name');

        $event = $this->mock('tubepress_lib_event_api_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn($mockTemplate);
        $args = array(

            'dataUrl' => $mockDataUrl,
            'itemId' => 'video-id',
            'itemProvider' => $mockMediaProvider,
            'embeddedProvider' => $mockEmbeddedProvider
        );
        foreach ($args as $k => $v) {
            $event->shouldReceive('getArgument')->once()->with($k)->andReturn($v);
        }

        $this->_sut->onEmbeddedTemplate($event);

        $this->assertTrue(true);
    }

    public function testBadColor()
    {
        $this->assertEquals('ff88dd', tubepress_app_embedded_impl_listeners_template_Core::getSafeColorValue('badcolor', 'ff88dd'));
    }

    public function testGoodColor()
    {
        $this->assertEquals('eecc33', tubepress_app_embedded_impl_listeners_template_Core::getSafeColorValue('eecc33', 'ff88dd'));
    }
}