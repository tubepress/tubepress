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
 * @covers tubepress_app_impl_listeners_galleryjs_OptionsListener<extended>
 */
class tubepress_test_app_impl_listeners_galleryjs_OptionsListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_listeners_galleryjs_OptionsListener
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
        $this->_mockExecutionContext = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockEvent            = $this->mock('tubepress_lib_api_event_EventInterface');

        $this->_sut = new tubepress_app_impl_listeners_galleryjs_OptionsListener(

            $this->_mockExecutionContext
        );
    }

    public function testGalleryInitJs()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_FLUID_THUMBS)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_AUTONEXT)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::HTTP_METHOD)->andReturn('HELLO');
        $this->_mockExecutionContext->shouldReceive('getEphemeralOptions')->once()->andReturn(array('x' => 'y', 'foo' => 'bar'));

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(array('yo' => 'mamma'));
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array(

            'yo' => 'mamma',

            'options' => array(

                tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION => true,
                tubepress_app_api_options_Names::GALLERY_FLUID_THUMBS    => false,
                tubepress_app_api_options_Names::GALLERY_AUTONEXT        => true,
                tubepress_app_api_options_Names::HTTP_METHOD             => 'HELLO',
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