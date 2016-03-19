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
 * @covers tubepress_wordpress_impl_listeners_wpaction_AdminHeadAndScriptsListener
 */
class tubepress_test_wordpress_impl_listeners_wpaction_AdminHeadAndScriptsListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_listeners_wpaction_AdminHeadAndScriptsListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockWordPressFunctionWrapper;

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

    public function onSetup()
    {
        $this->_mockWordPressFunctionWrapper    = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_mockForm                        = $this->mock(tubepress_api_options_ui_FormInterface::_);
        $this->_mockStringUtils                 = $this->mock(tubepress_api_util_StringUtilsInterface::_);
        $this->_mockEnvironment                 = $this->mock(tubepress_api_environment_EnvironmentInterface::_);

        $this->_sut = new tubepress_wordpress_impl_listeners_wpaction_AdminHeadAndScriptsListener(

            $this->_mockWordPressFunctionWrapper,
            $this->_mockForm,
            $this->_mockStringUtils,
            $this->_mockEnvironment
        );
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

        $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('/web/system-css-url', basename(TUBEPRESS_ROOT) . '/tubepress.php')->andReturn('<<system-style-url>>');
        $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('/web/system-js-url', basename(TUBEPRESS_ROOT) . '/tubepress.php')->andReturn('<<system-script-url>>');
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
}
