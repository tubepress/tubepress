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
class tubepress_plugins_wordpress_impl_DefaultContentFilterTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockStorageManager;

    private $_mockShortcodeParser;

    private $_mockShortcodeHtmlGenerator;

    private $_mockExecutionContext;

    private $_mockMessageService;

    public function onSetup()
    {
        $this->_sut = new tubepress_plugins_wordpress_impl_DefaultContentFilter();

        $this->_mockExecutionContext       = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockMessageService         = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);
        $this->_mockShortcodeHtmlGenerator = $this->createMockSingletonService(tubepress_spi_shortcode_ShortcodeHtmlGenerator::_);
        $this->_mockShortcodeParser        = $this->createMockSingletonService(tubepress_spi_shortcode_ShortcodeParser::_);
        $this->_mockStorageManager         = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);

    }

    function testContentFilter()
    {
        $this->_mockStorageManager->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::KEYWORD)->andReturn('trigger word');

        $this->_mockShortcodeParser->shouldReceive('somethingToParse')->times(2)->with('the content', 'trigger word')->andReturn(true);
        $this->_mockShortcodeParser->shouldReceive('somethingToParse')->times(2)->with('html for shortcode', 'trigger word')->andReturn(true, false);

        $this->_mockShortcodeHtmlGenerator->shouldReceive('getHtmlForShortcode')->once()->with('the content')->andReturn('html for shortcode');
        $this->_mockShortcodeHtmlGenerator->shouldReceive('getHtmlForShortcode')->once()->with('html for shortcode')->andReturn('html for shortcode');

        $this->_mockExecutionContext->shouldReceive('getActualShortcodeUsed')->times(4)->andReturn('<current shortcode>');
        $this->_mockExecutionContext->shouldReceive('reset')->twice();

        $this->assertEquals('html for shortcode', $this->_sut->filterContent('the content'));
    }


}
