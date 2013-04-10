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
class tubepress_addons_core_impl_filters_singlevideotemplate_CoreVariablesTest extends TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_filters_singlevideotemplate_CoreVariables
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEmbeddedHtmlGenerator;

	function onSetup()
	{
		$this->_sut = new tubepress_addons_core_impl_filters_singlevideotemplate_CoreVariables();

        $this->_mockExecutionContext      = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEmbeddedHtmlGenerator = $this->createMockSingletonService(tubepress_spi_embedded_EmbeddedHtmlGenerator::_);
	}

	function testYouTubeFavorites()
	{
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH)->andReturn(889);

        $video = new tubepress_api_video_Video();
        $video->setAttribute(tubepress_api_video_Video::ATTRIBUTE_ID, 'video-id');

        $this->_mockEmbeddedHtmlGenerator->shouldReceive('getHtml')->once()->with('video-id')->andReturn('embedded-html');

        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::EMBEDDED_SOURCE, 'embedded-html');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::EMBEDDED_WIDTH, 889);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::VIDEO, $video);

        $event = new tubepress_api_event_TubePressEvent($mockTemplate);

        $event->setArgument('video', $video);

        $this->_sut->onSingleVideoTemplate($event);

        $this->assertEquals($mockTemplate, $event->getSubject());
	}
}

