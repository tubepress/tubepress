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
 * @covers tubepress_core_html_gallery_impl_listeners_template_VideoMeta
 */
class tubepress_test_core_html_gallery_impl_listeners_template_ThumbGalleryVideoMetaTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_html_gallery_impl_listeners_template_VideoMeta
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
    private $_mockMessageService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockVideoProvider;

    public function onSetup()
    {
        $this->_mockMessageService = $this->mock(tubepress_core_translation_api_TranslatorInterface::_);
        $this->_mockExecutionContext = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockOptionProvider   = $this->mock(tubepress_core_options_api_ReferenceInterface::_);
        $this->_mockVideoProvider = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);
        $this->_sut = new tubepress_core_html_gallery_impl_listeners_template_VideoMeta(
            $this->_mockExecutionContext,
            $this->_mockMessageService,
            $this->_mockOptionProvider
        );
        $this->_sut->setMediaProviders(array($this->_mockVideoProvider));
    }

    public function testVideoMetaAboveAndBelow()
    {
        $shouldShow = array('meta' => '<<value of meta>>');
        $labels     = array('meta' => '##video-meta##');

        $this->_mockVideoProvider->shouldReceive('getMetaOptionNames')->once()->andReturn(array(

            'meta',
        ));

        $this->_mockExecutionContext->shouldReceive('get')->once()->with('meta')->andReturn("<<value of meta>>");
        $this->_mockOptionProvider->shouldReceive('optionExists')->once()->with('meta')->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('getUntranslatedLabel')->once()->with('meta')->andReturn('meta label!');
        $this->_mockMessageService->shouldReceive('_')->once()->with("meta label!")->andReturn("##video-meta##");

        $mockTemplate = $this->mock('tubepress_core_template_api_TemplateInterface');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW, $shouldShow);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_template_api_const_VariableNames::META_LABELS, $labels);

        $event = $this->mock('tubepress_core_event_api_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn($mockTemplate);

        $this->_sut->onGalleryTemplate($event);

        $this->assertTrue(true);
    }
}

