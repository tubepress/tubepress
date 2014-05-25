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
 * @covers tubepress_core_impl_listeners_template_ThumbGalleryCoreVariables
 */
class tubepress_test_core_impl_listeners_template_ThumbGalleryCoreVariablesTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_listeners_template_ThumbGalleryCoreVariables
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_core_api_options_ContextInterface::_);
        $this->_sut = new tubepress_core_impl_listeners_template_ThumbGalleryCoreVariables($this->_mockExecutionContext);
    }

    public function testAlterTemplate()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::THUMB_WIDTH)->andReturn(556);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::THUMB_HEIGHT)->andReturn(984);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::GALLERY_ID)->andReturn(47);

        $providerResult = new tubepress_core_api_video_VideoGalleryPage();
        $providerResult->setVideos(array('video-array'));

        $mockTemplate = $this->mock('tubepress_core_api_template_TemplateInterface');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::VIDEO_ARRAY, array('video-array'));
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::GALLERY_ID, 47);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::THUMBNAIL_WIDTH, 556);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_api_const_template_Variable::THUMBNAIL_HEIGHT, 984);

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn($mockTemplate);
        $event->shouldReceive('getArgument')->once()->with('videoGalleryPage')->andReturn($providerResult);

        $this->_sut->onGalleryTemplate($event);

        $this->assertTrue(true);
    }
}