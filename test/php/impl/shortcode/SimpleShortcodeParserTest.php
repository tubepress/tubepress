<?php

require_once BASE . '/sys/classes/org/tubepress/impl/shortcode/SimpleShortcodeParser.class.php';

class_exists('org_tubepress_impl_classloader_ClassLoader') || require BASE . '/sys/classes/org/tubepress/impl/classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Output',
    'org_tubepress_api_const_options_values_ModeValue',
    'org_tubepress_api_const_options_names_Meta',
    'org_tubepress_api_const_options_names_Display',
    'org_tubepress_api_const_options_names_Feed'
));

class org_tubepress_impl_shortcode_SimpleShortcodeParserTest extends TubePressUnitTest
{
    private $_sut;

    function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_shortcode_SimpleShortcodeParser();
        org_tubepress_impl_log_Log::setEnabled(false, array());

        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::KEYWORD)->andReturn('butters');

        $validator = $ioc->get('org_tubepress_api_options_OptionValidator');
        $validator->shouldReceive('validate')->zeroOrMoreTimes();
    }

    function testMixedCommasWithAllSortsOfQuotes()
    {
        $shortcode = '[butters mode=&#8216playlist&#8217  , playlistValue=&#8242;foobar&#8242; ,author="false", resultCountCap=\'200\' resultsPerPage=3]';

        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with(array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST,
            org_tubepress_api_const_options_names_Output::PLAYLIST_VALUE => 'foobar',
            org_tubepress_api_const_options_names_Meta::AUTHOR => false,
            org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP => 200,
            org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE => 3
        ));

        $this->_sut->parse($shortcode);
    }

    function testNoCommasWithAllSortsOfQuotes()
    {
        $shortcode = '[butters mode=&#8216playlist&#8217 playlistValue=&#8242;foobar&#8242; author="true" resultCountCap=\'200\' resultsPerPage=3]';

        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with(array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST,
            org_tubepress_api_const_options_names_Output::PLAYLIST_VALUE => 'foobar',
            org_tubepress_api_const_options_names_Meta::AUTHOR => true,
            org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP => 200,
            org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE => 3
        ));

        $this->_sut->parse($shortcode);
    }

    function testCommasWithAllSortsOfQuotes()
    {
        $shortcode = '[butters mode=&#8216playlist&#8217, playlistValue=&#8242;foobar&#8242;, author="true", resultCountCap=\'200\', resultsPerPage=3]';

        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with(array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST,
            org_tubepress_api_const_options_names_Output::PLAYLIST_VALUE => 'foobar',
            org_tubepress_api_const_options_names_Meta::AUTHOR => true,
            org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP => 200,
            org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE => 3
        ));

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

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with(array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST,
            org_tubepress_api_const_options_names_Output::PLAYLIST_VALUE => 'foobar'
        ));

        $this->_sut->parse($shortcode);
    }

    function testWeirdDoubleQuotes()
    {
        $shortcode = '[butters mode=&#34playlist&#8220; playlistValue=&#8221;foobar&#8243;]';

        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with(array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST,
            org_tubepress_api_const_options_names_Output::PLAYLIST_VALUE => 'foobar'
        ));

        $this->_sut->parse($shortcode);
    }

    function testNoQuotes()
    {
        $shortcode = '[butters mode=playlist	]';

        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with(array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST));

        $this->_sut->parse($shortcode);
    }

    function testSingleQuotes()
    {
        $shortcode = '[butters mode=\'playlist\']';
        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with(array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST));

        $this->_sut->parse($shortcode);
    }

    function testDoubleQuotes()
    {
        $shortcode = '[butters mode="playlist"]';
        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with(array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST));

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

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with($shortcode);
        $context->shouldReceive('setCustomOptions')->once()->with(array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST));

        $this->_sut->parse($shortcode);
    }

    function testSpaceAroundShortcode()
    {
        $shortcode = "sddf	 [butters mode='playlist']	sdsdfsdf";
        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with("[butters mode='playlist']");
        $context->shouldReceive('setCustomOptions')->once()->with(array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST));

        $this->_sut->parse($shortcode);
    }

    function testNoSpaceAroundShortcode()
    {
        $shortcode = "sddf[butters mode='playlist']sdsdfsdf";
        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('setActualShortcodeUsed')->once()->with("[butters mode='playlist']");
        $context->shouldReceive('setCustomOptions')->once()->with(array(org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::PLAYLIST));

        $this->_sut->parse($shortcode);
    }
}

