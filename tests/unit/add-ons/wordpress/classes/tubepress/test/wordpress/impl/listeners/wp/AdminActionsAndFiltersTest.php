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
 * @covers tubepress_wordpress_impl_listeners_wp_AdminActionsAndFilters
 */
class tubepress_test_wordpress_impl_listeners_wp_AdminActionsAndFiltersTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_listeners_wp_AdminActionsAndFilters
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockQss;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockForm;

    public function onSetup()
    {
        $this->_mockWordPressFunctionWrapper    = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_mockEventDispatcher             = $this->mock(tubepress_lib_api_event_EventDispatcherInterface::_);
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_lib_api_http_RequestParametersInterface::_);
        $this->_mockQss                         = $this->mock(tubepress_platform_api_url_UrlFactoryInterface::_);
        $this->_mockForm                        = $this->mock(tubepress_app_api_options_ui_FormInterface::_);

        $this->_sut = new tubepress_wordpress_impl_listeners_wp_AdminActionsAndFilters(

            $this->_mockWordPressFunctionWrapper,
            $this->_mockQss,
            $this->_mockHttpRequestParameterService,
            $this->_mockEventDispatcher,
            $this->_mockForm
        );

        $this->_sut->___doNotIgnoreExceptions();
    }

    public function testAdminNoticesNagNoDismissRequestedDismissStored()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('current_user_can')->once()->with('manage_options')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('dismissTubePressCacheNag')->andReturn(false);
        $mockUser = new stdClass();
        $mockUser->ID = 5;
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_get_current_user')->once()->andReturn($mockUser);
        $this->_mockWordPressFunctionWrapper->shouldReceive('get_transient')->once()->with('user_5_dismiss_tubepress_nag')->andReturn('dismiss');

        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_sut->onAction_admin_notices($mockEvent);

        $this->assertTrue(true);
    }

    public function testAdminNoticesNagNonAdminUser()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('current_user_can')->once()->with('manage_options')->andReturn(false);

        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_sut->onAction_admin_notices($mockEvent);
        $this->assertTrue(true);
    }

    public function testAdminNoticesNagNoDismissRequestedNoDismissStored()
    {
        $mockFullUrl = $this->mock('tubepress_platform_api_url_UrlInterface');
        $this->_mockQss->shouldReceive('fromCurrent')->once()->andReturn($mockFullUrl);
        $mockQuery = $this->mock('tubepress_platform_api_url_QueryInterface');
        $mockFullUrl->shouldReceive('getQuery')->twice()->andReturn($mockQuery);
        $mockQuery->shouldReceive('set')->once()->with('tubePressWpNonce', 'your nonce');
        $mockQuery->shouldReceive('set')->once()->with('dismissTubePressCacheNag', 'true');
        $mockQuery->shouldReceive('__toString')->once()->andReturn('xyz');

        $this->_mockWordPressFunctionWrapper->shouldReceive('current_user_can')->once()->with('manage_options')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('dismissTubePressCacheNag')->andReturn(false);
        $this->_completeNagTest();
    }

    public function testAdminNoticesNagNoDismissRequestedNoDismissStored2()
    {
        $mockFullUrl = $this->mock('tubepress_platform_api_url_UrlInterface');
        $this->_mockQss->shouldReceive('fromCurrent')->once()->andReturn($mockFullUrl);
        $mockQuery = $this->mock('tubepress_platform_api_url_QueryInterface');
        $mockFullUrl->shouldReceive('getQuery')->twice()->andReturn($mockQuery);
        $mockQuery->shouldReceive('set')->once()->with('tubePressWpNonce', 'your nonce');
        $mockQuery->shouldReceive('set')->once()->with('dismissTubePressCacheNag', 'true');
        $mockQuery->shouldReceive('__toString')->once()->andReturn('xyz');

        $this->_mockWordPressFunctionWrapper->shouldReceive('current_user_can')->once()->with('manage_options')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('dismissTubePressCacheNag')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('dismissTubePressCacheNag')->andReturn('xyz');
        $this->_completeNagTest();
    }

    public function testAdminNoticesNagNoDismissRequestedNoDismissStored3()
    {
        $mockFullUrl = $this->mock('tubepress_platform_api_url_UrlInterface');
        $this->_mockQss->shouldReceive('fromCurrent')->once()->andReturn($mockFullUrl);
        $mockQuery = $this->mock('tubepress_platform_api_url_QueryInterface');
        $mockFullUrl->shouldReceive('getQuery')->twice()->andReturn($mockQuery);
        $mockQuery->shouldReceive('set')->once()->with('tubePressWpNonce', 'your nonce');
        $mockQuery->shouldReceive('set')->once()->with('dismissTubePressCacheNag', 'true');
        $mockQuery->shouldReceive('__toString')->once()->andReturn('xyz');

        $this->_mockWordPressFunctionWrapper->shouldReceive('current_user_can')->once()->with('manage_options')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('dismissTubePressCacheNag')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubePressWpNonce')->andReturn(false);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('dismissTubePressCacheNag')->andReturn(true);

        $this->_completeNagTest();
    }

    public function testAdminNoticesNagNoDismissRequestedNoDismissStored4()
    {
        $mockFullUrl = $this->mock('tubepress_platform_api_url_UrlInterface');
        $this->_mockQss->shouldReceive('fromCurrent')->once()->andReturn($mockFullUrl);
        $mockQuery = $this->mock('tubepress_platform_api_url_QueryInterface');
        $mockFullUrl->shouldReceive('getQuery')->twice()->andReturn($mockQuery);
        $mockQuery->shouldReceive('set')->once()->with('tubePressWpNonce', 'your nonce');
        $mockQuery->shouldReceive('set')->once()->with('dismissTubePressCacheNag', 'true');
        $mockQuery->shouldReceive('__toString')->once()->andReturn('xyz');

        $this->_mockWordPressFunctionWrapper->shouldReceive('current_user_can')->once()->with('manage_options')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('dismissTubePressCacheNag')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubePressWpNonce')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('dismissTubePressCacheNag')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubePressWpNonce')->andReturn('bad nonce');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_verify_nonce')->once()->with('bad nonce', 'tubePressDismissNag')->andReturn(false);

        $this->_completeNagTest();
    }

    public function testAdminNoticesDismissRequested()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('current_user_can')->once()->with('manage_options')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('dismissTubePressCacheNag')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubePressWpNonce')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('dismissTubePressCacheNag')->andReturn(true);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValue')->once()->with('tubePressWpNonce')->andReturn('good nonce');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_verify_nonce')->once()->with('good nonce', 'tubePressDismissNag')->andReturn(true);
        $this->_mockWordPressFunctionWrapper->shouldReceive('set_transient')->once()->with('user_5_dismiss_tubepress_nag', 'dismiss', 86400);

        $mockUser = new stdClass();
        $mockUser->ID = 5;
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_get_current_user')->once()->andReturn($mockUser);

        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_sut->onAction_admin_notices($mockEvent);
        $this->assertTrue(true);
    }

    private function _completeNagTest()
    {
        $mockUser = new stdClass();
        $mockUser->ID = 5;
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_get_current_user')->once()->andReturn($mockUser);
        $this->_mockWordPressFunctionWrapper->shouldReceive('get_transient')->once()->with('user_5_dismiss_tubepress_nag')->andReturn(false);
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_create_nonce')->once()->with('tubePressDismissNag')->andReturn('your nonce');

        $this->expectOutputString(<<<ABC
<div class="update-nag">
TubePress is not configured for optimal performance, and could be slowing down your site. <strong><a target="_blank" href="http://docs.tubepress.com/page/manual/wordpress/install-upgrade-uninstall.html#optimize-for-speed">Fix it now</a></strong> or <a href="?xyz">dismiss this message</a>.
</div>
ABC
        );

        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_sut->onAction_admin_notices($mockEvent);
        $this->assertTrue(true);
    }

    public function testAdminMenu()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_options_page')->once()->with(

            'TubePress Options', 'TubePress', 'manage_options',
            'tubepress', array($this->_sut, '__fireOptionsPageEvent')
        );

        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');

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

    public function testAdminHead()
    {
        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');

        ob_start();
        $this->_sut->onAction_admin_head($mockEvent);
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<meta name="viewport" content="width=device-width, initial-scale=1.0"><meta http-equiv="X-UA-Compatible" content="IE=edge">', $result);
    }

    public function testEnqueueStylesAndScriptsDefault()
    {
        $mockCssUrl  = $this->mock(tubepress_platform_api_url_UrlInterface::_);
        $mockCssUrl->shouldReceive('toString')->once()->andReturn('syz');
        $mockCssUrls = array($mockCssUrl);
        $this->_mockForm->shouldReceive('getUrlsCSS')->once()->andReturn($mockCssUrls);

        $mockJsUrl  = $this->mock(tubepress_platform_api_url_UrlInterface::_);
        $mockJsUrl->shouldReceive('toString')->once()->andReturn('abc');
        $mockJsUrls = array($mockJsUrl);
        $this->_mockForm->shouldReceive('getUrlsJS')->once()->andReturn($mockJsUrls);

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_style')->once()->with('tubepress-0', 'syz');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with('tubepress-0');

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress-0', 'abc');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress-0', false, array(), false, false);

        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array('settings_page_tubepress'));
        $this->_sut->onAction_admin_enqueue_scripts($mockEvent);

        $this->assertTrue(true);
    }

    public function testRowMeta()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('plugin_basename')->once()->with('tubepress/tubepress.php')->andReturn('something');
        $this->_mockWordPressFunctionWrapper->shouldReceive('__')->once()->with('Settings', 'tubepress')->andReturn('orange');

        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array('x', 1, 'three'));
        $mockEvent->shouldReceive('getArgument')->once()->with('args')->andReturn(array('something'));
        $mockEvent->shouldReceive('setSubject')->once()->with(array(

            'x', 1, 'three',
            '<a href="options-general.php?page=tubepress.php">orange</a>',
            '<a href="http://docs.tubepress.com/">Documentation</a>',
            '<a href="http://community.tubepress.com/">Support</a>',

        ));

        $this->_sut->onFilter_row_meta($mockEvent);

        $this->assertTrue(true);
    }
}
