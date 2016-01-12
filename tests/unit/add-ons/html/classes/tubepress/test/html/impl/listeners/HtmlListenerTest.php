<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_html_impl_listeners_HtmlListener
 */
class tubepress_test_html_impl_listeners_HtmlListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var Mockery\MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEvent;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockLogger;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockRequestParams;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var tubepress_html_impl_listeners_HtmlListener
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockEvent               = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockEnvironmentDetector = $this->mock(tubepress_api_environment_EnvironmentInterface::_);
        $this->_mockLogger              = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockEventDispatcher     = $this->mock(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockRequestParams       = $this->mock(tubepress_api_http_RequestParametersInterface::_);
        $this->_sut                     = new tubepress_html_impl_listeners_HtmlListener(
            $this->_mockLogger,
            $this->_mockEnvironmentDetector,
            $this->_mockEventDispatcher,
            $this->_mockRequestParams
        );
    }

    public function testOnGlobalJsConfig()
    {
        $mockBaseUrl        = $this->mock('tubepress_api_url_UrlInterface');
        $mockUserContentUrl = $this->mock('tubepress_api_url_UrlInterface');
        $mockAjaxUrl        = $this->mock('tubepress_api_url_UrlInterface');

        $mockBaseUrl->shouldReceive('__toString')->once()->andReturn('mockBaseUrl');
        $mockUserContentUrl->shouldReceive('__toString')->once()->andReturn('mock-user-url');
        $mockAjaxUrl->shouldReceive('__toString')->once()->andReturn('mock-ajax-url');

        $mockBaseUrl->shouldReceive('getClone')->once()->andReturn($mockBaseUrl);
        $mockUserContentUrl->shouldReceive('getClone')->once()->andReturn($mockUserContentUrl);
        $mockAjaxUrl->shouldReceive('getClone')->once()->andReturn($mockAjaxUrl);

        $mockBaseUrl->shouldReceive('removeSchemeAndAuthority')->once();
        $mockUserContentUrl->shouldReceive('removeSchemeAndAuthority')->once();
        $mockAjaxUrl->shouldReceive('removeSchemeAndAuthority')->once();

        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentUrl')->once()->andReturn($mockUserContentUrl);
        $this->_mockEnvironmentDetector->shouldReceive('getAjaxEndpointUrl')->once()->andReturn($mockAjaxUrl);

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(array());
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array(
            'urls' => array(
                'base' => 'mockBaseUrl',
                'usr'  => 'mock-user-url',
                'ajax' => 'mock-ajax-url',
            )
        ));

        $this->_sut->onGlobalJsConfig($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testExceptionLogEnabled()
    {
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(new RuntimeException());
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('error')->atLeast(1);
        $this->_sut->onException($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function testExceptionLogDisabled()
    {
        //$this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(new RuntimeException());
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(false);
        $this->_sut->onException($this->_mockEvent);
        $this->assertTrue(true);
    }

    public function testPostStylesPage2()
    {
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn('html');

        $this->_mockRequestParams->shouldReceive('getParamValueAsInt')->once()->with('tubepress_page', 1)->andReturn(2);

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('html
<meta name="robots" content="noindex, nofollow" />');

        $this->_sut->onPostStylesTemplateRender($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testPostStylesPage1()
    {
        $this->_mockRequestParams->shouldReceive('getParamValueAsInt')->once()->with('tubepress_page', 1)->andReturn(1);

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('html');
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn('html');

        $this->_sut->onPostStylesTemplateRender($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testPostScripts()
    {
        $fakeArgs = array('yo' => 'mamma', 'is' => '"so fat"', 'x' => array('foo' => 500, 'html' => '<>\'"'));

        $mockInternalEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockInternalEvent->shouldReceive('getSubject')->once()->andReturn($fakeArgs);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(array())->andReturn($mockInternalEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_event_Events::HTML_GLOBAL_JS_CONFIG, $mockInternalEvent);

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn('hello');

        $this->_mockEvent->shouldReceive('setSubject')->once()->with($this->_expectedPreScriptJs());

        $this->_sut->onPostScriptsTemplateRender($this->_mockEvent);

        $this->assertTrue(true);
    }

    private function _expectedPreScriptJs()
    {
        return <<<EOT
<script type="text/javascript">var TubePressJsConfig = {"yo":"mamma","is":"\"so fat\"","x":{"foo":500,"html":"<>'\""}};</script>hello
EOT;
    }
}