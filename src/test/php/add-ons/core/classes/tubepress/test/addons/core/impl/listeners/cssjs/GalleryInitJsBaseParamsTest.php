<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_addons_core_impl_listeners_cssjs_GalleryInitJsBaseParams
 */
class tubepress_test_addons_core_impl_listeners_cssjs_GalleryInitJsBaseParamsTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_cssjs_GalleryInitJsBaseParams
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionDescriptorReference;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_core_impl_listeners_cssjs_GalleryInitJsBaseParams();

        $this->_mockExecutionContext          = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockOptionDescriptorReference = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockEnvironmentDetector       = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
    }

    public function testAlter()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn('<tubepress_base_url>');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT)->andReturn(999);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH)->andReturn(888);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::FLUID_THUMBS)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->twice()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('player-loc');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::HTTP_METHOD)->andReturn('some-http-method');
        $this->_mockExecutionContext->shouldReceive('getCustomOptions')->once()->andReturn(array('x' => 'y', 'foo' => 'bar'));

        $this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->times(10)->andReturnUsing(function ($arg) {

           if ($arg === tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION) {

               $mockOdr = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION);
               $mockOdr->setBoolean();

               return $mockOdr;
           }

            return null;
        });

        $event = new tubepress_spi_event_EventBase(array('yo' => 'mamma'));

        $mockPlayer = ehough_mockery_Mockery::mock(tubepress_spi_player_PluggablePlayerLocationService::_);
        $mockPlayer->shouldReceive('getName')->andReturn('player-loc');
        $mockPlayer->shouldReceive('getRelativePlayerJsUrl')->andReturn('abc');
        $mockPlayer->shouldReceive('producesHtml')->once()->andReturn(true);
        $this->_sut->setPluggablePlayerLocations(array($mockPlayer));

        $this->_sut->onGalleryInitJs($event);

        $result = $event->getSubject();

        $this->assertEquals(array(

            'yo' => 'mamma',

            'nvpMap' => array(

                'embeddedHeight' => 999,
                'embeddedWidth' => 888,
                'playerLocation' => 'player-loc',
                'x' => 'y',
                'foo' => 'bar'
            ),

            'jsMap' => array(

                'playerLocationJsUrl' => '<tubepress_base_url>/abc',
                'playerLocationProducesHtml' => true,
                'ajaxPagination' => true,
                'fluidThumbs' => false,
                'httpMethod' => 'some-http-method',
            )
        ), $result);
    }
}