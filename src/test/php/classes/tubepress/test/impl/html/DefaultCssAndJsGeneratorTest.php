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

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    public function onSetup()
    {
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockEventDispatcher             = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockEnvironmentDetector         = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);

        $this->_sut = new tubepress_impl_html_DefaultCssAndJsGenerator();
    }

    public function testJqueryInclude()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn('http://foo.bar/some/thing');

        $this->_mockEventDispatcher->shouldReceive('publish')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_SCRIPT_URL_JQUERY, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject()->toString() === 'http://foo.bar/some/thing/src/main/web/vendor/jquery-1.9.1.min.js';

            $event->setSubject('hello');

            return $ok;
        }));

        $this->assertEquals('<script type="text/javascript" src="hello"></script>', $this->_sut->getJqueryScriptTag());
    }

    public function testJsInclude()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn('http://foo.bar/some/thing');

        $this->_mockEventDispatcher->shouldReceive('publish')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_SCRIPT_URL_TUBEPRESSJS, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject()->toString() === 'http://foo.bar/some/thing/src/main/web/js/tubepress.js';

            $event->setSubject('yo');

            return $ok;
        }));

        $this->assertEquals('<script type="text/javascript" src="yo"></script>', $this->_sut->getTubePressScriptTag());
    }

    public function testInlineJs()
    {
        $this->_mockEventDispatcher->shouldReceive('publish')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_INLINE_JS, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject() === '';

            $event->setSubject('hi');

            return $ok;
        }));

        $this->assertEquals('hi', $this->_sut->getInlineJs());
    }

    public function testInlineCss()
    {
        $this->_mockEventDispatcher->shouldReceive('publish')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_INLINE_CSS, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject() === '';

            $event->setSubject('hi');

            return $ok;
        }));

        $this->assertEquals('hi', $this->_sut->getInlineCss());
    }

    public function testCss()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn('http://foo.bar/some/thing');

        $this->_mockEventDispatcher->shouldReceive('publish')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_CSS_URL_TUBEPRESS, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject()->toString() === 'http://foo.bar/some/thing/src/main/web/css/tubepress.css';

            $event->setSubject('blue');

            return $ok;
        }));
        $this->assertEquals('<link rel="stylesheet" href="blue/src/main/web/css/tubepress.css" type="text/css" />', $this->_sut->getTubePressCssTag());
    }

    public function testMetaTags1()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(1);

        $this->_mockEventDispatcher->shouldReceive('publish')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_META_TAGS, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject() === '';

            $event->setSubject('orange');

            return $ok;
        }));
        $this->assertEquals('orange', $this->_sut->getMetaTags());
    }

    public function testMetaTags2()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(2);

        $this->_mockEventDispatcher->shouldReceive('publish')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_META_TAGS, ehough_mockery_Mockery::on(function ($event) {

            $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject() === '<meta name="robots" content="noindex, nofollow" />';

            $event->setSubject('orange');

            return $ok;
        }));
        $this->assertEquals('orange', $this->_sut->getMetaTags());
    }
}
