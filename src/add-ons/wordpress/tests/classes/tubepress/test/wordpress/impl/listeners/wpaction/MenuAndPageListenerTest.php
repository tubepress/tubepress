<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_wordpress_impl_listeners_wpaction_MenuAndPageListener
 */
class tubepress_test_wordpress_impl_listeners_wpaction_MenuAndPageListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_listeners_wpaction_MenuAndPageListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockWpFunctions;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockOauth2Initiator;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockOauth2RedirectionCallback;

    public function onSetup()
    {
        $this->_mockWpFunctions               = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_mockEventDispatcher           = $this->mock(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockOauth2Initiator           = $this->mock('tubepress_http_oauth2_impl_popup_AuthorizationInitiator');
        $this->_mockOauth2RedirectionCallback = $this->mock('tubepress_http_oauth2_impl_popup_RedirectionCallback');

        $this->_sut = new tubepress_wordpress_impl_listeners_wpaction_MenuAndPageListener(

            $this->_mockWpFunctions,
            $this->_mockEventDispatcher,
            $this->_mockOauth2Initiator,
            $this->_mockOauth2RedirectionCallback
        );
    }

    public function testAdminMenu()
    {
        $this->_mockWpFunctions->shouldReceive('add_options_page')->once()->with(

            'TubePress Options', 'TubePress', 'manage_options',
            'tubepress', array($this->_sut, '__fireOptionsPageEvent')
        );

        $this->_mockWpFunctions->shouldReceive('add_submenu_page')->once()->with(

            null, '', '', 'manage_options',
            'tubepress_oauth2_start', array($this->_sut, '__noop')
        );

        $this->_mockWpFunctions->shouldReceive('add_submenu_page')->once()->with(

            null, '', '', 'manage_options',
            'tubepress_oauth2', array($this->_sut, '__noop')
        );

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');

        $this->_sut->onAction_admin_menu($mockEvent);

        $this->assertTrue(true);
    }

    public function testRunOptionsPage()
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(
            tubepress_wordpress_api_Constants::EVENT_OPTIONS_PAGE_INVOKED
        );

        $this->_sut->__fireOptionsPageEvent();

        $this->assertTrue(true);
    }
}
