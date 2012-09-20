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
class tubepress_plugins_core_filters_gallerytemplate_CoreVariablesTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockExecutionContext;

    public function setup()
    {
        $this->_sut = new tubepress_plugins_core_filters_gallerytemplate_CoreVariables();

        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
    }

    function testAlterTemplate()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THUMB_WIDTH)->andReturn(556);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT)->andReturn(984);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn(47);

        $providerResult = new tubepress_api_video_VideoGalleryPage();
        $providerResult->setVideos(array('video-array'));

        $mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::VIDEO_ARRAY, array('video-array'));
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::GALLERY_ID, 47);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::THUMBNAIL_WIDTH, 556);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::THUMBNAIL_HEIGHT, 984);

        $event = new tubepress_api_event_ThumbnailGalleryTemplateConstruction($mockTemplate);
        $event->setArguments(array(

            tubepress_api_event_ThumbnailGalleryTemplateConstruction::ARGUMENT_PAGE => 1,
            tubepress_api_event_ThumbnailGalleryTemplateConstruction::ARGUMENT_VIDEO_GALLERY_PAGE => $providerResult,
            tubepress_api_event_ThumbnailGalleryTemplateConstruction::ARGUMENT_PROVIDER_NAME => 'provider-name'
        ));

        $this->_sut->onGalleryTemplate($event);

        $this->assertEquals($mockTemplate, $event->getSubject());
    }
}