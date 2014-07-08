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
 * @covers tubepress_app_feature_gallery_impl_listeners_js_JsOptionsListener<extended>
 */
class tubepress_test_app_feature_gallery_impl_listeners_js_JsOptionsListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_feature_gallery_impl_listeners_js_JsOptionsListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_app_options_api_ContextInterface::_);
        $this->_mockEvent            = $this->mock('tubepress_lib_event_api_EventInterface');

        $this->_sut = new tubepress_app_feature_gallery_impl_listeners_js_JsOptionsListener(

            $this->_mockExecutionContext
        );
    }

    public function testGalleryInitJs()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_feature_gallery_api_Constants::OPTION_AJAX_PAGINATION)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_feature_gallery_api_Constants::OPTION_FLUID_THUMBS)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_feature_gallery_api_Constants::OPTION_AUTONEXT)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_lib_http_api_Constants::OPTION_HTTP_METHOD)->andReturn('HELLO');
        $this->_mockExecutionContext->shouldReceive('getEphemeralOptions')->once()->andReturn(array('x' => 'y', 'foo' => 'bar'));

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(array('yo' => 'mamma'));
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array(

            'yo' => 'mamma',

            'options' => array(

                tubepress_app_feature_gallery_api_Constants::OPTION_AJAX_PAGINATION => true,
                tubepress_app_feature_gallery_api_Constants::OPTION_FLUID_THUMBS    => false,
                tubepress_app_feature_gallery_api_Constants::OPTION_AUTONEXT        => true,
                tubepress_lib_http_api_Constants::OPTION_HTTP_METHOD             => 'HELLO',
            ),

            'ephemeral' => array(

                'x'   => 'y',
                'foo' => 'bar'
            )
        ));

        $this->_sut->onGalleryInitJs($this->_mockEvent);

        $this->assertTrue(true);
    }
}