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
 * @covers tubepress_app_impl_listeners_embedded_EmbeddedTemplateListener
 */
class tubepress_test_app_impl_listeners_embedded_EmbeddedTemplateListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_listeners_embedded_EmbeddedTemplateListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_app_api_options_ContextInterface::_);

        $this->_sut = new tubepress_app_impl_listeners_embedded_EmbeddedTemplateListener(
            $this->_mockExecutionContext
        );
    }

    public function testAlter()
    {
        $mockDataUrl = $this->mock('tubepress_platform_api_url_UrlInterface');
        $event       = $this->mock('tubepress_lib_api_event_EventInterface');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::EMBEDDED_WIDTH)->andReturn(660);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::EMBEDDED_HEIGHT)->andReturn(732);

        $event->shouldReceive('getSubject')->once()->andReturn(array('x' => 'y'));
        $event->shouldReceive('getArgument')->once()->with('dataUrl')->andReturn($mockDataUrl);
        $event->shouldReceive('setSubject')->once()->with(array(
            'x' => 'y',
            tubepress_app_api_template_VariableNames::EMBEDDED_DATA_URL => $mockDataUrl,
            tubepress_app_api_template_VariableNames::EMBEDDED_WIDTH_PX => 660,
            tubepress_app_api_template_VariableNames::EMBEDDED_HEIGHT_PX => 732,
        ));

        $this->_sut->onEmbeddedTemplatePreRender($event);

        $this->assertTrue(true);
    }
}