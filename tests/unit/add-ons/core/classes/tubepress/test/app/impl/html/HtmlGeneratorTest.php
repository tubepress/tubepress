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
 * @covers tubepress_app_impl_html_HtmlGenerator
 */
class tubepress_test_app_impl_html_HtmlGeneratorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_html_HtmlGenerator
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeRegistry;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplating;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockCurrentThemeService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironment;

    public function onSetup()
    {
        $this->_mockEventDispatcher     = $this->mock(tubepress_lib_api_event_EventDispatcherInterface::_);
        $this->_mockThemeRegistry       = $this->mock(tubepress_platform_api_contrib_RegistryInterface::_);
        $this->_mockTemplating          = $this->mock(tubepress_lib_api_template_TemplatingInterface::_);
        $this->_mockCurrentThemeService = $this->mock('tubepress_app_impl_theme_CurrentThemeService');
        $this->_mockEnvironment         = $this->mock(tubepress_app_api_environment_EnvironmentInterface::_);

        $this->_sut = new tubepress_app_impl_html_HtmlGenerator(

            $this->_mockEventDispatcher,
            $this->_mockThemeRegistry,
            $this->_mockTemplating,
            $this->_mockCurrentThemeService,
            $this->_mockEnvironment
        );
    }

    public function testHtmlBad()
    {
        $mockGenerationEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('')->andReturn($mockGenerationEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_api_event_Events::HTML_GENERATION, $mockGenerationEvent);
        $mockGenerationEvent->shouldReceive('getSubject')->once()->andReturnNull();

        $mockErrorEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(ehough_mockery_Mockery::type('RuntimeException'))->andReturn($mockErrorEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_api_event_Events::HTML_EXCEPTION_CAUGHT, $mockErrorEvent);

        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('exception/static', ehough_mockery_Mockery::type('array'))->andReturn('abc');

        $actual = $this->_sut->getHtml();

        $this->assertEquals('abc', $actual);
    }

    public function testHtmlOK()
    {
        $mockGenerationEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('')->andReturn($mockGenerationEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_api_event_Events::HTML_GENERATION, $mockGenerationEvent);
        $mockGenerationEvent->shouldReceive('getSubject')->once()->andReturn('abc');

        $actual = $this->_sut->getHtml();

        $this->assertEquals('abc', $actual);
    }

    /**
     * @dataProvider dataForCSSorJS
     */
    public function testGetCSSorJS($invoker, $getter, $themeGetter, $templateName, $templateVars,
                                   $eventName, $parentThemeRetrievals, $baseUrlLookups, $userContentLookups,
                                   $getParentThemeCalls)
    {
        list($finalUrls, $mockCurrentTheme, $mockParentTheme) =
            $this->testGetUrlsCSS($getter, $eventName, $parentThemeRetrievals, $baseUrlLookups, $userContentLookups, $getParentThemeCalls);

        $mockCurrentTheme->shouldReceive($themeGetter)->once()->andReturn('current theme data');
        $mockParentTheme->shouldReceive($themeGetter)->once()->andReturn('parent theme data');

        $finalTemplateVars = array_merge($templateVars, array('urls' => $finalUrls));

        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with($templateName, $finalTemplateVars)->andReturn('abc');

        $actual = $this->_sut->$invoker();

        $this->assertEquals('abc', $actual);
    }

    public function dataForCSSorJS()
    {
        return array(

            array('getCSS', 'getUrlsCSS', 'getInlineCSS', 'cssjs/styles', array('inlineCSS' => 'parent theme datacurrent theme data',), tubepress_app_api_event_Events::HTML_STYLESHEETS, 2, 4, 4, 2),
            array('getJS', 'getUrlsJS', null, 'cssjs/scripts', array(), tubepress_app_api_event_Events::HTML_SCRIPTS, 1, 3, 2, 1),
        );
    }

    /**
     * @dataProvider getDataUrls
     */
    public function testGetUrlsCSS($getter, $eventName, $parentThemeRetrievals, $baseUrlLookups, $userContentUrlLookups,
                                   $getParentThemeCalls)
    {
        $js = $getter === 'getUrlsJS';

        $mockBaseUrl        = $this->mock(tubepress_platform_api_url_UrlInterface::_);
        $mockUserContentUrl = $this->mock(tubepress_platform_api_url_UrlInterface::_);

        $mockThemeUrl1 = $this->mock(tubepress_platform_api_url_UrlInterface::_);
        $mockThemeUrl2 = $this->mock(tubepress_platform_api_url_UrlInterface::_);
        $mockThemeUrls = array($mockThemeUrl1, $mockThemeUrl2);

        $mockParentUrl1 = $this->mock(tubepress_platform_api_url_UrlInterface::_);
        $mockParentUrl2 = $this->mock(tubepress_platform_api_url_UrlInterface::_);
        $mockParentUrls = array($mockParentUrl1, $mockParentUrl2);

        $mockCurrentTheme = $this->mock(tubepress_app_api_theme_ThemeInterface::_);
        $mockParentTheme  = $this->mock(tubepress_app_api_theme_ThemeInterface::_);

        $mockUrlEvent = $this->mock('tubepress_lib_api_event_EventInterface');

        $finalUrls = array_merge($mockParentUrls, $mockThemeUrls);

        if ($js) {

            $mockBaseUrl->shouldReceive('getClone')->once()->andReturn($mockBaseUrl);
            $mockBaseUrl->shouldReceive('addPath')->once()->with('/web/js/tubepress.js');

            array_unshift($finalUrls, $mockBaseUrl);
        }

        $this->_mockCurrentThemeService->shouldReceive('getCurrentTheme')->times($parentThemeRetrievals)->andReturn($mockCurrentTheme);

        $this->_mockEnvironment->shouldReceive('getBaseUrl')->times($baseUrlLookups)->andReturn($mockBaseUrl);
        $this->_mockEnvironment->shouldReceive('getUserContentUrl')->times($userContentUrlLookups)->andReturn($mockUserContentUrl);

        $mockCurrentTheme->shouldReceive($getter)->once()->andReturn($mockThemeUrls);
        $mockCurrentTheme->shouldReceive('getParentThemeName')->times($getParentThemeCalls)->andReturn('parent-theme-name');

        $mockParentTheme->shouldReceive($getter)->once()->andReturn($mockParentUrls);
        $mockParentTheme->shouldReceive('getParentThemeName')->times($getParentThemeCalls)->andReturnNull();

        $this->_mockThemeRegistry->shouldReceive('getInstanceByName')->times($parentThemeRetrievals)->with('parent-theme-name')->andReturn($mockParentTheme);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($finalUrls)->andReturn($mockUrlEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with($eventName, $mockUrlEvent);

        $mockUrlEvent->shouldReceive('getSubject')->once()->andReturn($finalUrls);

        $actual = $this->_sut->$getter();

        $this->assertSame($finalUrls, $actual);

        return array(

            $finalUrls, $mockCurrentTheme, $mockParentTheme
        );
    }

    public function getDataUrls()
    {
        return array(
            array('getUrlsCSS', tubepress_app_api_event_Events::HTML_STYLESHEETS, 1, 2, 2, 1),
            array('getUrlsJS', tubepress_app_api_event_Events::HTML_SCRIPTS, 1, 3, 2, 1),
        );
    }
}
