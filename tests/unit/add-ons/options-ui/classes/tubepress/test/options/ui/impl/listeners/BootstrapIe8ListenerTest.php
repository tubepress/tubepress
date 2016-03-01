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
 * @covers tubepress_options_ui_impl_listeners_BootstrapIe8Listener
 */
class tubepress_test_options_ui_impl_listeners_BootstrapIe8ListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_options_ui_impl_listeners_BootstrapIe8Listener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEnvironment;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockIncomingEvent;

    public function onSetup()
    {
        $this->_mockEnvironment   = $this->mock(tubepress_api_environment_EnvironmentInterface::_);
        $this->_mockIncomingEvent = $this->mock('tubepress_api_event_EventInterface');

        $this->_sut = new tubepress_options_ui_impl_listeners_BootstrapIe8Listener(
            $this->_mockEnvironment
        );
    }

    public function testBootstrap()
    {
        $mockBootstrapUrl = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockBootstrapUrl->shouldReceive('getPath')->once()->andReturn('/some/path/bootstrap/');

        $mockShivUrl = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockShivUrl->shouldReceive('setPath')->once()->with('/web/admin-themes/admin-default/vendor/html5-shiv-3.7.0/html5shiv.js');
        $mockShivUrl->shouldReceive('freeze')->once();

        $mockRespondUrl = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockRespondUrl->shouldReceive('setPath')->once()->with('/web/admin-themes/admin-default/vendor/respond-1.4.2/respond.min.js');
        $mockRespondUrl->shouldReceive('freeze')->once();
        $mockRespondUrl->shouldReceive('getClone')->once()->andReturn($mockShivUrl);

        $mockBaseUrl = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockBaseUrl->shouldReceive('getClone')->once()->andReturn($mockRespondUrl);

        $this->_mockEnvironment->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);

        $this->_mockIncomingEvent->shouldReceive('getSubject')->once()->andReturn(array(
            $mockBootstrapUrl
        ));
        $this->_mockIncomingEvent->shouldReceive('setSubject')->once()->with(array(
            $mockShivUrl,
            $mockRespondUrl,
            $mockBootstrapUrl
        ));

        $this->_sut->__setServerVars(array('HTTP_USER_AGENT' => 'MSIE 8;'));
        $this->_sut->onAdminScripts($this->_mockIncomingEvent);

        $this->assertTrue(true);
    }

    public function testNoBootstrap()
    {
        $this->_mockIncomingEvent->shouldReceive('getSubject')->once()->andReturn(array());

        $this->_sut->__setServerVars(array('HTTP_USER_AGENT' => 'MSIE 8;'));
        $this->_sut->onAdminScripts($this->_mockIncomingEvent);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider nonIE8Data
     */
    public function testNonIe8($serverVars)
    {
        $this->_mockIncomingEvent->shouldReceive('getSubject')->once()->andReturn(array());

        $this->_sut->__setServerVars($serverVars);
        $this->_sut->onAdminScripts($this->_mockIncomingEvent);

        $this->assertTrue(true);
    }

    public function nonIE8Data()
    {
        return array(
            array(array()),
            array(array('HTTP_USER_AGENT' => 'something')),
            array(array('HTTP_USER_AGENT' => 'MSIE something')),
            array(array('HTTP_USER_AGENT' => 'MSIE something;')),
            array(array('HTTP_USER_AGENT' => 'MSIE 9')),
        );
    }
}