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
 * @covers tubepress_app_html_impl_HtmlGenerator
 */
class tubepress_test_app_feature_impl_HtmlGeneratorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_html_impl_HtmlGenerator
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeHandlerInterface;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockShortcodeParser;

    public function onSetup()
    {
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_app_http_api_RequestParametersInterface::_);
        $this->_mockEventDispatcher             = $this->mock(tubepress_lib_event_api_EventDispatcherInterface::_);
        $this->_mockThemeHandlerInterface       = $this->mock(tubepress_app_theme_api_ThemeLibraryInterface::_);
        $this->_mockShortcodeParser             = $this->mock(tubepress_app_shortcode_api_ParserInterface::_);

        $this->_sut = new tubepress_app_html_impl_HtmlGenerator(

            $this->_mockEventDispatcher,
            $this->_mockShortcodeParser,
            $this->_mockThemeHandlerInterface
        );
    }

    public function testCssHtml()
    {
        $mockStyleUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockStyleUrl->shouldReceive('__toString')->once()->andReturn('xyz');
        $mockStylesUrls = array($mockStyleUrl);
        $this->_mockThemeHandlerInterface->shouldReceive('getStylesUrls')->once()->andReturn($mockStylesUrls);

        $mockPreStylesheetsEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockPreStylesheetsEvent->shouldReceive('getSubject')->once()->andReturn('foobaz');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('')->andReturn($mockPreStylesheetsEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_html_api_Constants::EVENT_STYLESHEETS_PRE, $mockPreStylesheetsEvent);

        $mockStylesUrlsEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockStylesUrlsEvent->shouldReceive('getSubject')->once()->andReturn($mockStylesUrls);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockStylesUrls)->andReturn($mockStylesUrlsEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_html_api_Constants::EVENT_STYLESHEETS, $mockStylesUrlsEvent);

        $mockPostStylesheetsEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockPostStylesheetsEvent->shouldReceive('getSubject')->once()->andReturn('foobar');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with("foobaz
<link href=\"xyz\" rel=\"stylesheet\" type=\"text/css\">
")->andReturn($mockPostStylesheetsEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_html_api_Constants::EVENT_STYLESHEETS_POST, $mockPostStylesheetsEvent);

        $result = $this->_sut->getCssHtml();

        $this->assertEquals('foobar', $result);
    }

    public function testJsHtml()
    {
        $mockScriptUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockScriptUrl->shouldReceive('__toString')->once()->andReturn('xyz');
        $mockScriptsUrls = array($mockScriptUrl);
        $this->_mockThemeHandlerInterface->shouldReceive('getScriptsUrls')->once()->andReturn($mockScriptsUrls);

        $mockPreScriptsheetsEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockPreScriptsheetsEvent->shouldReceive('getSubject')->once()->andReturn('foobaz');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('')->andReturn($mockPreScriptsheetsEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_html_api_Constants::EVENT_SCRIPTS_PRE, $mockPreScriptsheetsEvent);

        $mockScriptsUrlsEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockScriptsUrlsEvent->shouldReceive('getSubject')->once()->andReturn($mockScriptsUrls);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockScriptsUrls)->andReturn($mockScriptsUrlsEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_html_api_Constants::EVENT_SCRIPTS, $mockScriptsUrlsEvent);

        $mockPostScriptsheetsEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockPostScriptsheetsEvent->shouldReceive('getSubject')->once()->andReturn('foobar');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with("foobaz
<script type=\"text/javascript\" src=\"xyz\"></script>
")->andReturn($mockPostScriptsheetsEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_html_api_Constants::EVENT_SCRIPTS_POST, $mockPostScriptsheetsEvent);

        $result = $this->_sut->getJsHtml();

        $this->assertEquals('foobar', $result);
    }

    public function testOneHandlerCouldHandle()
    {
        $mockEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_html_api_Constants::EVENT_PRIMARY_HTML, $mockEvent);
        $mockEvent->shouldReceive('getSubject')->once()->andReturn('foobar');


        $this->_mockShortcodeParser->shouldReceive('parse')->once();

        $result = $this->_sut->getHtmlForShortcode('shortcode');

        $this->assertEquals('foobar', $result);
    }

    public function testNoHandlersCouldHandle()
    {
        $mockPrimaryEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockPrimaryEvent->shouldReceive('getSubject')->once()->andReturn(null);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('')->andReturn($mockPrimaryEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_html_api_Constants::EVENT_PRIMARY_HTML, $mockPrimaryEvent);

        $mockErrorEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockErrorEvent->shouldReceive('getArgument')->once()->with('htmlForUser')->andReturn('bla');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(ehough_mockery_Mockery::type('RuntimeException'), array('htmlForUser' => 'Unable to generate HTML.'))->andReturn($mockErrorEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_html_api_Constants::EVENT_EXCEPTION_CAUGHT, $mockErrorEvent);

        $this->_mockShortcodeParser->shouldReceive('parse')->once();

        $result = $this->_sut->getHtmlForShortcode('shortcode');

        $this->assertEquals('bla', $result);
    }
}
