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
 * @covers tubepress_app_impl_listeners_template_pre_GalleryCorePreListener
 */
class tubepress_test_app_impl_listeners_template_pre_GalleryCorePreListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_listeners_template_pre_GalleryCorePreListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockContext = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockEvent   = $this->mock('tubepress_lib_api_event_EventInterface');

        $this->_sut = new tubepress_app_impl_listeners_template_pre_GalleryCorePreListener(
            $this->_mockContext
        );
    }

    public function testOnGalleryTemplatePreRender()
    {
        $expected = array(
            tubepress_app_api_template_VariableNames::HTML_WIDGET_ID              => 47,
            tubepress_app_api_template_VariableNames::GALLERY_THUMBNAIL_WIDTH_PX  => 556,
            tubepress_app_api_template_VariableNames::GALLERY_THUMBNAIL_HEIGHT_PX => 984,
        );

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(array('foo' => 'bar'));
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array_merge(array('foo' => 'bar'), $expected));

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::HTML_GALLERY_ID)->andReturn(47);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH)->andReturn(556);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT)->andReturn(984);

        $this->_sut->onGalleryTemplatePreRender($this->_mockEvent);

        $this->assertTrue(true);
    }
}