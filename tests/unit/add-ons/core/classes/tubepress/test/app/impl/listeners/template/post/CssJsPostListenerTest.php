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
 * @covers tubepress_app_impl_listeners_template_post_CssJsPostListener
 */
class tubepress_test_app_impl_listeners_template_post_CssJsPostListenerTest extends tubepress_test_TubePressUnitTest
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
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockRequestParams;

    /**
     * @var tubepress_app_impl_listeners_template_post_CssJsPostListener
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockEventDispatcher = $this->mock(tubepress_lib_api_event_EventDispatcherInterface::_);
        $this->_mockEvent           = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockRequestParams   = $this->mock(tubepress_lib_api_http_RequestParametersInterface::_);

        $this->_sut = new tubepress_app_impl_listeners_template_post_CssJsPostListener(
            $this->_mockEventDispatcher,
            $this->_mockRequestParams
        );
    }

    public function testOnBeforeCssPage2()
    {
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn('html');

        $this->_mockRequestParams->shouldReceive('getParamValueAsInt')->once()->with('tubepress_page', 1)->andReturn(2);

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('html
<meta name="robots" content="noindex, nofollow" />');

        $this->_sut->onPostStylesTemplateRender($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testOnBeforeCssPage1()
    {
        $this->_mockRequestParams->shouldReceive('getParamValueAsInt')->once()->with('tubepress_page', 1)->andReturn(1);

        $this->_mockEvent->shouldReceive('setSubject')->once()->with('html');
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn('html');

        $this->_sut->onPostStylesTemplateRender($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testPreScriptsHtml()
    {
        $fakeArgs = array('yo' => 'mamma', 'is' => '"so fat"', 'x' => array('foo' => 500, 'html' => '<>\'"'));

        $mockInternalEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $mockInternalEvent->shouldReceive('getSubject')->once()->andReturn($fakeArgs);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(array())->andReturn($mockInternalEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_api_event_Events::HTML_GLOBAL_JS_CONFIG, $mockInternalEvent);

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