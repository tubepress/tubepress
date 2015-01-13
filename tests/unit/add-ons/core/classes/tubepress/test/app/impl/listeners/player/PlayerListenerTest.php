<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_app_impl_listeners_player_PlayerListener
 */
class tubepress_test_app_impl_listeners_player_PlayerListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_listeners_player_PlayerListener
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
    private $_mockEvent;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMediaPage;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMediaItem;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPlayerLocation1;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPlayerLocation2;

    public function onSetup()
    {
        $this->_mockContext         = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockTemplating      = $this->mock(tubepress_lib_api_template_TemplatingInterface::_);
        $this->_mockEvent           = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockMediaPage       = $this->mock('tubepress_app_api_media_MediaPage');
        $this->_mockMediaItem       = $this->mock('tubepress_app_api_media_MediaItem');
        $this->_mockPlayerLocation1 = $this->mock('tubepress_app_api_player_PlayerLocationInterface');
        $this->_mockPlayerLocation2 = $this->mock('tubepress_app_api_player_PlayerLocationInterface');

        $this->_sut = new tubepress_app_impl_listeners_player_PlayerListener(
            $this->_mockContext,
            $this->_mockTemplating
        );

        $this->_mockPlayerLocation1->shouldReceive('getName')->atLeast(1)->andReturn('player1-name');
        $this->_mockPlayerLocation2->shouldReceive('getName')->atLeast(1)->andReturn('player2-name');

        $this->_sut->setPlayerLocations(array($this->_mockPlayerLocation1, $this->_mockPlayerLocation2));
    }

    public function testOnAjaxPlayerTemplateSelection()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::PLAYER_LOCATION)->andReturn('player2-name');
        $this->_mockPlayerLocation2->shouldReceive('getAjaxTemplateName')->once()->andReturn('abc');

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('abc');

        $this->_sut->onAjaxPlayerTemplateSelection($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testOnStaticPlayerTemplateSelection()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::PLAYER_LOCATION)->andReturn('player2-name');
        $this->_mockPlayerLocation2->shouldReceive('getStaticTemplateName')->once()->andReturn('abc');

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('abc');

        $this->_sut->onStaticPlayerTemplateSelection($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testOnGalleryInitJs()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::PLAYER_LOCATION)->andReturn('player2-name');

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturnNull();
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array('options' => array(
            tubepress_app_api_options_Names::PLAYER_LOCATION => 'player2-name',
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
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::PLAYER_LOCATION)->andReturn('player2-name');
        $this->_mockPlayerLocation2->shouldReceive('getStaticTemplateName')->once()->andReturn('xyz');
        $this->_mockMediaPage->shouldReceive('getItems')->once()->andReturn(array($this->_mockMediaItem));

        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('gallery/player/static', array('mediaItem' => $this->_mockMediaItem))->andReturn('static-player');

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(array('mediaPage' => $this->_mockMediaPage));
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array(
            'mediaPage' => $this->_mockMediaPage,
            tubepress_app_api_template_VariableNames::PLAYER_HTML => 'static-player',
        ));

        $this->_sut->onGalleryTemplatePreRender($this->_mockEvent);

        $this->assertTrue(true);
    }
}