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
 * @covers tubepress_core_html_impl_listeners_GlobalJsConfig
 */
class tubepress_test_core_html_impl_listeners_JsConfigTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_html_impl_listeners_GlobalJsConfig
     */
    private $_sut;

    /**
     * @var tubepress_core_media_provider_api_Page
     */
    private $_providerResult;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_providerResult       = new tubepress_core_media_provider_api_Page();
        $this->_mockEventDispatcher  = $this->mock(tubepress_core_event_api_EventDispatcherInterface::_);
        $this->_sut                  = new tubepress_core_html_impl_listeners_GlobalJsConfig($this->_mockEventDispatcher);
    }

    public function testAlterHtml()
    {
        $fakeArgs = array('yo' => 'mamma', 'is' => '"so fat"', 'x' => array('foo' => 500, 'html' => '<>\'"'));

        $mockInternalEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockInternalEvent->shouldReceive('getSubject')->once()->andReturn($fakeArgs);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(array())->andReturn($mockInternalEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_html_api_Constants::EVENT_GLOBAL_JS_CONFIG, $mockInternalEvent);

        $event = $this->mock('tubepress_core_event_api_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn('hello');

        $event->shouldReceive('setSubject')->once()->with($this->expectedAjax());

        $this->_sut->onPreScriptsHtml($event);

        $this->assertTrue(true);
    }

    public function expectedAjax()
    {
        return <<<EOT
<script type="text/javascript">var TubePressJsConfig = {"yo":"mamma","is":"\"so fat\"","x":{"foo":500,"html":"<>'\""}};</script>hello
EOT;
    }
}