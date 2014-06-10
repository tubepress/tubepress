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
 * @covers tubepress_core_html_single_impl_listeners_template_SingleVideoCoreVariables
 */
class tubepress_test_core_html_single_impl_listeners_template_SingleVideoCoreVariablesTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_html_single_impl_listeners_template_SingleVideoCoreVariables
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

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionReference;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTranslator;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMediaProvider;


    public function onSetup()
    {

        $this->_mockExecutionContext      = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockEmbeddedHtmlGenerator = $this->mock(tubepress_core_embedded_api_EmbeddedHtmlInterface::_);
        $this->_mockOptionReference   = $this->mock(tubepress_core_options_api_ReferenceInterface::_);
        $this->_mockMediaProvider = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);
        $this->_mockTranslator = $this->mock(tubepress_core_translation_api_TranslatorInterface::_);

        $this->_sut = new tubepress_core_html_single_impl_listeners_template_SingleVideoCoreVariables(
            $this->_mockExecutionContext,
            $this->_mockEmbeddedHtmlGenerator,
            $this->_mockOptionReference,
            $this->_mockTranslator
        );

        $this->_sut->setMediaProviders(array($this->_mockMediaProvider));
    }

    public function testYouTubeFavorites()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_WIDTH)->andReturn(889);

        $video = new tubepress_core_media_item_api_MediaItem('video-id');

        $this->_mockEmbeddedHtmlGenerator->shouldReceive('getHtml')->once()->with('video-id')->andReturn('embedded-html');

        $mockTemplate = $this->mock('tubepress_core_template_api_TemplateInterface');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_embedded_api_Constants::TEMPLATE_VAR_SOURCE, 'embedded-html');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_embedded_api_Constants::TEMPLATE_VAR_WIDTH, 889);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_html_single_api_Constants::TEMPLATE_VAR_MEDIA_ITEM, $video);

        $event = $this->mock('tubepress_core_event_api_EventInterface');
        $event->shouldReceive('getArgument')->once()->with('item')->andReturn($video);
        $event->shouldReceive('getSubject')->once()->andReturn($mockTemplate);

        $this->_testVideoMetaStuff($mockTemplate);

        $this->_sut->onSingleVideoTemplate($event);

        $this->assertTrue(true);
    }

    private function _testVideoMetaStuff(ehough_mockery_mockery_MockInterface $mockTemplate)
    {
        $this->_mockMediaProvider->shouldReceive('getMetaOptionNames')->once()->andReturn(array(

            'meta',
        ));

        $shouldShow = array('meta' => '<<value of meta>>');
        $labels     = array('meta' => '##video-meta##');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with('meta')->andReturn("<<value of meta>>");
        $this->_mockOptionReference->shouldReceive('optionExists')->once()->with('meta')->andReturn(true);
        $this->_mockOptionReference->shouldReceive('getUntranslatedLabel')->once()->with('meta')->andReturn('meta label!');
        $this->_mockTranslator->shouldReceive('_')->once()->with("meta label!")->andReturn("##video-meta##");

        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW, $shouldShow);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_media_item_api_Constants::TEMPLATE_VAR_META_LABELS, $labels);

    }
}

