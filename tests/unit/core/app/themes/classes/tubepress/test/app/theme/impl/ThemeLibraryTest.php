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
 * @covers tubepress_app_theme_impl_ThemeLibrary<extended>
 */
class tubepress_test_app_theme_impl_ThemeHandlerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_theme_impl_ThemeLibrary
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplateBuilder;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var string
     */
    private $_mockThemeDirectory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLangUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    public function onSetup()
    {
        $this->_mockUrlFactory          = $this->mock(tubepress_lib_url_api_UrlFactoryInterface::_);
        $this->_mockTemplateBuilder     = $this->mock('ehough_contemplate_api_TemplateBuilder');
        $this->_mockContext             = $this->mock(tubepress_app_options_api_ContextInterface::_);
        $this->_mockEnvironmentDetector = $this->mock(tubepress_app_environment_api_EnvironmentInterface::_);
        $this->_mockLangUtils           = $this->mock(tubepress_platform_api_util_LangUtilsInterface::_);
        $this->_mockLogger              = $this->mock(tubepress_platform_api_log_LoggerInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_app_theme_impl_ThemeLibrary(array(

            'tubepress/default' => array(

                'title' => 'TubePress Default Theme',
                'scripts' => array(
                    'foo/bar.js',
                    'fooz/baz.js',
                ),
                'styles' => array(
                    'tip/cup.css',
                    'window/glass.css',
                ),
                'themeRoot' => '/some/otherpath',
                'templates' => array(
                    'what/up.tpl.php',
                    'modern/buddy.tpl.php',
                ),
                'isSystem' => true,
                'screenshots' => array()
            ),
            'cool/theme' => array(

                'title' => 'Some Cool Theme',
                'scripts' => array(
                    'blue/red.js',
                    'orange/pie.js',
                ),
                'styles' => array(
                    'http://foo.edu/car/tire.css',
                    'bike/ride.css',
                ),
                'themeRoot' => '/some/neat',
                'templates' => array(
                    'ac/dc.tpl.php',
                    'ab/ba.tpl.php',
                ),
                'parent' => 'tubepress/default',
                'isSystem' => false,
                'screenshots' => array('one.jpg')
            ),
            'some/theme' => array(

                'title' => 'Some Awesome Theme',
                'scripts' => array(
                    'one/1.js',
                    'two/2.js',
                ),
                'styles' => array(
                    'one/one.css',
                    'two/two.css',
                ),
                'themeRoot' => '/some/path',
                'templates' => array(
                    'one/hello.tpl.php',
                    'two/goodbye.tpl.php',
                ),
                'parent' => 'cool/theme',
                'isSystem' => false,
                'screenshots' => array()
            )
        ),
            $this->_mockContext,
            $this->_mockEnvironmentDetector,
        $this->_mockUrlFactory,
        $this->_mockLangUtils,
        $this->_mockLogger);

        $this->_mockThemeDirectory = sys_get_temp_dir() . '/mock-theme';
        $this->recursivelyDeleteDirectory($this->_mockThemeDirectory);
        mkdir($this->_mockThemeDirectory);

        $this->_mockLangUtils->shouldReceive('isAssociativeArray')->andReturnUsing(function ($arg) {

            $util = new tubepress_platform_impl_util_LangUtils();
            return $util->isAssociativeArray($arg);
        });
    }

    public function onTearDown()
    {
        $this->recursivelyDeleteDirectory($this->_mockThemeDirectory);
    }

    public function testGetScreenshots()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_theme_api_Constants::OPTION_THEME)->andReturn('cool/theme');
        $mockUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockUrl->shouldReceive('toString')->once()->andReturn('http://foo.bar/hello/user');
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentUrl')->once()->andReturn($mockUrl);
        $expected = array(
            'http://foo.bar/hello/user/themes/neat/one.jpg' => 'http://foo.bar/hello/user/themes/neat/one.jpg',
        );

        $actual = $this->_sut->getScreenshots();

        $this->assertEquals($expected, $actual);
    }

    public function testGetScripts()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_theme_api_Constants::OPTION_THEME)->andReturn('some/theme');
        $mockUserContentUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockUserContentUrl->shouldReceive('toString')->times(4)->andReturn('http://foo.bar/hello/user');
        $mockBaseUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockBaseUrl->shouldReceive('toString')->times(3)->andReturn('http://foo.bar/hello');
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentUrl')->times(4)->andReturn($mockUserContentUrl);
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->times(3)->andReturn($mockBaseUrl);


        $expected = array(
            'http://foo.bar/hello/web/js/tubepress.js',
            'http://foo.bar/hello/web/themes/otherpath/foo/bar.js',
            'http://foo.bar/hello/web/themes/otherpath/fooz/baz.js',
            'http://foo.bar/hello/user/themes/neat/blue/red.js',
            'http://foo.bar/hello/user/themes/neat/orange/pie.js',
            'http://foo.bar/hello/user/themes/path/one/1.js',
            'http://foo.bar/hello/user/themes/path/two/2.js'
        );
        $urls = array();
        $mockUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://foo.bar/hello')->andReturn($mockUrl);
        $mockUrl->shouldReceive('addPath')->once()->with('/web/js/tubepress.js')->andReturn($mockUrl);
        $urls[] = $mockUrl;

        foreach ($expected as $url) {
            $mockUrl = $this->mock('tubepress_lib_url_api_UrlString');

            if ($url === 'http://foo.bar/hello/web/js/tubepress.js') {

                continue;
            }

            $this->_mockUrlFactory->shouldReceive('fromString')->once()->with($url)->andReturn($mockUrl);
            $urls[] = $mockUrl;
        }

        $actual = $this->_sut->getScriptsUrls();

        $this->assertCount(count($urls), $actual);

        for ($x = 0; $x < count($actual); $x++) {

            $this->assertSame($actual[$x], $urls[$x]);
        }
    }

    public function testGetStylesWithParent()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_theme_api_Constants::OPTION_THEME)->andReturn('cool/theme');
        $mockUserContentUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockUserContentUrl->shouldReceive('toString')->once()->andReturn('http://foo.bar/hello/user');
        $mockBaseUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockBaseUrl->shouldReceive('toString')->twice()->andReturn('http://foo.bar/hello');
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentUrl')->times(1)->andReturn($mockUserContentUrl);
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->times(2)->andReturn($mockBaseUrl);

        $expected = array(
            'http://foo.bar/hello/web/themes/otherpath/tip/cup.css'        => 1,
            'http://foo.bar/hello/web/themes/otherpath/window/glass.css'   => 1,
            'http://foo.edu/car/tire.css'                                           => 2,
            'http://foo.bar/hello/user/themes/neat/bike/ride.css'                   => 1,
        );
        $urls = array();
        foreach ($expected as $string => $count) {
            $mockUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
            $this->_mockUrlFactory->shouldReceive('fromString')->times($count)->with($string)->andReturn($mockUrl);
            $urls[] = $mockUrl;
        }

        $actual = $this->_sut->getStylesUrls();

        $this->assertCount(count($urls), $actual);

        for ($x = 0; $x < count($actual); $x++) {

            $this->assertSame($urls[$x], $actual[$x]);
        }
    }

    public function testGetStylesNoParent()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_theme_api_Constants::OPTION_THEME)->andReturn('tubepress/default');
        $mockBaseUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
        $mockBaseUrl->shouldReceive('toString')->twice()->andReturn('http://foo.bar/hello');
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->times(2)->andReturn($mockBaseUrl);

        $expected = array(
            'http://foo.bar/hello/web/themes/otherpath/tip/cup.css'     => 1,
            'http://foo.bar/hello/web/themes/otherpath/window/glass.css'=> 1
        );
        $urls = array();
        foreach ($expected as $string => $count) {
            $mockUrl = $this->mock('tubepress_lib_url_api_UrlInterface');
            $this->_mockUrlFactory->shouldReceive('fromString')->times($count)->with($string)->andReturn($mockUrl);
            $urls[] = $mockUrl;
        }

        $actual = $this->_sut->getStylesUrls();

        $this->assertCount(count($urls), $actual);

        for ($x = 0; $x < count($actual); $x++) {

            $this->assertSame($urls[$x], $actual[$x]);
        }
    }

    public function testGetTemplateInstanceDirectHitWithLeadingSlashes()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_theme_api_Constants::OPTION_THEME)->andReturn('some/theme');

        $template = $this->_sut->getAbsolutePathToTemplate('////one/hello.tpl.php');

        $this->assertEquals('/some/path/one/hello.tpl.php', $template);
    }

    public function testGetTemplateInstanceDirectHit()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_theme_api_Constants::OPTION_THEME)->andReturn('some/theme');

        $template = $this->_sut->getAbsolutePathToTemplate('one/hello.tpl.php');

        $this->assertEquals('/some/path/one/hello.tpl.php', $template);
    }

    public function testGetTemplateInstanceFromParent()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_theme_api_Constants::OPTION_THEME)->andReturn('some/theme');

        $template = $this->_sut->getAbsolutePathToTemplate('ac/dc.tpl.php');

        $this->assertEquals('/some/neat/ac/dc.tpl.php', $template);
    }

    public function testGetTemplateInstanceFallBack()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_theme_api_Constants::OPTION_THEME)->andReturn('some/theme');

        $template = $this->_sut->getAbsolutePathToTemplate('foo.bar');

        $this->assertNull($template);
    }

    public function testGetMapOfAllThemeNamesToTitles()
    {
        $result = $this->_sut->getMapOfAllThemeNamesToTitles();

        $this->assertEquals(array(
            'tubepress/default' => 'TubePress Default Theme',
            'cool/theme'        => 'Some Cool Theme',
            'some/theme'        => 'Some Awesome Theme',
        ), $result);
    }
}