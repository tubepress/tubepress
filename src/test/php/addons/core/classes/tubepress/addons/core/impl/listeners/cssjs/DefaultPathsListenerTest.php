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
class tubepress_addons_core_impl_listeners_cssjs_DefaultPathsListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_cssjs_DefaultPathsListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    public function onSetup()
    {
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockEnvironmentDetector         = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);

        $this->_sut = new tubepress_addons_core_impl_listeners_cssjs_DefaultPathsListener();
    }

    public function testJqueryScriptTag()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn('<tubepress_base_url>');

        $event = new tubepress_api_event_TubePressEvent();

        $this->_sut->onJqueryScriptTag($event);

        $this->assertEquals('<script type="text/javascript" src="<tubepress_base_url>/src/main/web/vendor/jquery-1.9.1.min.js"></script>', $event->getSubject());
    }

    public function testTubePressScriptTag()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn('<tubepress_base_url>');

        $event = new tubepress_api_event_TubePressEvent();

        $this->_sut->onTubePressScriptTag($event);

        $this->assertEquals('<script type="text/javascript" src="<tubepress_base_url>/src/main/web/js/tubepress.js"></script>', $event->getSubject());
    }

    public function testTubePressCssTag()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn('<tubepress_base_url>');

        $event = new tubepress_api_event_TubePressEvent();

        $this->_sut->onTubePressStylesheetTag($event);

        $this->assertEquals('<link rel="stylesheet" href="<tubepress_base_url>/src/main/web/css/tubepress.css" type="text/css" />', $event->getSubject());
    }

    public function testMetaPageOne()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(1);

        $event = new tubepress_api_event_TubePressEvent();

        $this->_sut->onMetaTags($event);

        $this->assertEquals('', $event->getSubject());
    }

    public function testMetaPageTwo()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(2);

        $event = new tubepress_api_event_TubePressEvent();

        $this->_sut->onMetaTags($event);

        $this->assertEquals('<meta name="robots" content="noindex, nofollow" />', $event->getSubject());
    }
}