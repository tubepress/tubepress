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
class tubepress_plugins_core_impl_filters_singlevideotemplate_VideoMetaTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockMessageService;

    private $_mockExecutionContext;

    private $_mockOptionDescriptorReference;

	function onSetup()
	{
        $this->_mockMessageService = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);

        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

        $this->_mockOptionDescriptorReference = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);

		$this->_sut = new tubepress_plugins_core_impl_filters_singlevideotemplate_VideoMeta();
	}

	function testYouTubeFavorites()
	{
	    $this->_mockMessageService->shouldReceive('_')->atLeast()->once()->andReturnUsing(function ($msg) {
	          return "##$msg##";
	    });

	    $metaNames  = tubepress_impl_util_LangUtils::getDefinedConstants('tubepress_api_const_options_names_Meta');
        $shouldShow = array();
        $labels     = array();

        $this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->times(17)->andReturnUsing(function ($m) {

             $mock = new tubepress_spi_options_OptionDescriptor($m);
             $mock->setLabel('video-' . $m);
             return $mock;
        });

        foreach ($metaNames as $metaName) {

            $shouldShow[$metaName] = "<<value of $metaName>>";
            $labels[$metaName]     = '##video-' . $metaName . '##';

            $this->_mockExecutionContext->shouldReceive('get')->once()->with($metaName)->andReturnUsing(function ($m) {
                   return "<<value of $m>>";
            });
        }

        $video = new tubepress_api_video_Video();

        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::META_SHOULD_SHOW, $shouldShow);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::META_LABELS, $labels);

        $event = new tubepress_api_event_TubePressEvent($mockTemplate);

        $event->setArgument('video', $video);

        $this->_sut->onSingleVideoTemplate($event);

        $this->assertEquals($mockTemplate, $event->getSubject());
	}

}

