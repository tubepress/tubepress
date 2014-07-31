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
 * @covers tubepress_wordpress_impl_listeners_html_WpHtmlListener
 */
class tubepress_test_wordpress_impl_listeners_html_CssJsDequererTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_listeners_html_WpHtmlListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_wpFunctions;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    public function onSetup()
    {
        $this->_wpFunctions = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_mockUrlFactory = $this->mock(tubepress_platform_api_url_UrlFactoryInterface::_);
        $this->_sut = new tubepress_wordpress_impl_listeners_html_WpHtmlListener(
            $this->_wpFunctions,
            $this->_mockUrlFactory
        );
    }

    public function testGlobalJsConfig()
    {
        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturnNull();
        $mockEvent->shouldReceive('setSubject')->once()->with(array(
            'urls' => array('php' => array('sys' => array('ajaxEndpoint' => 'yoyo')))
        ));

        $this->_wpFunctions->shouldReceive('admin_url')->once()->with('admin-ajax.php')->andReturn('s');

        $mockUrl = $this->mock('tubepress_platform_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('s')->andReturn($mockUrl);

        $mockUrl->shouldReceive('removeSchemeAndAuthority')->once();
        $mockUrl->shouldReceive('__toString')->once()->andReturn('yoyo');

        $this->_sut->onGlobalJsConfig($mockEvent);

        $this->assertTrue(true);
    }

    public function testCss()
    {
        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $mockEvent->shouldReceive('setSubject')->once()->with(array());

        $this->_sut->onCss($mockEvent);

        $this->assertTrue(true);
    }

    public function testJs()
    {
        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $mockEvent->shouldReceive('setSubject')->once()->with(array());

        $this->_sut->onJs($mockEvent);

        $this->assertTrue(true);
    }
}