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
class tubepress_plugins_core_impl_filters_singlevideotemplate_VideoMetaTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockMessageService;

    private $_mockExecutionContext;

    private $_mockOptionDescriptorReference;

	function setup()
	{
        $this->_mockMessageService = Mockery::mock(tubepress_spi_message_MessageService::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setMessageService($this->_mockMessageService);

        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);

        $this->_mockOptionDescriptorReference = Mockery::mock(tubepress_api_service_options_OptionDescriptorReference::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionDescriptorReference($this->_mockOptionDescriptorReference);

		$this->_sut = new tubepress_plugins_core_impl_filters_singlevideotemplate_VideoMeta();
	}

	function testYouTubeFavorites()
	{
	    $this->_mockMessageService->shouldReceive('_')->atLeast()->once()->andReturnUsing(function ($msg) {
	          return "##$msg##";
	    });

	    $metaNames  = tubepress_impl_util_LangUtils::getDefinedConstants(tubepress_api_const_options_names_Meta::_);
        $shouldShow = array();
        $labels     = array();

        $this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->times(17)->andReturnUsing(function ($m) {

             $mock = new tubepress_api_model_options_OptionDescriptor($m);
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

        $mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::META_SHOULD_SHOW, $shouldShow);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::META_LABELS, $labels);

        $event = new tubepress_api_event_TubePressEvent($mockTemplate);

        $event->setArgument('video', $video);

        $this->_sut->onSingleVideoTemplate($event);

        $this->assertEquals($mockTemplate, $event->getSubject());
	}

}

