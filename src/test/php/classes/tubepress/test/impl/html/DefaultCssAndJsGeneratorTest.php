<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_impl_html_DefaultCssAndJsGeneratorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_html_DefaultCssAndJsGenerator
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockEventDispatcher             = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);

        $this->_sut = new tubepress_impl_html_DefaultCssAndJsGenerator();
    }

    public function testJqueryInclude()
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_SCRIPT_TAG_JQUERY, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject() === '';

            $event->setSubject('hello');

            return $ok;
        }));

        $this->assertEquals('hello', $this->_sut->getJqueryScriptTag());
    }

    public function testJsInclude()
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_SCRIPT_TAG_TUBEPRESS, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject() === '';

            $event->setSubject('yo');

            return $ok;
        }));

        $this->assertEquals('yo', $this->_sut->getTubePressScriptTag());
    }

    public function testInlineJs()
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_INLINE_JS, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject() === '';

            $event->setSubject('hi');

            return $ok;
        }));

        $this->assertEquals('hi', $this->_sut->getInlineJs());
    }

    public function testInlineCss()
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_INLINE_CSS, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject() === '';

            $event->setSubject('hi');

            return $ok;
        }));

        $this->assertEquals('hi', $this->_sut->getInlineCss());
    }

    public function testCss()
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_STYLESHEET_TAG_TUBEPRESS, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject() === '';

            $event->setSubject('blue');

            return $ok;
        }));
        $this->assertEquals('blue', $this->_sut->getTubePressCssTag());
    }

    public function testMetaTags()
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_META_TAGS, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject() === '';

            $event->setSubject('orange');

            return $ok;
        }));
        $this->assertEquals('orange', $this->_sut->getMetaTags());
    }
}
