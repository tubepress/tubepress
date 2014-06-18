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

    public function onSetup()
    {
        $this->_mockWordPressFunctionWrapper = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_mockEventDispatcher              = $this->mock(tubepress_core_event_api_EventDispatcherInterface::_);
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_core_http_api_RequestParametersInterface::_);
        $this->_mockQss                         = $this->mock(tubepress_core_url_api_UrlFactoryInterface::_);

        $this->_sut = new tubepress_wordpress_impl_listeners_wp_AdminActionsAndFilters(

            $this->_mockWordPressFunctionWrapper,
            $this->_mockQss,
            $this->_mockHttpRequestParameterService,
            $this->_mockEventDispatcher
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

        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $this->_sut->onAction_admin_notices($mockEvent);

        $this->assertTrue(true);
    }

    public function testAdminNoticesNagNonAdminUser()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('current_user_can')->once()->with('manage_options')->andReturn(false);

        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $this->_sut->onAction_admin_notices($mockEvent);
        $this->assertTrue(true);
    }

    public function testAdminNoticesNagNoDismissRequestedNoDismissStored()
    {
        $mockFullUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockQss->shouldReceive('fromCurrent')->once()->andReturn($mockFullUrl);
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
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
        $mockFullUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockQss->shouldReceive('fromCurrent')->once()->andReturn($mockFullUrl);
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
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
        $mockFullUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockQss->shouldReceive('fromCurrent')->once()->andReturn($mockFullUrl);
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
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
        $mockFullUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockQss->shouldReceive('fromCurrent')->once()->andReturn($mockFullUrl);
        $mockQuery = $this->mock('tubepress_core_url_api_QueryInterface');
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

        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');
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

        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $this->_sut->onAction_admin_notices($mockEvent);
        $this->assertTrue(true);
    }

    public function testAdminMenu()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_options_page')->once()->with(

            'TubePress Options', 'TubePress', 'manage_options',
            'tubepress', array($this->_sut, '__fireOptionsPageEvent')
        );

        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');

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
        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');

        ob_start();
        $this->_sut->onAction_admin_head($mockEvent);
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<meta name="viewport" content="width=device-width, initial-scale=1.0"><meta http-equiv="X-UA-Compatible" content="IE=edge">', $result);
    }

    public function testEnqueueStylesAndScriptsDefault()
    {
        $this->_testRegisterStylesAndScripts();
    }

    public function testEnqueueStylesAndScriptsIE7()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'MSIE 7.4;';

        $this->_testRegisterStylesAndScripts(true);
    }

    public function testEnqueueStylesAndScriptsIE8()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'MSIE 8.4;';

        $this->_testRegisterStylesAndScripts(true);
    }

    public function testEnqueueStylesAndScriptsIE9()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'MSIE 9.4;';

        $this->_testRegisterStylesAndScripts(false);
    }

    public function testEnqueueStylesAndScriptsIE10()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'MSIE 10.4;';

        $this->_testRegisterStylesAndScripts(false);
    }

    private function _testRegisterStylesAndScripts($ie8orLess = false)
    {
        foreach ($this->_getCssMap() as $id => $path) {

            $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('tubepress' . $path, 'tubepress')->andReturn(strtoupper($id));
            $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_style')->once()->with($id, strtoupper($id));
            $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with($id);
        }

        foreach ($this->_getJsMap($ie8orLess) as $id => $path) {

            $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('tubepress' . $path, 'tubepress')->andReturn(strtoupper($id));
            $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_script')->once()->with($id, strtoupper($id));
            $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with($id, false, array(), false, false);
        }

        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array('settings_page_tubepress'));
        $this->_sut->onAction_admin_enqueue_scripts($mockEvent);

        $this->assertTrue(true);
    }

    private function _getCssMap()
    {
        return array(

            'bootstrap-3.1.1'         => '/src/core/options-ui/web/vendor/bootstrap-3.1.1/css/bootstrap-custom.css',
            'bootstrap-theme'         => '/src/core/options-ui/web/vendor/bootstrap-3.1.1/css/bootstrap-custom-theme.css',
            'bootstrap-multiselect'   => '/src/core/options-ui/web/vendor/bootstrap-multiselect-0.9.4/css/bootstrap-multiselect.css',
            'blueimp-gallery-2.14.0'  => '/src/core/options-ui/web/vendor/blueimp-gallery-2.14.0/css/blueimp-gallery.min.css',
            'bootstrap-image-gallery' => '/src/core/options-ui/web/vendor/bootstrap-image-gallery-3.1.0/css/bootstrap-image-gallery.css',
            'tubepress-options-gui'   => '/src/core/options-ui/web/css/options-page.css',
            'wordpress-options-gui'   => '/src/core/wordpress/web/options-gui/css/options-page.css',
            'spectrum'                => '/src/core/options-ui/web/vendor/spectrum-1.3.1/spectrum.css',
        );
    }

    private function _getJsMap($ie8orLess)
    {
        $toReturn = array(

            'bootstrap-3.1.1' => '/src/core/options-ui/web/vendor/bootstrap-3.1.1/js/bootstrap.min.js',
        );

        if ($ie8orLess) {

            $toReturn = array_merge($toReturn, array(

                'html5-shiv-3.7.0' => '/src/core/options-ui/web/vendor/html5-shiv-3.7.0/html5shiv.js',
                'respond-1.4.2'    => '/src/core/options-ui/web/vendor/respond-1.4.2/respond.min.js',
            ));
        }

        $toReturn = array_merge($toReturn, array(

            'bootstrap-multiselect'         => '/src/core/options-ui/web/vendor/bootstrap-multiselect-0.9.4/js/bootstrap-multiselect.js',
            'spectrum'                      => '/src/core/options-ui/web/vendor/spectrum-1.3.1/spectrum.js',
            'blueimp-gallery-2.14.0'        => '/src/core/options-ui/web/vendor/blueimp-gallery-2.14.0/js/blueimp-gallery.min.js',
            'bootstrap-image-gallery'       => '/src/core/options-ui/web/vendor/bootstrap-image-gallery-3.1.0/js/bootstrap-image-gallery.js',
            'bootstrap-field-error-handler' => '/src/core/options-ui/web/js/bootstrap-field-error-handler.js',
            'participant-filter-handler'    => '/src/core/options-ui/web/js/participant-filter-handler.js',
            'spectrum-js-initializer'       => '/src/core/options-ui/web/js/spectrum-js-initializer.js',
            'bootstrap-multiselect-init'    => '/src/core/options-ui/web/js/bootstrap-multiselect-initializer.js',
            'theme-field-handler'           => '/src/core/options-ui/web/js/theme-field-handler.js',
            'theme-reminder'                => '/src/core/wordpress/web/options-gui/js/theme-reminder.js',
            'iframe-loader'                 => '/src/core/wordpress/web/options-gui/js/iframe-loader.js',
        ));

        return $toReturn;
    }

    public function testRowMeta()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('plugin_basename')->once()->with('tubepress/tubepress.php')->andReturn('something');
        $this->_mockWordPressFunctionWrapper->shouldReceive('__')->once()->with('Settings', 'tubepress')->andReturn('orange');

        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');
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
