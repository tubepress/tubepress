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
class tubepress_addons_core_impl_filters_gallerytemplate_VideoMetaTest extends TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_filters_gallerytemplate_VideoMeta
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    function onSetup()
    {
        $this->_sut = new tubepress_addons_core_impl_filters_gallerytemplate_VideoMeta();

        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
    }

    function testVideoMetaAboveAndBelow()
    {
        $messageService = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);
        $messageService->shouldReceive('_')->atLeast()->once()->andReturnUsing(function ($msg) {
              return "##$msg##";
        });

        $metaNames  = tubepress_impl_util_LangUtils::getDefinedConstants('tubepress_api_const_options_names_Meta');
        $shouldShow = array();
        $labels     = array();

        $mockOdr = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);

        $mockOdr->shouldReceive('findOneByName')->times(17)->andReturnUsing(function ($m) {

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

        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::META_SHOULD_SHOW, $shouldShow);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::META_LABELS, $labels);

        $providerResult = new tubepress_api_video_VideoGalleryPage();

        $event = new tubepress_api_event_TubePressEvent($mockTemplate);
        $event->setArguments(array(

            'page' => 1,
            'providerName' => 'youtube',
            'videoGalleryPage' => $providerResult
        ));

        $this->_sut->onGalleryTemplate($event);

        $this->assertEquals($mockTemplate, $event->getSubject());
    }
}

