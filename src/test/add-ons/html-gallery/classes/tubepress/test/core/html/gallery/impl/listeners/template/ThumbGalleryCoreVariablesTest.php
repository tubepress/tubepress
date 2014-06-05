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
 * @covers tubepress_core_html_gallery_impl_listeners_template_CoreVariables
 */
class tubepress_test_core_html_gallery_impl_listeners_template_ThumbGalleryCoreVariablesTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_html_gallery_impl_listeners_template_CoreVariables
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_sut = new tubepress_core_html_gallery_impl_listeners_template_CoreVariables($this->_mockExecutionContext);
    }

    public function testAlterTemplate()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_gallery_api_Constants::OPTION_THUMB_WIDTH)->andReturn(556);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_gallery_api_Constants::OPTION_THUMB_HEIGHT)->andReturn(984);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_api_Constants::OPTION_GALLERY_ID)->andReturn(47);

        $providerResult = new tubepress_core_media_provider_api_Page();
        $providerResult->setItems(array('video-array'));

        $mockTemplate = $this->mock('tubepress_core_template_api_TemplateInterface');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_template_api_const_VariableNames::VIDEO_ARRAY, array('video-array'));
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_template_api_const_VariableNames::GALLERY_ID, 47);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_template_api_const_VariableNames::THUMBNAIL_WIDTH, 556);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_template_api_const_VariableNames::THUMBNAIL_HEIGHT, 984);

        $event = $this->mock('tubepress_core_event_api_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn($mockTemplate);
        $event->shouldReceive('getArgument')->once()->with('page')->andReturn($providerResult);

        $this->_sut->onGalleryTemplate($event);

        $this->assertTrue(true);
    }
}