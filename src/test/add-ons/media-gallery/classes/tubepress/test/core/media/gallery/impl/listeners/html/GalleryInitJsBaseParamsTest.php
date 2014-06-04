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
 * @covers tubepress_core_media_gallery_impl_listeners_html_GalleryInitJsBaseParams
 */
class tubepress_test_core_impl_listeners_cssjs_GalleryInitJsBaseParamsTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_media_gallery_impl_listeners_html_GalleryInitJsBaseParams
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
    private $_mockEnvironmentDetector;

    public function onSetup()
    {

        $this->_mockExecutionContext    = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockOptionProvider      = $this->mock(tubepress_core_options_api_ReferenceInterface::_);
        $this->_mockEnvironmentDetector = $this->mock(tubepress_core_environment_api_EnvironmentInterface::_);
        $this->_sut = new tubepress_core_media_gallery_impl_listeners_html_GalleryInitJsBaseParams(

            $this->_mockExecutionContext,
            $this->_mockOptionProvider,
            $this->_mockEnvironmentDetector
        );
    }

    public function testAlter()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_media_gallery_api_Constants::OPTION_AJAX_PAGINATION)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT)->andReturn(999);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_WIDTH)->andReturn(888);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_media_gallery_api_Constants::OPTION_FLUID_THUMBS)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->twice()->with(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION)->andReturn('player-loc');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_http_api_Constants::OPTION_HTTP_METHOD)->andReturn('some-http-method');
        $this->_mockExecutionContext->shouldReceive('getEphemeralOptions')->once()->andReturn(array('x' => 'y', 'foo' => 'bar'));

        $this->_mockOptionProvider->shouldReceive('hasOption')->once()->with(tubepress_core_media_gallery_api_Constants::OPTION_AJAX_PAGINATION)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('hasOption')->once()->with(tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('hasOption')->once()->with(tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_WIDTH)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('hasOption')->once()->with(tubepress_core_media_gallery_api_Constants::OPTION_FLUID_THUMBS)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('hasOption')->once()->with(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('hasOption')->once()->with(tubepress_core_http_api_Constants::OPTION_HTTP_METHOD)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('hasOption')->once()->with('x')->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('hasOption')->once()->with('foo')->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('hasOption')->once()->with('playerLocationJsUrl')->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('hasOption')->once()->with('playerLocationProducesHtml')->andReturn(false);

        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with(tubepress_core_media_gallery_api_Constants::OPTION_AJAX_PAGINATION)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with(tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT)->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with(tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_WIDTH)->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with(tubepress_core_media_gallery_api_Constants::OPTION_FLUID_THUMBS)->andReturn(true);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION)->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with(tubepress_core_http_api_Constants::OPTION_HTTP_METHOD)->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with('x')->andReturn(false);
        $this->_mockOptionProvider->shouldReceive('isBoolean')->once()->with('foo')->andReturn(false);

        $event = $this->mock('tubepress_core_event_api_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn(array('yo' => 'mamma'));
        $event->shouldReceive('setSubject')->once()->with(array(

            'yo' => 'mamma',

            'nvpMap' => array(

                'embeddedHeight' => 999,
                'embeddedWidth' => 888,
                'playerLocation' => 'player-loc',
                'x' => 'y',
                'foo' => 'bar'
            ),

            'jsMap' => array(

                'playerLocationJsUrl' => '/abc',
                'playerLocationProducesHtml' => true,
                'ajaxPagination' => true,
                'fluidThumbs' => false,
                'httpMethod' => 'some-http-method',
            )
        ));

        $mockPlayer = $this->mock(tubepress_core_player_api_PlayerLocationInterface::_);
        $mockPlayer->shouldReceive('getName')->andReturn('player-loc');
        $mockPlayer->shouldReceive('getPlayerJsUrl')->andReturn('/abc/');
        $mockPlayer->shouldReceive('producesHtml')->once()->andReturn(true);
        $this->_sut->setPluggablePlayerLocations(array($mockPlayer));

        $this->_sut->onGalleryInitJs($event);

        $this->assertTrue(true);
    }
}