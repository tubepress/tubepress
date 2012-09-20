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
class tubepress_plugins_core_filters_gallerytemplate_VideoMetaTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockExecutionContext;

	function setup()
	{
		$this->_sut = new tubepress_plugins_core_filters_gallerytemplate_VideoMeta();

        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
	}

	function testVideoMetaAboveAndBelow()
	{
        $messageService = Mockery::mock(tubepress_spi_message_MessageService::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setMessageService($messageService);
	    $messageService->shouldReceive('_')->atLeast()->once()->andReturnUsing(function ($msg) {
	          return "##$msg##";
	    });

	    $metaNames  = tubepress_impl_util_LangUtils::getDefinedConstants(tubepress_api_const_options_names_Meta::_);
        $shouldShow = array();
        $labels     = array();

        $mockOdr = Mockery::mock(tubepress_spi_options_OptionDescriptorReference::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionDescriptorReference($mockOdr);

        $mockOdr->shouldReceive('findOneByName')->times(17)->andReturnUsing(function ($m) {

             $mock = \Mockery::mock(tubepress_spi_options_OptionDescriptor::_);
             $mock->shouldReceive('getLabel')->once()->andReturn('video-' . $m);
             return $mock;
        });

        foreach ($metaNames as $metaName) {

            $shouldShow[$metaName] = "<<value of $metaName>>";
            $labels[$metaName]     = '##video-' . $metaName . '##';

            $this->_mockExecutionContext->shouldReceive('get')->once()->with($metaName)->andReturnUsing(function ($m) {
                   return "<<value of $m>>";
            });
        }

        $mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');
	    $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::META_SHOULD_SHOW, $shouldShow);
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::META_LABELS, $labels);

        $providerResult = new tubepress_api_video_VideoGalleryPage();

        $event = new tubepress_api_event_ThumbnailGalleryTemplateConstruction($mockTemplate);
        $event->setArguments(array(

            tubepress_api_event_ThumbnailGalleryTemplateConstruction::ARGUMENT_PAGE => 1,
            tubepress_api_event_ThumbnailGalleryTemplateConstruction::ARGUMENT_PROVIDER_NAME => tubepress_spi_provider_Provider::YOUTUBE,
            tubepress_api_event_ThumbnailGalleryTemplateConstruction::ARGUMENT_VIDEO_GALLERY_PAGE => $providerResult
        ));

        $this->_sut->onGalleryTemplate($event);

        $this->assertEquals($mockTemplate, $event->getSubject());
	}
}

