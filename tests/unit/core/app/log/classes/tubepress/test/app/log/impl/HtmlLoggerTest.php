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
 * @covers tubepress_app_log_impl_HtmlLogger
 */
class tubepress_test_app_log_impl_HtmlLoggerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_log_impl_HtmlLogger
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParams;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    public function onSetup()
    {
        $this->_mockHttpRequestParams = $this->mock(tubepress_app_http_api_RequestParametersInterface::_);
        $this->_mockContext           = $this->mock(tubepress_app_options_api_ContextInterface::_);

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_log_api_Constants::OPTION_DEBUG_ON)->andReturn(true);

        $this->_mockHttpRequestParams->shouldReceive('hasParam')->once()->with('tubepress_debug')->andReturn(true);
        $this->_mockHttpRequestParams->shouldReceive('getParamValue')->once()->with('tubepress_debug')->andReturn(true);

        $this->_sut = new tubepress_app_log_impl_HtmlLogger(
            $this->_mockContext,
            $this->_mockHttpRequestParams
        );
    }

    public function testEnabled()
    {
        $this->assertTrue($this->_sut->isEnabled());
    }

    public function testError()
    {
        $this->_sut->onBootComplete();
        $this->expectOutputRegex('~<span style="color: red">\[[0-9]{2}:[0-9]{2}\.[0-9]+ - ERROR\] some message {"foo":"bar"}</span><br />~');
        $this->_sut->error('some message', array('foo' => 'bar'));
        $this->assertTrue(true);
    }

    public function testDebug()
    {
        $this->_sut->onBootComplete();
        $this->expectOutputRegex('~<span style="color: inherit">\[[0-9]{2}:[0-9]{2}\.[0-9]+ - INFO\] some message {"foo":"bar"}</span><br />~');
        $this->_sut->debug('some message', array('foo' => 'bar'));
        $this->assertTrue(true);
    }

    public function testWrite()
    {
        $this->_sut->onBootComplete();
        $this->expectOutputRegex('~<span style="color: red">\[[0-9]{2}:[0-9]{2}\.[0-9]+ - ERROR\] some message</span><br />~');
        $this->_sut->___write('some message', array(), true);
    }
}

