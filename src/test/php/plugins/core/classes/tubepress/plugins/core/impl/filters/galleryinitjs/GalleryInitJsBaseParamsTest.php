<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class org_tubepress_impl_plugin_filters_galleryinitjs_GalleryInitJsBaseParamsTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockExecutionContext;

	function setup()
	{
		$this->_sut = new tubepress_plugins_core_impl_filters_galleryinitjs_GalleryInitJsBaseParams();

        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
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
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT)->andReturn(999);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH)->andReturn(888);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::FLUID_THUMBS)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->twice()->with(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION)->andReturn('player-loc');
        $this->_mockExecutionContext->shouldReceive('toShortcode')->once()->andReturn(' + "&');

        $event = new tubepress_api_event_TubePressEvent(array('yo' => 'mamma'));

        $mockPlayer = Mockery::mock(tubepress_spi_player_PluggablePlayerLocationService::_);
        $mockPlayer->shouldReceive('getName')->andReturn('player-loc');
        $mockPlayer->shouldReceive('getRelativePlayerJsUrl')->andReturn('abc');
        $mockPlayer->shouldReceive('producesHtml')->once()->andReturn(true);
        $mockPlayers = array($mockPlayer);

        $tubepress_base_url = 'xyz';

        $mockServiceCollectionsRegistry = Mockery::mock(tubepress_spi_patterns_sl_ServiceCollectionsRegistry::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setServiceCollectionsRegistry($mockServiceCollectionsRegistry);
        $mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->once()->with(tubepress_spi_player_PluggablePlayerLocationService::_)->andReturn($mockPlayers);

        $this->_sut->onGalleryInitJs($event);

	    $result = $event->getSubject();

	    $this->assertEquals(array(

	        'ajaxPagination' => 'true',
	        'embeddedHeight' => '"999"',
	        'embeddedWidth' => '"888"',
	        'fluidThumbs' => 'false',
	        'playerLocationName' => '"player-loc"',
            'shortcode' => '"%20%2B%20%22%26"',
            'playerJsUrl' => '"xyz/abc"',
            'playerLocationProducesHtml' => 'true',
            'yo' => 'mamma'

	    ), $result);
	}
}