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
 * @covers tubepress_wordpress_impl_listeners_wp_PublicActionsAndFilters
 */
class tubepress_test_wordpress_impl_listeners_wp_PublicActionsAndFiltersTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_listeners_wp_PublicActionsAndFilters
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStorageManager;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockShortcodeParser;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockShortcodeHtmlGenerator;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMessageService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStringUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeHandler;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTranslator;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockAjaxHandler;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParams;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockThemeHandler             = $this->mock(tubepress_app_theme_api_ThemeLibraryInterface::_);
        $this->_mockWordPressFunctionWrapper = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_mockExecutionContext         = $this->mock(tubepress_app_options_api_ContextInterface::_);
        $this->_mockMessageService           = $this->mock(tubepress_lib_translation_api_TranslatorInterface::_);
        $this->_mockShortcodeHtmlGenerator   = $this->mock(tubepress_app_html_api_HtmlGeneratorInterface::_);
        $this->_mockShortcodeParser          = $this->mock(tubepress_app_shortcode_api_ParserInterface::_);
        $this->_mockStorageManager           = $this->mock(tubepress_app_options_api_PersistenceInterface::_);
        $this->_mockStringUtils              = $this->mock(tubepress_platform_api_util_StringUtilsInterface::_);
        $this->_mockEventDispatcher          = $this->mock(tubepress_lib_event_api_EventDispatcherInterface::_);
        $this->_mockAjaxHandler              = $this->mock(tubepress_app_http_api_AjaxCommandInterface::_);
        $this->_mockHttpRequestParams        = $this->mock(tubepress_app_http_api_RequestParametersInterface::_);
        $this->_mockTranslator               = $this->mock(tubepress_lib_translation_api_TranslatorInterface::_);

        $this->_sut = new tubepress_wordpress_impl_listeners_wp_PublicActionsAndFilters(

            $this->_mockWordPressFunctionWrapper,
            $this->_mockThemeHandler,
            $this->_mockStringUtils,
            $this->_mockShortcodeHtmlGenerator,
            $this->_mockAjaxHandler,
            $this->_mockHttpRequestParams,
            $this->_mockExecutionContext,
            $this->_mockStorageManager,
            $this->_mockShortcodeParser,
            $this->_mockTranslator,
            $this->_mockEventDispatcher
        );
    }

    public function testWidgetInitAction()
    {
        $widgetOps = array('classname' => 'widget_tubepress', 'description' => 'X');

        $this->_mockTranslator->shouldReceive('_')->once()->with('Displays YouTube or Vimeo videos with TubePress')->andReturn('X');

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_sidebar_widget')->once()->with('tubepress', 'TubePress', array($this->_sut, 'printWidgetHtml'), $widgetOps);
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_widget_control')->once()->with('tubepress', 'TubePress', array($this->_sut, 'printControlHtml'));

        $mockEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $this->_sut->onAction_widgets_init($mockEvent);
        $this->assertTrue(true);
    }

    public function testWpHeadExecuteInsideAdmin()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(true);

        $mockEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $this->_sut->onAction_wp_head($mockEvent);
        $this->assertTrue(true);
    }

    public function testWpHeadExecuteOutsideAdmin()
    {

        $this->_mockShortcodeHtmlGenerator->shouldReceive('getCssHtml')->once()->andReturn('hello there');
        $this->_mockShortcodeHtmlGenerator->shouldReceive('getJsHtml')->once()->andReturn('goodbye now');

        $this->_mockWordPressFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(false);

        $this->expectOutputString('hello theregoodbye now');

        $mockEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $this->_sut->onAction_wp_head($mockEvent);

        $this->assertTrue(true);
    }

    public function testPrintWidgetControlHtml()
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()
            ->with(tubepress_wordpress_api_Constants::EVENT_WIDGET_PRINT_CONTROLS);

        $this->_sut->__fireWidgetControlEvent();

        $this->assertTrue(true);
    }

    public function testPrintWidgetHtml()
    {
        $mockEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(array(1))->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(
            tubepress_wordpress_api_Constants::EVENT_WIDGET_PUBLIC_HTML, $mockEvent);

        $this->_sut->__fireWidgetHtmlEvent(array(1));

        $this->assertTrue(true);
    }

    public function testInitAction()
    {
        $mockStyleUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockScriptUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockStyleUrl->shouldReceive('toString')->once()->andReturn('style-url');
        $mockScriptUrl->shouldReceive('toString')->twice()->andReturn('script-url');
        $themeStyles = array($mockStyleUrl);
        $themeScripts = array($mockScriptUrl);
        $this->_mockThemeHandler->shouldReceive('getStylesUrls')->once()->andReturn($themeStyles);
        $this->_mockThemeHandler->shouldReceive('getScriptsUrls')->once()->andReturn($themeScripts);

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_style')->once()->with('tubepress-theme-0', 'style-url');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with('tubepress-theme-0');

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress-theme-0', 'script-url');
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress-theme-0', false, array(), false, false);

        $this->_mockWordPressFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(false);
        $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('tubepress/web/js/tubepress.js', 'tubepress')->andReturn('<tubepressjs>');
        $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('tubepress/web/js/wordpress-ajax.js', 'tubepress')->andReturn('<ajaxjs>');

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress', '<tubepressjs>', array('jquery'));
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress_ajax', '<ajaxjs>', array('tubepress'));

        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress', false, array(), false, false);
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('jquery', false, array(), false, false);
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress_ajax', false, array(), false, false);

        $this->_mockStringUtils->shouldReceive('endsWith')->once()->with('script-url', '/web/js/tubepress.js')->andReturn(false);

        $mockEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $this->_sut->onAction_init($mockEvent);
        $this->assertTrue(true);
    }

    public function testFilterContent()
    {
        $this->_mockStorageManager->shouldReceive('fetch')->once()->with(tubepress_app_shortcode_api_Constants::OPTION_KEYWORD)->andReturn('trigger word');

        $this->_mockShortcodeParser->shouldReceive('somethingToParse')->times(2)->with('the content', 'trigger word')->andReturn(true);
        $this->_mockShortcodeParser->shouldReceive('somethingToParse')->times(2)->with('html for shortcode', 'trigger word')->andReturn(true, false);
        $this->_mockShortcodeParser->shouldReceive('getLastShortcodeUsed')->times(4)->andReturn('<current shortcode>');

        $this->_mockShortcodeHtmlGenerator->shouldReceive('getHtmlForShortcode')->once()->with('the content')->andReturn('html for shortcode');
        $this->_mockShortcodeHtmlGenerator->shouldReceive('getHtmlForShortcode')->once()->with('html for shortcode')->andReturn('html for shortcode');

        $this->_mockExecutionContext->shouldReceive('setEphemeralOptions')->twice()->with(array());

        $mockEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn('the content');
        $mockEvent->shouldReceive('setSubject')->once()->with('html for shortcode');

        $this->_mockStringUtils->shouldReceive('replaceFirst')->once()->with('<current shortcode>', 'html for shortcode', 'the content')->andReturn('html for shortcode');
        $this->_mockStringUtils->shouldReceive('replaceFirst')->once()->with('<current shortcode>', 'html for shortcode', 'html for shortcode')->andReturn('html for shortcode');
        $this->_mockStringUtils->shouldReceive('removeEmptyLines')->twice()->with('html for shortcode')->andReturn('html for shortcode');

        $this->_sut->onFilter_the_content($mockEvent);

        $this->assertTrue(true);
    }
}
