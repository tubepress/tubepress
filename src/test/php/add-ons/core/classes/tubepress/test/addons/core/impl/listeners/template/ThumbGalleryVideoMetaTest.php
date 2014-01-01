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
 * @covers tubepress_addons_core_impl_listeners_template_ThumbGalleryVideoMeta
 */
class tubepress_test_addons_core_impl_listeners_template_ThumbGalleryVideoMetaTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_template_ThumbGalleryVideoMeta
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_core_impl_listeners_template_ThumbGalleryVideoMeta();

        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
    }

    public function testVideoMetaAboveAndBelow()
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

        $event = new tubepress_spi_event_EventBase($mockTemplate);
        $event->setArguments(array(

            'page' => 1,
            'providerName' => 'youtube',
            'videoGalleryPage' => $providerResult
        ));

        $this->_sut->onGalleryTemplate($event);

        $this->assertEquals($mockTemplate, $event->getSubject());
    }
}

