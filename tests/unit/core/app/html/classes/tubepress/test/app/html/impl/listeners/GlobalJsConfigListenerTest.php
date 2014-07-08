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
 * @covers tubepress_app_html_impl_listeners_GlobalJsConfigListener
 */
class tubepress_test_app_feature_impl_listeners_GlobalJsConfigListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    /**
     * @var tubepress_app_html_impl_listeners_GlobalJsConfigListener
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockEventDispatcher     = $this->mock(tubepress_lib_event_api_EventDispatcherInterface::_);
        $this->_mockEvent               = $this->mock('tubepress_lib_event_api_EventInterface');
        $this->_sut                     = new tubepress_app_html_impl_listeners_GlobalJsConfigListener(
            $this->_mockEventDispatcher
        );
    }

    public function testPreScriptsHtml()
    {
        $fakeArgs = array('yo' => 'mamma', 'is' => '"so fat"', 'x' => array('foo' => 500, 'html' => '<>\'"'));

        $mockInternalEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockInternalEvent->shouldReceive('getSubject')->once()->andReturn($fakeArgs);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(array())->andReturn($mockInternalEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_html_api_Constants::EVENT_GLOBAL_JS_CONFIG, $mockInternalEvent);

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn('hello');

        $this->_mockEvent->shouldReceive('setSubject')->once()->with($this->_expectedPreScriptJs());

        $this->_sut->onPreScriptsHtml($this->_mockEvent);

        $this->assertTrue(true);
    }

    private function _expectedPreScriptJs()
    {
        return <<<EOT
<script type="text/javascript">var TubePressJsConfig = {"yo":"mamma","is":"\"so fat\"","x":{"foo":500,"html":"<>'\""}};</script>hello
EOT;
    }
}