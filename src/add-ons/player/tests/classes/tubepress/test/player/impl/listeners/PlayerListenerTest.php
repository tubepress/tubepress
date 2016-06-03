<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_player_impl_listeners_PlayerListener
 */
class tubepress_test_player_impl_listeners_PlayerListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_player_impl_listeners_PlayerListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContext;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockTemplating;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEvent;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockMediaPage;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockMediaItem;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockPlayerLocation1;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockPlayerLocation2;

    public function onSetup()
    {
        $this->_mockContext         = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockTemplating      = $this->mock(tubepress_api_template_TemplatingInterface::_);
        $this->_mockEvent           = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockMediaPage       = $this->mock('tubepress_api_media_MediaPage');
        $this->_mockMediaItem       = $this->mock('tubepress_api_media_MediaItem');
        $this->_mockPlayerLocation1 = $this->mock('tubepress_spi_player_PlayerLocationInterface');
        $this->_mockPlayerLocation2 = $this->mock('tubepress_spi_player_PlayerLocationInterface');

        $this->_sut = new tubepress_player_impl_listeners_PlayerListener(
            $this->_mockContext,
            $this->_mockTemplating
        );

        $this->_mockPlayerLocation1->shouldReceive('getName')->atLeast(1)->andReturn('player1-name');
        $this->_mockPlayerLocation2->shouldReceive('getName')->atLeast(1)->andReturn('player2-name');

        $this->_sut->setPlayerLocations(array($this->_mockPlayerLocation1, $this->_mockPlayerLocation2));
    }

    public function testOnAjaxPlayerTemplateSelection()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::PLAYER_LOCATION)->andReturn('player2-name');
        $this->_mockPlayerLocation2->shouldReceive('getAjaxTemplateName')->once()->andReturn('abc');

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('abc');

        $this->_sut->onAjaxPlayerTemplateSelection($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testOnStaticPlayerTemplateSelection()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::PLAYER_LOCATION)->andReturn('player2-name');
        $this->_mockPlayerLocation2->shouldReceive('getStaticTemplateName')->once()->andReturn('abc');

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('abc');

        $this->_sut->onStaticPlayerTemplateSelection($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testOnGalleryInitJs()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::PLAYER_LOCATION)->andReturn('player2-name');

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturnNull();
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array('options' => array(
            tubepress_api_options_Names::PLAYER_LOCATION => 'player2-name',
        )));

        $this->_sut->onGalleryInitJs($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testOnAcceptableValues()
    {
        $this->_mockPlayerLocation1->shouldReceive('getUntranslatedDisplayName')->once()->andReturn('player 1 name');
        $this->_mockPlayerLocation2->shouldReceive('getUntranslatedDisplayName')->once()->andReturn('player 2 name');

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturnNull();
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array(
            'player1-name' => 'player 1 name',
            'player2-name' => 'player 2 name',
        ));

        $this->_sut->onAcceptableValues($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testOnGalleryTemplatePreRender()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::PLAYER_LOCATION)->andReturn('player2-name');
        $this->_mockPlayerLocation2->shouldReceive('getStaticTemplateName')->once()->andReturn('xyz');
        $this->_mockMediaPage->shouldReceive('getItems')->once()->andReturn(array($this->_mockMediaItem));

        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('gallery/player/static', array('mediaItem' => $this->_mockMediaItem))->andReturn('static-player');

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(array('mediaPage' => $this->_mockMediaPage));
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array(
            'mediaPage'                                       => $this->_mockMediaPage,
            tubepress_api_template_VariableNames::PLAYER_HTML => 'static-player',
        ));

        $this->_sut->onGalleryTemplatePreRender($this->_mockEvent);

        $this->assertTrue(true);
    }
}
