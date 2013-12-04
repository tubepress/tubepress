<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_addons_wordpress_impl_listeners_cssjs_BaseUrlSetterTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var tubepress_addons_wordpress_impl_listeners_cssjs_BaseUrlSetter
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_sut                     = new tubepress_addons_wordpress_impl_listeners_cssjs_BaseUrlSetter();
    }

    public function testOnJsConfig()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn('foobar');
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentUrl')->once()->andReturn('barfoo');

        $event = new tubepress_spi_event_EventBase(array());

        $this->_sut->onJsConfig($event);

        $result = $event->getSubject();

        $this->assertTrue(is_array($result));
        $this->assertTrue(isset($result['urls']));
        $this->assertTrue(isset($result['urls']['base']));
        $this->assertTrue(isset($result['urls']['usr']));
        $this->assertEquals('foobar', $result['urls']['base']);
        $this->assertEquals('barfoo', $result['urls']['usr']);
    }
}