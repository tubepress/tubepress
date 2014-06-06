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
 * @covers tubepress_core_html_impl_listeners_CoreHtmlListener
 */
class tubepress_test_core_html_impl_listeners_BaseUrlSetterTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    /**
     * @var tubepress_core_html_impl_listeners_CoreHtmlListener
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockEnvironmentDetector = $this->mock(tubepress_core_environment_api_EnvironmentInterface::_);
        $this->_mockLogger              = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockEventDispatcher     = $this->mock(tubepress_core_event_api_EventDispatcherInterface::_);
        $this->_mockEvent               = $this->mock('tubepress_core_event_api_EventInterface');
        $this->_sut                     = new tubepress_core_html_impl_listeners_CoreHtmlListener(
            $this->_mockLogger,
            $this->_mockEnvironmentDetector,
            $this->_mockEventDispatcher
        );
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

    public function testPreScriptsHtml()
    {
        $fakeArgs = array('yo' => 'mamma', 'is' => '"so fat"', 'x' => array('foo' => 500, 'html' => '<>\'"'));

        $mockInternalEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockInternalEvent->shouldReceive('getSubject')->once()->andReturn($fakeArgs);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(array())->andReturn($mockInternalEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_html_api_Constants::EVENT_GLOBAL_JS_CONFIG, $mockInternalEvent);

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn('hello');

        $this->_mockEvent->shouldReceive('setSubject')->once()->with($this->_expectedPreScriptJs());

        $this->_sut->onPreScriptsHtml($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testOnGlobalJsConfig()
    {
        $mockBaseUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockUserContentUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockBaseUrl->shouldReceive('toString')->once()->andReturn('foobar');
        $mockUserContentUrl->shouldReceive('toString')->once()->andReturn('barfoo');
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentUrl')->once()->andReturn($mockUserContentUrl);

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(array());
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array(
            'urls' => array(
                'base' => 'foobar',
                'usr'  => 'barfoo'
            )
        ));

        $this->_sut->onGlobalJsConfig($this->_mockEvent);

        $this->assertTrue(true);
    }

    private function _expectedPreScriptJs()
    {
        return <<<EOT
<script type="text/javascript">var TubePressJsConfig = {"yo":"mamma","is":"\"so fat\"","x":{"foo":500,"html":"<>'\""}};</script>hello
EOT;
    }
}