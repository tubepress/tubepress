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
 * @covers tubepress_wordpress_impl_listeners_wp_AdminActionsAndFilters
 */
class tubepress_test_wordpress_impl_listeners_wp_AdminActionsAndFiltersTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_listeners_wp_AdminActionsAndFilters
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockQss;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockForm;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockStringUtils;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEnvironment;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockOauth2Initiator;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockOauth2Callback;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContext;

    public function onSetup()
    {
        $this->_mockWordPressFunctionWrapper    = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_mockEventDispatcher             = $this->mock(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_api_http_RequestParametersInterface::_);
        $this->_mockQss                         = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $this->_mockForm                        = $this->mock(tubepress_api_options_ui_FormInterface::_);
        $this->_mockStringUtils                 = $this->mock(tubepress_api_util_StringUtilsInterface::_);
        $this->_mockEnvironment                 = $this->mock(tubepress_api_environment_EnvironmentInterface::_);
        $this->_mockOauth2Initiator             = $this->mock('tubepress_http_oauth2_impl_popup_AuthorizationInitiator');
        $this->_mockOauth2Callback              = $this->mock('tubepress_http_oauth2_impl_popup_RedirectionCallback');
        $this->_mockContext                     = $this->mock(tubepress_api_options_ContextInterface::_);

        $this->_sut = new tubepress_wordpress_impl_listeners_wp_AdminActionsAndFilters(

            $this->_mockWordPressFunctionWrapper,
            $this->_mockQss,
            $this->_mockHttpRequestParameterService,
            $this->_mockEventDispatcher,
            $this->_mockForm,
            $this->_mockStringUtils,
            $this->_mockEnvironment,
            $this->_mockOauth2Initiator,
            $this->_mockOauth2Callback,
            $this->_mockContext
        );
    }

    public function testAdminMenu()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_options_page')->once()->with(

            'TubePress Options', 'TubePress', 'manage_options',
            'tubepress', array($this->_sut, '__fireOptionsPageEvent')
        );

        $this->_mockWordPressFunctionWrapper->shouldReceive('add_submenu_page')->once()->with(

            null, '', '', 'manage_options',
            'tubepress_oauth2_start', array($this->_sut, '__noop')
        );

        $this->_mockWordPressFunctionWrapper->shouldReceive('add_submenu_page')->once()->with(

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

    public function testAdminHead()
    {
        $mockEvent = $this->mock('tubepress_api_event_EventInterface');

        ob_start();
        $this->_sut->onAction_admin_head($mockEvent);
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<meta name="viewport" content="width=device-width, initial-scale=1.0"><meta http-equiv="X-UA-Compatible" content="IE=edge">', $result);
    }

    public function testEnqueueStylesAndScriptsDefault()
    {
        $mockSystemCssUrl = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockSystemJsUrl  = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockUserCssUrl   = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockUserJsUrl    = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockBaseUrl      = $this->mock('tubepress_api_url_UrlInterface');
        $mockUserUrl      = $this->mock('tubepress_api_url_UrlInterface');

        $mockSystemCssUrl->shouldReceive('isAbsolute')->once()->andReturn(false);
        $mockSystemJsUrl->shouldReceive('isAbsolute')->once()->andReturn(false);
        $mockUserCssUrl->shouldReceive('isAbsolute')->once()->andReturn(false);
        $mockUserJsUrl->shouldReceive('isAbsolute')->once()->andReturn(false);

        $mockBaseUrl->shouldReceive('toString')->once()->andReturn('--base-url--');
        $mockUserUrl->shouldReceive('toString')->once()->andReturn('--user-url--');
        $mockSystemCssUrl->shouldReceive('toString')->once()->andReturn('--base-url--/web/system-css-url');
        $mockSystemJsUrl->shouldReceive('toString')->once()->andReturn('--base-url--/web/system-js-url');
        $mockUserCssUrl->shouldReceive('toString')->once()->andReturn('--user-url--/something/user-css-url');
        $mockUserJsUrl->shouldReceive('toString')->once()->andReturn('--user-url--/something/user-js-url');

        $mockCssUrls = array($mockSystemCssUrl, $mockUserCssUrl);
        $mockJsUrls = array($mockSystemJsUrl, $mockUserJsUrl);
        $this->_mockForm->shouldReceive('getUrlsCSS')->once()->andReturn($mockCssUrls);
        $this->_mockForm->shouldReceive('getUrlsJS')->once()->andReturn($mockJsUrls);

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_style')->once()->with('tubepress-0', '<<system-style-url>>');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_style')->once()->with('tubepress-1', '<<user-style-url>>');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with('tubepress-0');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with('tubepress-1');

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress-0', '<<system-script-url>>');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress-1', '<<user-script-url>>');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress-0', false, array(), false, false);
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress-1', false, array(), false, false);

        $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('/web/system-css-url', 'core/tubepress.php')->andReturn('<<system-style-url>>');
        $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('/web/system-js-url', 'core/tubepress.php')->andReturn('<<system-script-url>>');
        $this->_mockWordPressFunctionWrapper->shouldReceive('content_url')->once()->with('tubepress-content/something/user-css-url')->andReturn('<<user-style-url>>');
        $this->_mockWordPressFunctionWrapper->shouldReceive('content_url')->once()->with('tubepress-content/something/user-js-url')->andReturn('<<user-script-url>>');

        $this->_mockEnvironment->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);
        $this->_mockEnvironment->shouldReceive('getUserContentUrl')->once()->andReturn($mockUserUrl);

        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('--base-url--/web/system-css-url', '--base-url--/web/')->andReturn(true);
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('--base-url--/web/system-js-url', '--base-url--/web/')->andReturn(true);
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('--user-url--/something/user-css-url', '--base-url--/web/')->andReturn(false);
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('--user-url--/something/user-css-url', '--user-url--/')->andReturn(true);
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('--user-url--/something/user-js-url', '--base-url--/web/')->andReturn(false);
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('--user-url--/something/user-js-url', '--user-url--/')->andReturn(true);

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array('settings_page_tubepress'));
        $this->_sut->onAction_admin_enqueue_scripts($mockEvent);

        $this->assertTrue(true);
    }

    public function testRowMeta()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('plugin_basename')->once()->with('core/tubepress.php')->andReturn('something');
        $this->_mockWordPressFunctionWrapper->shouldReceive('__')->once()->with('Settings', 'tubepress')->andReturn('orange');

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array('x', 1, 'three'));
        $mockEvent->shouldReceive('getArgument')->once()->with('args')->andReturn(array('something'));
        $mockEvent->shouldReceive('setSubject')->once()->with(array(

            'x', 1, 'three',
            '<a href="options-general.php?page=tubepress.php">orange</a>',
            '<a href="http://support.tubepress.com/">Support</a>',

        ));

        $this->_sut->onFilter_row_meta($mockEvent);

        $this->assertTrue(true);
    }
}
