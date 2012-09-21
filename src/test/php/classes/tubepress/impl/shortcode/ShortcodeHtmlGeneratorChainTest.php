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
class org_tubepress_impl_shortcode_ShortcodeHtmlGeneratorChainTest extends TubePressUnitTest
{
    private $_sut;
    private $_page;
    private $_mockChain;
    private $_mockShortcodeParser;
    private $_mockEventDispatcher;

    function setup()
    {
        $this->_page = 1;

        $this->_mockChain = Mockery::mock('ehough_chaingang_api_Chain');

        $this->_sut = new tubepress_impl_shortcode_ShortcodeHtmlGeneratorChain($this->_mockChain);

        $this->_mockShortcodeParser = Mockery::mock(tubepress_spi_shortcode_ShortcodeParser::_);
        $this->_mockEventDispatcher = Mockery::mock('ehough_tickertape_api_IEventDispatcher');

        tubepress_impl_patterns_ioc_KernelServiceLocator::setShortcodeHtmlParser($this->_mockShortcodeParser);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);

        $this->_mockShortcodeParser->shouldReceive('parse')->once()->with('shortcode');
    }

    /**
     * @expectedException RuntimeException
     */
    function testGetHtmlNoCommandHandled()
    {
        $this->_mockChain->shouldReceive('execute')->once()->with(Mockery::on(function ($arg) {

            return $arg instanceof ehough_chaingang_api_Context;

        }))->andReturn(false);

        $this->_sut->getHtmlForShortcode('shortcode');
    }

    function testGetHtmlNoFilters()
    {
        $this->_mockChain->shouldReceive('execute')->once()->with(Mockery::on(function ($arg) {

            $arg->put(tubepress_impl_shortcode_ShortcodeHtmlGeneratorChain::CHAIN_KEY_GENERATED_HTML, 'chain-return-value');

            return $arg instanceof ehough_chaingang_api_Context;

        }))->andReturn(true);

        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_api_const_event_CoreEventNames::HTML_CONSTRUCTION)->andReturn(false);

        $this->assertEquals('chain-return-value', $this->_sut->getHtmlForShortcode('shortcode'));
    }

    function testGetHtml()
    {
        $this->_mockChain->shouldReceive('execute')->once()->with(Mockery::on(function ($arg) {

            $arg->put(tubepress_impl_shortcode_ShortcodeHtmlGeneratorChain::CHAIN_KEY_GENERATED_HTML, 'chain-return-value');

            return $arg instanceof ehough_chaingang_api_Context;

        }))->andReturn(true);


        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_api_const_event_CoreEventNames::HTML_CONSTRUCTION)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::HTML_CONSTRUCTION, Mockery::on(function ($arg) {

            $good = $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === 'chain-return-value';

            $arg->setSubject('final-value');

            return $good;
        }));

        $this->assertEquals('final-value', $this->_sut->getHtmlForShortcode('shortcode'));
    }

}