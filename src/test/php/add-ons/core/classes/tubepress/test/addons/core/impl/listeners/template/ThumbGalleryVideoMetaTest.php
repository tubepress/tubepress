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

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionProvider;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMetaNameProvider;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMessageService;

    public function onSetup()
    {
        $this->_mockMessageService = ehough_mockery_Mockery::mock(tubepress_api_translation_TranslatorInterface::_);
        $this->_mockExecutionContext = ehough_mockery_Mockery::mock(tubepress_api_options_ContextInterface::_);
        $this->_mockOptionProvider   = $this->createMockSingletonService(tubepress_api_options_ProviderInterface::_);
        $this->_mockMetaNameProvider = $this->createMockSingletonService(tubepress_addons_core_impl_options_MetaOptionNameService::_);
        $this->_sut = new tubepress_addons_core_impl_listeners_template_ThumbGalleryVideoMeta(
            $this->_mockExecutionContext, $this->_mockMessageService, $this->_mockOptionProvider);
    }

    public function testVideoMetaAboveAndBelow()
    {
        $metaNames      = array('x', 'y', 'z');
        $this->_mockMetaNameProvider->shouldReceive('getAllMetaOptionNames')->once()->andReturn($metaNames);
        $shouldShow     = array();
        $labels         = array();

        foreach ($metaNames as $metaName) {

            $shouldShow[$metaName] = "<<value of $metaName>>";
            $labels[$metaName]     = '##video-' . $metaName . '##';

            $this->_mockExecutionContext->shouldReceive('get')->once()->with($metaName)->andReturn("<<value of $metaName>>");
            $this->_mockOptionProvider->shouldReceive('hasOption')->once()->with($metaName)->andReturn(true);
            $this->_mockOptionProvider->shouldReceive('getLabel')->once()->with($metaName)->andReturn("video-$metaName");
            $this->_mockMessageService->shouldReceive('_')->once()->with("video-$metaName")->andReturn("##video-$metaName##");
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

