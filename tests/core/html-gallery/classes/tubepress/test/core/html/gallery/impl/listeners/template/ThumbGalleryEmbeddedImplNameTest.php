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
 * @covers tubepress_core_html_gallery_impl_listeners_template_EmbeddedVars
 */
class tubepress_test_core_html_gallery_impl_listeners_template_ThumbGalleryEmbeddedImplNameTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_html_gallery_impl_listeners_template_EmbeddedVars
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    public function onSetup()
    {

        $this->_mockExecutionContext = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_sut = new tubepress_core_html_gallery_impl_listeners_template_EmbeddedVars($this->_mockExecutionContext);
    }

    public function testAlterTemplateLongtailYouTube()
    {
        $this->_testCustomYouTube('longtail');
    }

    public function testAlterTemplateEmbedPlusYouTube()
    {
        $this->_testCustomYouTube('embedplus');
    }

    public function testAlterTemplateProviderDefault()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_embedded_api_Constants::OPTION_PLAYER_IMPL)->andReturn('player-impl');

        $video = new tubepress_core_media_item_api_MediaItem('id');
        $mockProvider = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);
        $mockProvider->shouldReceive('getName')->once()->andReturn('provider-name');
        $video->setAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER, $mockProvider);

        $providerResult = new tubepress_core_media_provider_api_Page();
        $providerResult->setItems(array($video));

        $mockTemplate = $this->mock('tubepress_core_template_api_TemplateInterface');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_template_api_const_VariableNames::EMBEDDED_IMPL_NAME, 'provider-name');

        $event = $this->mock('tubepress_core_event_api_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn($mockTemplate);
        $event->shouldReceive('getArgument')->once()->with('page')->andReturn($providerResult);

        $this->_sut->onGalleryTemplate($event);

        $this->assertTrue(true);
    }

    private function _testCustomYouTube($name)
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_embedded_api_Constants::OPTION_PLAYER_IMPL)->andReturn($name);

        $video = new tubepress_core_media_item_api_MediaItem('id');
        $mockProvider = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);
        $mockProvider->shouldReceive('getName')->twice()->andReturn($name);
        $video->setAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER, $mockProvider);

        $providerResult = new tubepress_core_media_provider_api_Page();
        $providerResult->setItems(array($video));

        $mockTemplate = $this->mock('tubepress_core_template_api_TemplateInterface');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_core_template_api_const_VariableNames::EMBEDDED_IMPL_NAME, $name);

        $event = $this->mock('tubepress_core_event_api_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn($mockTemplate);
        $event->shouldReceive('getArgument')->once()->with('page')->andReturn($providerResult);

        $this->_sut->onGalleryTemplate($event);

        $this->assertTrue(true);
    }
}

