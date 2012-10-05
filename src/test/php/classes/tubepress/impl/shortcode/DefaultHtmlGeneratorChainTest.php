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
class org_tubepress_impl_shortcode_DefaultHtmlGeneratorChainTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockShortcodeParser;
    private $_mockEventDispatcher;
    private $_mockServiceCollectionsRegistry;

    function setup()
    {
        $this->_sut = new tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator();

        $this->_mockShortcodeParser            = Mockery::mock(tubepress_spi_shortcode_ShortcodeParser::_);
        $this->_mockEventDispatcher            = Mockery::mock('ehough_tickertape_api_IEventDispatcher');
        $this->_mockServiceCollectionsRegistry = Mockery::mock(tubepress_spi_patterns_sl_ServiceCollectionsRegistry::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setShortcodeHtmlParser($this->_mockShortcodeParser);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setServiceCollectionsRegistry($this->_mockServiceCollectionsRegistry);

        $this->_mockShortcodeParser->shouldReceive('parse')->once()->with('shortcode');
    }

    function testOneHandlerCouldHandle()
    {
        $mockHandler = Mockery::mock(tubepress_spi_shortcode_ShortcodeHandler::_);
        $mockHandler->shouldReceive('shouldExecute')->once()->andReturn(true);
        $mockHandler->shouldReceive('getHtml')->once()->andReturn('foobar');

        $mockHandlers = array($mockHandler);

        $this->_mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->once()->with(tubepress_spi_shortcode_ShortcodeHandler::_)->andReturn($mockHandlers);

        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->andReturn(true);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::HTML_CONSTRUCTION, Mockery::on(function ($arg) {

            return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === 'foobar';
        }));

        $result = $this->_sut->getHtmlForShortcode('shortcode');

        $this->assertEquals('foobar', $result);
    }

    function testNoHandlersCouldHandle()
    {
        $mockHandler = Mockery::mock(tubepress_spi_shortcode_ShortcodeHandler::_);
        $mockHandler->shouldReceive('shouldExecute')->once()->andReturn(false);

        $mockHandlers = array($mockHandler);

        $this->_mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->once()->with(tubepress_spi_shortcode_ShortcodeHandler::_)->andReturn($mockHandlers);

        $result = $this->_sut->getHtmlForShortcode('shortcode');

        $this->assertEquals('No handlers could generate HTML', $result);
    }

    function testNoHandlers()
    {
        $this->_mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->once()->with(tubepress_spi_shortcode_ShortcodeHandler::_)->andReturn(array());

        $result = $this->_sut->getHtmlForShortcode('shortcode');

        $this->assertEquals('No handlers could generate HTML', $result);
    }

}