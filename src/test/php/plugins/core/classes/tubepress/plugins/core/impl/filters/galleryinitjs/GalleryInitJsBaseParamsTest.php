<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_impl_plugin_filters_galleryinitjs_GalleryInitJsBaseParamsTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockExecutionContext;

    private $_mockOptionDescriptorReference;

	function onSetup()
	{
		$this->_sut = new tubepress_plugins_core_impl_filters_galleryinitjs_GalleryInitJsBaseParams();

        $this->_mockExecutionContext          = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockOptionDescriptorReference = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
	}

    function onTearDown()
    {
        global $tubepress_base_url;

        unset($tubepress_base_url);
    }

	function testAlter()
	{
        global $tubepress_base_url;

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::AUTONEXT)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT)->andReturn(999);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH)->andReturn(888);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::FLUID_THUMBS)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->twice()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('player-loc');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::HTTP_METHOD)->andReturn('some-http-method');
        $this->_mockExecutionContext->shouldReceive('getCustomOptions')->once()->andReturn(array('x' => 'y', 'foo' => 'bar'));

        $this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->times(11)->andReturnUsing(function ($arg) {

           if ($arg === tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION) {

               $mockOdr = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION);
               $mockOdr->setBoolean();

               return $mockOdr;
           }

            return null;
        });

        $event = new tubepress_api_event_TubePressEvent(array('yo' => 'mamma'));

        $mockPlayer = $this->createMockPluggableService(tubepress_spi_player_PluggablePlayerLocationService::_);
        $mockPlayer->shouldReceive('getName')->andReturn('player-loc');
        $mockPlayer->shouldReceive('getRelativePlayerJsUrl')->andReturn('abc');
        $mockPlayer->shouldReceive('producesHtml')->once()->andReturn(true);

        $tubepress_base_url = 'xyz';

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

                'playerJsUrl' => 'xyz/abc',
                'playerLocationProducesHtml' => true,
                'ajaxPagination' => true,
                'autoNext' => true,
                'fluidThumbs' => false,
                'httpMethod' => 'some-http-method',
            )
	    ), $result);
	}
}