<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_addons_core_impl_listeners_template_ThumbGalleryCoreVariablesTest extends TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_template_ThumbGalleryCoreVariables
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_core_impl_listeners_template_ThumbGalleryCoreVariables();

        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
    }

    public function testAlterTemplate()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THUMB_WIDTH)->andReturn(556);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT)->andReturn(984);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn(47);

        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos(array('video-array'));

        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::VIDEO_ARRAY, array('video-array'));
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::GALLERY_ID, 47);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::THUMBNAIL_WIDTH, 556);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::THUMBNAIL_HEIGHT, 984);

        $event = new tubepress_api_event_TubePressEvent($mockTemplate);
        $event->setArguments(array(

            'page' => 1,
            'videoGalleryPage' => $providerResult,
            'providerName' => 'provider-name'
        ));

        $this->_sut->onGalleryTemplate($event);

        $this->assertEquals($mockTemplate, $event->getSubject());
    }
}