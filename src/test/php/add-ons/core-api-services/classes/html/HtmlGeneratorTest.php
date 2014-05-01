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
 * @covers tubepress_addons_coreapiservices_impl_html_HtmlGenerator
 */
class tubepress_test_addons_coreapiservices_impl_html_CssAndJsHtmlGeneratorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_coreapiservices_impl_html_HtmlGenerator
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
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockEventDispatcher             = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockThemeHandlerInterface       = $this->createMockSingletonService(tubepress_spi_theme_ThemeHandlerInterface::_);
        $this->_mockShortcodeParser             = $this->createMockSingletonService(tubepress_api_shortcode_ParserInterface::_);

        $this->_sut = new tubepress_addons_coreapiservices_impl_html_HtmlGenerator($this->_mockShortcodeParser);
    }

    public function testCssHtml()
    {
        $this->_mockThemeHandlerInterface->shouldReceive('getStyles')->once()->andReturn(array('x'));

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_STYLESHEETS_PRE, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_spi_event_EventBase && $event->getSubject() === '';

            $event->setSubject('abc');

            return $ok;
        }));

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_STYLESHEETS, ehough_mockery_Mockery::on(function ($event) {

            return $event instanceof tubepress_spi_event_EventBase && $event->getSubject() === array('x');

        }))->andReturn(array('x'));

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_STYLESHEETS_POST, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_spi_event_EventBase && $event->getSubject() === 'abc
<link href="x" rel="stylesheet" type="text/css">
';

            $event->setSubject('yum');
            return $ok;
        }));

        $result = $this->_sut->getCssHtml();

        $this->assertEquals('yum', $result);
    }

    public function testJsHtml()
    {
        $this->_mockThemeHandlerInterface->shouldReceive('getScripts')->once()->andReturn(array('x'));

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_SCRIPTS_PRE, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_spi_event_EventBase && $event->getSubject() === '';

            $event->setSubject('abc');

            return $ok;
        }));

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_SCRIPTS, ehough_mockery_Mockery::on(function ($event) {

            return $event instanceof tubepress_spi_event_EventBase && $event->getSubject() === array('x');

        }))->andReturn(array('x'));

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_SCRIPTS_POST, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_spi_event_EventBase && $event->getSubject() === 'abc
<script type="text/javascript" src="x"></script>
';

            $event->setSubject('yum');
            return $ok;
        }));

        $result = $this->_sut->getJsHtml();

        $this->assertEquals('yum', $result);
    }

    public function testOneHandlerCouldHandle()
    {
        $mockHandler = ehough_mockery_Mockery::mock(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);
        $mockHandler->shouldReceive('shouldExecute')->once()->andReturn(true);
        $mockHandler->shouldReceive('getHtml')->once()->andReturn('foobar');

        $this->_sut->setPluggableShortcodeHandlers(array($mockHandler));

        $this->_mockShortcodeParser->shouldReceive('parse')->once();

        $result = $this->_sut->getHtmlForShortcode('shortcode');

        $this->assertEquals('foobar', $result);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoHandlersCouldHandle()
    {
        $mockHandler = ehough_mockery_Mockery::mock(tubepress_spi_shortcode_PluggableShortcodeHandlerService::_);
        $mockHandler->shouldReceive('shouldExecute')->once()->andReturn(false);

        $this->_mockShortcodeParser->shouldReceive('parse')->once();

        $this->_sut->setPluggableShortcodeHandlers(array($mockHandler));

        $this->_sut->getHtmlForShortcode('shortcode');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoHandlers()
    {
        $this->_mockShortcodeParser->shouldReceive('parse')->once();

        $this->_sut->getHtmlForShortcode('shortcode');
    }
}
