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

class org_tubepress_impl_shortcode_SimpleShortcodeParserTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    function setup()
    {
        parent::setUp();

        $this->_sut = new org_tubepress_impl_shortcode_SimpleShortcodeParser();

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);

        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::KEYWORD)->andReturn('butters');
    }

    function testNothingToParse()
    {
        $shortcode = '[bu mode=&#8216playlist&#8217  , playlistValue=&#8242;foobar&#8242; ,author="false", resultCountCap=\'200\' resultsPerPage=3]';

        $this->_sut->parse($shortcode);
    }

    function testMixedCommasWithAllSortsOfQuotes()
    {
        $shortcode = '[butters mode=&#8216playlist&#8217  , playlistValue=&#8242;foobar&#8242; ,author="false", resultCountCap=\'200\' resultsPerPage=3]';

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $expected = array(
            org_tubepress_api_const_options_names_Output::GALLERY_SOURCE                => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            org_tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'foobar',
            org_tubepress_api_const_options_names_Meta::AUTHOR                          => 'false',
            org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP                => 200,
            org_tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE              => 3
        );

        $this->_setupExpectedFilters($ioc, $expected);

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);
    }

    function testNoCommasWithAllSortsOfQuotes()
    {
        $shortcode = '[butters mode=&#8216playlist&#8217 playlistValue=&#8242;foobar&#8242; author="true" resultCountCap=\'200\' resultsPerPage=3]';

        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $expected = array(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            org_tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'foobar',
            org_tubepress_api_const_options_names_Meta::AUTHOR => 'true',
            org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP => 200,
            org_tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE => 3
        );

        $this->_setupExpectedFilters($ioc, $expected);

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);
    }

    function testCommasWithAllSortsOfQuotes()
    {
        $shortcode = '[butters mode=&#8216playlist&#8217, playlistValue=&#8242;foobar&#8242;, author="true", resultCountCap=\'200\', resultsPerPage=3]';

        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $expected = array(
            org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            org_tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'foobar',
            org_tubepress_api_const_options_names_Meta::AUTHOR => 'true',
            org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP => 200,
            org_tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE => 3
        );

        $this->_setupExpectedFilters($ioc, $expected);

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);
    }

    function testNoCustomOptions()
    {
        $shortcode = '[butters]';

        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->never();

        $this->_sut->parse($shortcode);
    }

    function testWeirdSingleQuotes()
    {
        $shortcode = '[butters mode=&#8216playlist&#8217 playlistValue=&#8242;foobar&#8242;]';

        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $expected = array(
            org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            org_tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'foobar'
        );

        $this->_setupExpectedFilters($ioc, $expected);

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);
    }

    function testWeirdDoubleQuotes()
    {
        $shortcode = '[butters mode=&#34playlist&#8220; playlistValue=&#8221;foobar&#8243;]';

        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $expected = array(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            org_tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE => 'foobar'
        );

        $this->_setupExpectedFilters($ioc, $expected);

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);
    }

    function testNoQuotes()
    {
        $shortcode = '[butters mode=playlist	]';

        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $expected = array(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($ioc, $expected);

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);
    }

    function testSingleQuotes()
    {
        $shortcode = '[butters mode=\'playlist\']';
        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $expected = array(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($ioc, $expected);

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);
    }

    function testDoubleQuotes()
    {
        $shortcode = '[butters mode="playlist"]';
        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $expected = array(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($ioc, $expected);

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);
    }

    function testMismatchedStartEndQuotes()
    {
        $this->_sut->parse('[butters mode=\'playlist"]');
    }

    function testNoClosingBracket()
    {
        $this->_sut->parse('[butters mode=\'playlist\'');
    }

    function testNoOpeningBracket()
    {
        $content = "butters mode='playlist']";

        $this->_sut->parse($content);
    }

    function testSpaceAroundAttributes()
    {
        $shortcode = "[butters mode='playlist']";
        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $expected = array(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($ioc, $expected);

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);
    }

    function testSpaceAroundShortcode()
    {
        $shortcode = "sddf	 [butters mode='playlist']	sdsdfsdf";
        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $expected = array(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($ioc, $expected);

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with("[butters mode='playlist']");
        $context->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);
    }

    function testNoSpaceAroundShortcode()
    {
        $shortcode = "sddf[butters mode='playlist']sdsdfsdf";
        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $expected = array(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE => org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST);

        $this->_setupExpectedFilters($ioc, $expected);

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with("[butters mode='playlist']");
        $context->shouldReceive('setCustomOptions')->once()->with($expected);

        $this->_sut->parse($shortcode);
    }

    private function _setupExpectedFilters($ioc, $expected)
    {
        $pm = $ioc->get(org_tubepress_api_plugin_PluginManager::_);

        foreach ($expected as $name => $value) {

            $pm->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::VARIABLE_READ_FROM_EXTERNAL_INPUT, $value, $name)->andReturn($value);
        }
    }
}

