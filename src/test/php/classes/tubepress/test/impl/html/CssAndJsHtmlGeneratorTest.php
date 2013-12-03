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
 * @var tubepress_impl_html_CssAndJsHtmlGenerator
 */
class tubepress_test_impl_html_CssAndJsHtmlGeneratorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_html_CssAndJsHtmlGenerator
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
    private $_mockCssAndJsRegistry;

    public function onSetup()
    {
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockEventDispatcher             = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockCssAndJsRegistry            = $this->createMockSingletonService(tubepress_spi_html_CssAndJsRegistryInterface::_);

        $this->_sut = new tubepress_impl_html_CssAndJsHtmlGenerator();
    }

    public function testCssHtml()
    {
        $style = array('url' => 'something',  'media' => 'news');

        $this->_mockCssAndJsRegistry->shouldReceive('getStyleHandlesForDisplay')->once()->andReturn(array('x'));
        $this->_mockCssAndJsRegistry->shouldReceive('getStyle')->once()->with('x')->andReturn($style);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_STYLESHEETS_PRE, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_spi_event_EventBase && $event->getSubject() === '';

            $event->setSubject('abc');

            return $ok;
        }));

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_STYLESHEETS, ehough_mockery_Mockery::on(function ($event) use ($style) {

            return $event instanceof tubepress_spi_event_EventBase && $event->getSubject() === array('x' => $style);

        }))->andReturn(array('x' => $style));

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_STYLESHEETS_POST, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_spi_event_EventBase && $event->getSubject() === 'abc
<link href="something" rel="stylesheet" type="text/css" media="news">
';

            $event->setSubject('yum');
            return $ok;
        }));

        $result = $this->_sut->getCssHtml();

        $this->assertEquals('yum', $result);
    }

    public function testJsHtml()
    {
        $script = array('url' => 'something');

        $this->_mockCssAndJsRegistry->shouldReceive('getScriptHandlesForDisplay')->once()->andReturn(array('x'));
        $this->_mockCssAndJsRegistry->shouldReceive('getScript')->once()->with('x')->andReturn($script);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_SCRIPTS_PRE, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_spi_event_EventBase && $event->getSubject() === '';

            $event->setSubject('abc');

            return $ok;
        }));

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_SCRIPTS, ehough_mockery_Mockery::on(function ($event) use ($script) {

            return $event instanceof tubepress_spi_event_EventBase && $event->getSubject() === array('x' => $script);

        }))->andReturn(array('x' => $script));

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_SCRIPTS_POST, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_spi_event_EventBase && $event->getSubject() === 'abc
<script type="text/javascript" src="something"></script>
';

            $event->setSubject('yum');
            return $ok;
        }));

        $result = $this->_sut->getJsHtml();

        $this->assertEquals('yum', $result);
    }
}
