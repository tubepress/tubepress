<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_wordpress_impl_listeners_wpaction_ThemeCssJsListener
 */
class tubepress_test_wordpress_impl_listeners_wpaction_ThemeCssJsListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_listeners_wpaction_ThemeCssJsListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockHtmlGenerator;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockStringUtils;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEnvironment;

    public function onSetup()
    {
        $this->_mockWordPressFunctionWrapper = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_mockHtmlGenerator            = $this->mock(tubepress_api_html_HtmlGeneratorInterface::_);
        $this->_mockStringUtils              = $this->mock(tubepress_api_util_StringUtilsInterface::_);
        $this->_mockEnvironment              = $this->mock(tubepress_api_environment_EnvironmentInterface::_);

        $this->_sut = new tubepress_wordpress_impl_listeners_wpaction_ThemeCssJsListener(

            $this->_mockWordPressFunctionWrapper,
            $this->_mockEnvironment,
            $this->_mockHtmlGenerator,
            $this->_mockStringUtils
        );
    }

    public function testInitAction()
    {
        $mockSysJsUrl        = $this->mock('tubepress_api_url_UrlInterface');
        $mockSystemStyleUrl  = $this->mock('tubepress_api_url_UrlInterface');
        $mockUserStyleUrl    = $this->mock('tubepress_api_url_UrlInterface');
        $mockSystemScriptUrl = $this->mock('tubepress_api_url_UrlInterface');
        $mockUserScriptUrl   = $this->mock('tubepress_api_url_UrlInterface');
        $mockBaseUrl         = $this->mock('tubepress_api_url_UrlInterface');
        $mockUserUrl         = $this->mock('tubepress_api_url_UrlInterface');

        $mockBaseUrl->shouldReceive('toString')->once()->andReturn('--base-url--');
        $mockUserUrl->shouldReceive('toString')->once()->andReturn('--user-url--');
        $mockSysJsUrl->shouldReceive('toString')->once()->andReturn('--base-url--/web/js/tubepress.js');
        $mockSystemStyleUrl->shouldReceive('toString')->once()->andReturn('--base-url--/web/something/system-style-url');
        $mockSystemScriptUrl->shouldReceive('toString')->once()->andReturn('--base-url--/web/something/system-script-url');
        $mockUserScriptUrl->shouldReceive('toString')->once()->andReturn('--user-url--/something/user-script-url');
        $mockUserStyleUrl->shouldReceive('toString')->once()->andReturn('--user-url--/something/user-style-url');

        $mockSysJsUrl->shouldReceive('isAbsolute')->once()->andReturn(false);
        $mockSystemStyleUrl->shouldReceive('isAbsolute')->once()->andReturn(false);
        $mockUserStyleUrl->shouldReceive('isAbsolute')->once()->andReturn(false);
        $mockSystemScriptUrl->shouldReceive('isAbsolute')->once()->andReturn(false);
        $mockUserScriptUrl->shouldReceive('isAbsolute')->once()->andReturn(false);

        $themeStyles  = array($mockSystemStyleUrl, $mockUserStyleUrl);
        $themeScripts = array($mockSysJsUrl, $mockSystemScriptUrl, $mockUserScriptUrl);
        $mockVersion  = $this->mock('tubepress_api_version_Version');
        $mockVersion->shouldReceive('__toString')->atLeast(1)->andReturn('tubepress-version');

        $this->_mockEnvironment->shouldReceive('getVersion')->once()->andReturn($mockVersion);
        $this->_mockEnvironment->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);
        $this->_mockEnvironment->shouldReceive('getUserContentUrl')->once()->andReturn($mockUserUrl);

        $this->_mockHtmlGenerator->shouldReceive('getUrlsCSS')->once()->andReturn($themeStyles);
        $this->_mockHtmlGenerator->shouldReceive('getUrlsJS')->once()->andReturn($themeScripts);

        $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('web/js/wordpress-ajax.js', basename(TUBEPRESS_ROOT) . '/tubepress.php')->andReturn('<ajaxjs>');
        $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('/web/js/tubepress.js', basename(TUBEPRESS_ROOT) . '/tubepress.php')->andReturn('<<system-js-url>>');
        $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('/web/something/system-style-url', basename(TUBEPRESS_ROOT) . '/tubepress.php')->andReturn('<<system-style-url>>');
        $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('/web/something/system-script-url', basename(TUBEPRESS_ROOT) . '/tubepress.php')->andReturn('<<system-script-url>>');
        $this->_mockWordPressFunctionWrapper->shouldReceive('content_url')->once()->with('tubepress-content/something/user-style-url')->andReturn('<<user-style-url>>');
        $this->_mockWordPressFunctionWrapper->shouldReceive('content_url')->once()->with('tubepress-content/something/user-script-url')->andReturn('<<user-script-url>>');

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_style')->once()->with('tubepress-theme-0', '<<system-style-url>>', array(), 'tubepress-version');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_style')->once()->with('tubepress-theme-1', '<<user-style-url>>', array(), 'tubepress-version');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with('tubepress-theme-0');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with('tubepress-theme-1');

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress', '<<system-js-url>>', array(), 'tubepress-version');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress_ajax', '<ajaxjs>', array('tubepress'), 'tubepress-version');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress-theme-1', '<<system-script-url>>', array('tubepress'), 'tubepress-version');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress-theme-2', '<<user-script-url>>', array('tubepress'), 'tubepress-version');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress', false, array(), false, false);
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress-theme-1', false, array(), false, false);
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress-theme-2', false, array(), false, false);
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('jquery', false, array(), false, false);
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress_ajax', false, array(), false, false);

        $this->_mockWordPressFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(false);

        $this->_mockStringUtils->shouldReceive('endsWith')->once()->with('<<system-js-url>>', '/web/js/tubepress.js')->andReturn(true);
        $this->_mockStringUtils->shouldReceive('endsWith')->once()->with('<<system-script-url>>', '/web/js/tubepress.js')->andReturn(false);
        $this->_mockStringUtils->shouldReceive('endsWith')->once()->with('<<user-script-url>>', '/web/js/tubepress.js')->andReturn(false);
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('--base-url--/web/js/tubepress.js', '--base-url--/web/')->andReturn(true);
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('--base-url--/web/something/system-style-url', '--base-url--/web/')->andReturn(true);
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('--base-url--/web/something/system-script-url', '--base-url--/web/')->andReturn(true);
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('--user-url--/something/user-style-url', '--base-url--/web/')->andReturn(false);
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('--user-url--/something/user-style-url', '--user-url--/')->andReturn(true);
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('--user-url--/something/user-script-url', '--base-url--/web/')->andReturn(false);
        $this->_mockStringUtils->shouldReceive('startsWith')->once()->with('--user-url--/something/user-script-url', '--user-url--/')->andReturn(true);

        $mockEvent = $this->mock('tubepress_api_event_EventInterface');
        $this->_sut->onAction_init($mockEvent);
        $this->assertTrue(true);
    }
}
