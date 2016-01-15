<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_embedded_common_impl_listeners_EmbeddedListener
 */
class tubepress_test_embedded_common_impl_listeners_EmbeddedListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_embedded_common_impl_listeners_EmbeddedListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplating;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEmbeddedProvider1;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEmbeddedProvider2;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockIncomingEvent;

    public function onSetup()
    {
        $this->_mockContext           = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockTemplating        = $this->mock(tubepress_api_template_TemplatingInterface::_);
        $this->_mockEmbeddedProvider1 = $this->mock('tubepress_spi_embedded_EmbeddedProviderInterface');
        $this->_mockEmbeddedProvider2 = $this->mock('tubepress_spi_embedded_EmbeddedProviderInterface');
        $this->_mockIncomingEvent     = $this->mock('tubepress_api_event_EventInterface');

        $this->_sut = new tubepress_embedded_common_impl_listeners_EmbeddedListener(
            $this->_mockContext, $this->_mockTemplating
        );

        $this->_sut->setEmbeddedProviders(array($this->_mockEmbeddedProvider1, $this->_mockEmbeddedProvider2));
    }

    /**
     * @dataProvider getDataOnPlayerTemplatePreRender
     */
    public function testOnPlayerTemplatePreRender($requestedEmbeddedImplName,
                                                  $mediaProviderName,
                                                  array $compatibleNames1,
                                                  array $compatibleNames2)
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::EMBEDDED_PLAYER_IMPL)->andReturn($requestedEmbeddedImplName);

        $mockMediaItem     = $this->mock('tubepress_api_media_MediaItem');
        $mockMediaProvider = $this->mock('tubepress_api_media_MediaProvider');

        $this->_mockEmbeddedProvider1->shouldReceive('getName')->atLeast(1)->andReturn('provider-1');
        $this->_mockEmbeddedProvider2->shouldReceive('getName')->atLeast(1)->andReturn('provider-2');

        $this->_mockEmbeddedProvider1->shouldReceive('getCompatibleMediaProviderNames')->atLeast(1)->andReturn($compatibleNames1);
        $this->_mockEmbeddedProvider2->shouldReceive('getCompatibleMediaProviderNames')->atLeast(1)->andReturn($compatibleNames2);

        $this->_mockEmbeddedProvider2->shouldReceive('getTemplateVariables')->once()->andReturn(array('foo' => 'bar'));

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::EMBEDDED_WIDTH)->andReturn('embedded-width');
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::EMBEDDED_HEIGHT)->andReturn('embedded-height');
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::RESPONSIVE_EMBEDS)->andReturn('responsive');

        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('single/embedded', array(

            'embeddedProvider' => $this->_mockEmbeddedProvider2,
            'mediaItem'        => $mockMediaItem,
            'embeddedWidthPx'  => 'embedded-width',
            'embeddedHeightPx' => 'embedded-height',
            'responsiveEmbeds' => 'responsive',
            'foo'              => 'bar',
        ))->andReturn('hiya');

        $mockMediaItem->shouldReceive('getAttribute')->once()->with(tubepress_api_media_MediaItem::ATTRIBUTE_PROVIDER)->andReturn($mockMediaProvider);
        $mockMediaProvider->shouldReceive('getName')->once()->andReturn($mediaProviderName);

        $initialTemplateVars = array(

            tubepress_api_template_VariableNames::MEDIA_ITEM => $mockMediaItem,
        );

        $this->_mockIncomingEvent->shouldReceive('getSubject')->once()->andReturn($initialTemplateVars);
        $this->_mockIncomingEvent->shouldReceive('setSubject')->once()->with(array_merge($initialTemplateVars, array(
            'embeddedSource' => 'hiya',
            'embeddedWidthPx' => 'embedded-width',
            'embeddedHeightPx' => 'embedded-height',
            'responsiveEmbeds' => 'responsive',
        )));

        $this->_sut->onPlayerTemplatePreRender($this->_mockIncomingEvent);

        $this->assertTrue(true);
    }

    public function getDataOnPlayerTemplatePreRender()
    {
        return array(

            array('provider-2', 'provider-name', array('foo'), array('provider-name')),
            array('foobar', 'provider-name', array('foo'), array('provider-name')),
        );
    }

    public function testOnPlayerTemplatePreRenderError()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::EMBEDDED_PLAYER_IMPL)->andReturn('provider-2');

        $mockMediaItem     = $this->mock('tubepress_api_media_MediaItem');
        $mockMediaProvider = $this->mock('tubepress_api_media_MediaProvider');

        $this->_mockEmbeddedProvider1->shouldReceive('getName')->twice()->andReturn('provider-1');
        $this->_mockEmbeddedProvider2->shouldReceive('getName')->twice()->andReturn('provider-2');

        $this->_mockEmbeddedProvider1->shouldReceive('getCompatibleMediaProviderNames')->atLeast(1)->andReturn(array('foo'));
        $this->_mockEmbeddedProvider2->shouldReceive('getCompatibleMediaProviderNames')->atLeast(1)->andReturn(array('bar'));

        $this->setExpectedException('RuntimeException', 'No embedded providers could generate HTML for item abc');
        $mockMediaItem->shouldReceive('getId')->once()->andReturn('abc');

        $mockMediaItem->shouldReceive('getAttribute')->once()->with(tubepress_api_media_MediaItem::ATTRIBUTE_PROVIDER)->andReturn($mockMediaProvider);
        $mockMediaProvider->shouldReceive('getName')->once()->andReturn('provider-name');

        $initialTemplateVars = array(

            tubepress_api_template_VariableNames::MEDIA_ITEM => $mockMediaItem,
        );

        $this->_mockIncomingEvent->shouldReceive('getSubject')->once()->andReturn($initialTemplateVars);

        $this->_sut->onPlayerTemplatePreRender($this->_mockIncomingEvent);

        $this->assertTrue(true);
    }

    public function testOnEmbeddedTemplateSelect()
    {
        $this->_mockIncomingEvent->shouldReceive('hasArgument')->once()->with('embeddedProvider')->andReturn(true);
        $this->_mockIncomingEvent->shouldReceive('getArgument')->once()->with('embeddedProvider')->andReturn($this->_mockEmbeddedProvider2);
        $this->_mockIncomingEvent->shouldReceive('setSubject')->once()->with('xyz');

        $this->_mockEmbeddedProvider2->shouldReceive('getTemplateName')->once()->andReturn('xyz');

        $this->_sut->onEmbeddedTemplateSelect($this->_mockIncomingEvent);

        $this->assertTrue(true);
    }

    public function testOnEmbeddedTemplateSelectNoProvider()
    {
        $this->_mockIncomingEvent->shouldReceive('hasArgument')->once()->with('embeddedProvider')->andReturn(false);

        $this->_sut->onEmbeddedTemplateSelect($this->_mockIncomingEvent);

        $this->assertTrue(true);
    }

    public function testAcceptableValues()
    {
        $this->_mockEmbeddedProvider1->shouldReceive('getName')->once()->andReturn('provider-1');
        $this->_mockEmbeddedProvider2->shouldReceive('getName')->once()->andReturn('provider-2');
        $this->_mockEmbeddedProvider1->shouldReceive('getUntranslatedDisplayName')->once()->andReturn('provider 1');
        $this->_mockEmbeddedProvider2->shouldReceive('getUntranslatedDisplayName')->once()->andReturn('provider 2');

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array(
            'foo' => 'bar'
        ));

        $mockEvent->shouldReceive('setSubject')->once()->with(array(

            'provider_based' => 'Provider default',
            'foo'            => 'bar',
            'provider-1'     => 'provider 1',
            'provider-2'     => 'provider 2',
        ));

        $this->_sut->onAcceptableValues($mockEvent);

        $this->assertTrue(true);
    }
}