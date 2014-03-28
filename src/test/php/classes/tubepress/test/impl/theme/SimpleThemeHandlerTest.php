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
 * @covers tubepress_impl_theme_ThemeHandler<extended>
 */
class tubepress_test_impl_theme_SimpleThemeHandlerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_theme_ThemeHandler
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
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var string
     */
    private $_mockThemeDirectory;

    public function onSetup()
    {
        $this->_mockTemplateBuilder     = $this->createMockSingletonService('ehough_contemplate_api_TemplateBuilder');
        $this->_mockContext             = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);

        $this->_sut = new tubepress_impl_theme_ThemeHandler(array(

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
        ));

        $this->_mockThemeDirectory = sys_get_temp_dir() . '/mock-theme';
        $this->recursivelyDeleteDirectory($this->_mockThemeDirectory);
        mkdir($this->_mockThemeDirectory);
    }

    public function onTearDown()
    {
        $this->recursivelyDeleteDirectory($this->_mockThemeDirectory);
    }

    public function testGetScreenshots()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THEME)->andReturn('cool/theme');
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentUrl')->once()->andReturn('http://foo.bar/hello/user');

        $actual = $this->_sut->getScreenshots();
        $expected = array(
            'http://foo.bar/hello/user/themes/neat/one.jpg' => 'http://foo.bar/hello/user/themes/neat/one.jpg',
        );

        $this->assertEquals($expected, $actual);
    }

    public function testGetScripts()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THEME)->andReturn('some/theme');
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->times(3)->andReturn('http://foo.bar/hello');
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentUrl')->times(4)->andReturn('http://foo.bar/hello/user');

        $actual = $this->_sut->getScripts();
        $expected = array(
            'http://foo.bar/hello/src/main/web/js/tubepress.js',
            'http://foo.bar/hello/src/main/web/themes/otherpath/foo/bar.js',
            'http://foo.bar/hello/src/main/web/themes/otherpath/fooz/baz.js',
            'http://foo.bar/hello/user/themes/neat/blue/red.js',
            'http://foo.bar/hello/user/themes/neat/orange/pie.js',
            'http://foo.bar/hello/user/themes/path/one/1.js',
            'http://foo.bar/hello/user/themes/path/two/2.js'
        );

        $this->assertEquals($expected, $actual);
    }

    public function testGetStylesWithParent()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THEME)->andReturn('cool/theme');
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->twice()->andReturn('http://foo.bar/hello');
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentUrl')->once()->andReturn('http://foo.bar/hello/user');

        $actual = $this->_sut->getStyles();
        $expected = array(
            'http://foo.bar/hello/src/main/web/themes/otherpath/tip/cup.css',
            'http://foo.bar/hello/src/main/web/themes/otherpath/window/glass.css',
            'http://foo.edu/car/tire.css',
            'http://foo.bar/hello/user/themes/neat/bike/ride.css',
        );

        $this->assertEquals($expected, $actual);
    }

    public function testGetStylesNoParent()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THEME)->andReturn('tubepress/default');
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->twice()->andReturn('http://foo.bar/hello');

        $actual = $this->_sut->getStyles();
        $expected = array(
            'http://foo.bar/hello/src/main/web/themes/otherpath/tip/cup.css',
            'http://foo.bar/hello/src/main/web/themes/otherpath/window/glass.css'
        );

        $this->assertEquals($expected, $actual);
    }

    public function testGetTemplateInstanceDirectHitWithLeadingSlashes()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THEME)->andReturn('some/theme');
        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_TemplateInterface');
        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with('/some/path/one/hello.tpl.php')->andReturn($mockTemplate);

        $template = $this->_sut->getTemplateInstance('////one/hello.tpl.php', $this->_mockThemeDirectory);

        $this->assertInstanceOf('ehough_contemplate_api_TemplateInterface', $template);
    }

    public function testGetTemplateInstanceDirectHit()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THEME)->andReturn('some/theme');
        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_TemplateInterface');
        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with('/some/path/one/hello.tpl.php')->andReturn($mockTemplate);

        $template = $this->_sut->getTemplateInstance('one/hello.tpl.php', $this->_mockThemeDirectory);

        $this->assertInstanceOf('ehough_contemplate_api_TemplateInterface', $template);
    }

    public function testGetTemplateInstanceFromParent()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THEME)->andReturn('some/theme');
        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_TemplateInterface');
        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with('/some/neat/ac/dc.tpl.php')->andReturn($mockTemplate);

        $template = $this->_sut->getTemplateInstance('ac/dc.tpl.php', $this->_mockThemeDirectory);

        $this->assertInstanceOf('ehough_contemplate_api_TemplateInterface', $template);
    }

    public function testGetTemplateInstanceFallBack()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THEME)->andReturn('some/theme');
        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_TemplateInterface');
        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with($this->_mockThemeDirectory . '/foo.bar')->andReturn($mockTemplate);
        file_put_contents($this->_mockThemeDirectory . '/foo.bar', 'hello!');

        $template = $this->_sut->getTemplateInstance('foo.bar', $this->_mockThemeDirectory);

        $this->assertSame($mockTemplate, $template);
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