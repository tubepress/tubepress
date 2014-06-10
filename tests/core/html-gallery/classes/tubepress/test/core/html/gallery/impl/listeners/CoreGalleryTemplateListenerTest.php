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
 * @covers tubepress_core_html_gallery_impl_listeners_CoreGalleryTemplateListener
 */
class tubepress_test_core_html_gallery_impl_listeners_CoreGalleryTemplateListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_html_gallery_impl_listeners_CoreGalleryTemplateListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPlayerHtmlGenerator;

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

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplate;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPlayerLocation;

    public function onSetup()
    {
        $this->_mockPlayerHtmlGenerator = $this->mock(tubepress_core_player_api_PlayerHtmlInterface::_);
        $this->_mockTranslator = $this->mock(tubepress_core_translation_api_TranslatorInterface::_);
        $this->_mockExecutionContext = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockOptionReference   = $this->mock(tubepress_core_options_api_ReferenceInterface::_);
        $this->_mockMediaProvider = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);
        $this->_mockEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $this->_mockTemplate = $this->mock('tubepress_core_template_api_TemplateInterface');
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn($this->_mockTemplate);
        $this->_mockPlayerLocation = $this->mock(tubepress_core_player_api_PlayerLocationInterface::_);

        $this->_sut = new tubepress_core_html_gallery_impl_listeners_CoreGalleryTemplateListener(
            $this->_mockExecutionContext, $this->_mockOptionReference,
            $this->_mockPlayerHtmlGenerator, $this->_mockTranslator);

        $this->_sut->setPlayerLocations(array($this->_mockPlayerLocation));
        $this->_sut->setMediaProviders(array($this->_mockMediaProvider));
    }

    public function testOnTemplate()
    {
        $this->_testEmbeddedImplName();
        $this->_testGalleryIdAndItems();
        $this->_testThumbSizes();
        $this->_testPlayerLocationStuff();
        $this->_testVideoMetaStuff();

        $this->_sut->onGalleryTemplate($this->_mockEvent);

        $this->assertTrue(true);
    }

    private function _testEmbeddedImplName()
    {
        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_embedded_api_Constants::TEMPLATE_VAR_IMPL_NAME, 'x');
    }

    private function _testVideoMetaStuff()
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

        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW, $shouldShow);
        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_META_LABELS, $labels);

    }

    public function _testGalleryIdAndItems()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_api_Constants::OPTION_GALLERY_ID)->andReturn(47);

        $providerResult = new tubepress_core_media_provider_api_Page();
        $providerResult->setItems(array('video-array'));

        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_VIDEO_ARRAY, array('video-array'));
        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_html_api_Constants::TEMPLATE_VAR_GALLERY_ID, 47);

        $this->_mockEvent->shouldReceive('getArgument')->once()->with('page')->andReturn($providerResult);
    }

    public function _testThumbSizes()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_gallery_api_Constants::OPTION_THUMB_WIDTH)->andReturn(556);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_gallery_api_Constants::OPTION_THUMB_HEIGHT)->andReturn(984);

        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_WIDTH, 556);
        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_html_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_HEIGHT, 984);
    }

    private function _testPlayerLocationStuff()
    {
        $this->_mockPlayerLocation->shouldReceive('getName')->once()->andReturn('player');
        $this->_mockPlayerLocation->shouldReceive('displaysHtmlOnInitialGalleryLoad')->once()->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->twice()->with(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION)->andReturn('player');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_api_Constants::OPTION_GALLERY_ID)->andReturn('gallery-id');

        $fakeVideo = new tubepress_core_media_item_api_MediaItem('id');

        $providerResult = new tubepress_core_media_provider_api_Page();
        $providerResult->setItems(array($fakeVideo));

        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_player_api_Constants::TEMPLATE_VAR_HTML, 'player-html');
        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_player_api_Constants::TEMPLATE_VAR_NAME, 'player');

        $this->_mockPlayerHtmlGenerator->shouldReceive('getHtml')->once()->with($fakeVideo, 'gallery-id')->andReturn('player-html');

        $this->_mockEvent->shouldReceive('getArgument')->once()->with('page')->andReturn($providerResult);
    }
}