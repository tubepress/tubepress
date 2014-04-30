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
 * @covers tubepress_impl_theme_ThemeFinder<extended>
 */
class tubepress_test_impl_theme_ThemeFinderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_theme_ThemeFinder
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFinderFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var string
     */
    private $_mockUserThemeDirectory;

    /**
     * @var string
     */
    private $_mockLegacyThemeDirectory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    public function onSetup()
    {
        $this->_mockFinderFactory       = ehough_mockery_Mockery::mock('ehough_finder_FinderFactoryInterface');
        $this->_mockEnvironmentDetector = ehough_mockery_Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockUrlFactory          = $this->createMockSingletonService(tubepress_spi_url_UrlFactoryInterface::_);
        $this->_sut                     = new tubepress_impl_theme_ThemeFinder(

            $this->_mockFinderFactory,
            $this->_mockEnvironmentDetector
        );

        $this->_mockUserThemeDirectory   = sys_get_temp_dir() . '/mock-user-themes';
        $this->_mockLegacyThemeDirectory = sys_get_temp_dir() . '/mock-legacy-themes';

        $this->recursivelyDeleteDirectory($this->_mockUserThemeDirectory);
        $this->recursivelyDeleteDirectory($this->_mockLegacyThemeDirectory);

        mkdir($this->_mockUserThemeDirectory . '/themes/something', 0755, true);
        mkdir($this->_mockLegacyThemeDirectory . '/themes/hiya', 0755, true);
    }

    public function onTearDown()
    {
        $this->recursivelyDeleteDirectory($this->_mockUserThemeDirectory);
        $this->recursivelyDeleteDirectory($this->_mockLegacyThemeDirectory);
    }

    public function testFindAllThemes()
    {
        $mockLegacyFinder = $this->_setupLegacyThemeMocks();
        $mockSystemFinder = $this->_setupSystemThemeMocks();
        $mockUserFinder   = $this->_setupUserThemeMocks();

        $this->_mockFinderFactory->shouldReceive('createFinder')->andReturn($mockSystemFinder, $mockUserFinder, $mockLegacyFinder);
        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->twice()->andReturn($this->_mockUserThemeDirectory, $this->_mockLegacyThemeDirectory);

        $this->_mockUrlFactory->shouldReceive('fromString')->twice()->with('http://tubepress.com');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://opensource.org/licenses/MIT');
        $this->_mockUrlFactory->shouldReceive('fromString')->twice()->with('http://www.mozilla.org/MPL/2.0/');
        $onceUrls = array(
            'http://themes.tubepress-cdn.com/default/screenshots/1-thumbnail.png',
            'http://themes.tubepress-cdn.com/default/screenshots/1.png',
            'http://themes.tubepress-cdn.com/default/screenshots/2-thumbnail.png',
            'http://themes.tubepress-cdn.com/default/screenshots/2.png',
            'http://themes.tubepress-cdn.com/default/screenshots/3-thumbnail.png',
            'http://themes.tubepress-cdn.com/default/screenshots/3.png',
            'http://themes.tubepress-cdn.com/youtube.com-clone/screenshots/1-thumbnail.png','http://themes.tubepress-cdn.com/youtube.com-clone/screenshots/1.png',
            'http://themes.tubepress-cdn.com/youtube.com-clone/screenshots/2-thumbnail.png','http://themes.tubepress-cdn.com/youtube.com-clone/screenshots/2.png',
        );
        foreach ($onceUrls as $onceUrl) {
            $this->_mockUrlFactory->shouldReceive('fromString')->once()->with($onceUrl);
        }

        $themes = $this->_sut->findAllThemes();

        $this->assertTrue(is_array($themes));
        $this->assertTrue(count($themes) === 3);

        $first = $themes[0];
        $this->assertInstanceOf(tubepress_spi_theme_ThemeInterface::_, $first);
        $this->assertTrue($first->getName() === 'tubepress/default');

        $second = $themes[1];
        $this->assertInstanceOf(tubepress_spi_theme_ThemeInterface::_, $second);
        $this->assertTrue($second->getName() === 'tubepress/youtube.com-clone');

        $third = $themes[2];
        $this->assertInstanceOf(tubepress_spi_theme_ThemeInterface::_, $third);
        $this->assertEquals('unknown/legacy-hiya', $third->getName());
    }

    private function _setupLegacyThemeMocks()
    {
        $mockLegacyThemeSplFileInfo = ehough_mockery_Mockery::mock('SplFileInfo');
        $mockLegacyThemeSplFileInfo->shouldReceive('getRealPath')->andReturn($this->_mockLegacyThemeDirectory . '/themes/hiya');
        $mockLegacyThemesSplFileInfoArray = array(

            $mockLegacyThemeSplFileInfo
        );

        $mockLegacyFinder = ehough_mockery_Mockery::mock('ehough_finder_FinderInterface');
        $mockLegacyFinder->shouldReceive('directories')->once()->andReturn($mockLegacyFinder);
        $mockLegacyFinder->shouldReceive('in')->once()->with($this->_mockLegacyThemeDirectory . '/themes')->andReturn($mockLegacyFinder);
        $mockLegacyFinder->shouldReceive('depth')->once()->with('< 1')->andReturn($mockLegacyThemesSplFileInfoArray);

        return $mockLegacyFinder;
    }

    private function _setupSystemThemeMocks()
    {
        $mockSystemThemeSplFileInfo = ehough_mockery_Mockery::mock('SplFileInfo');
        $mockSystemThemeSplFileInfo->shouldReceive('getRealPath')->twice()->andReturn(TUBEPRESS_ROOT . '/src/main/web/themes/default/theme.json');
        $mockSystemThemeSplFileInfoArray = array(

            $mockSystemThemeSplFileInfo
        );

        $mockSystemFinder = ehough_mockery_Mockery::mock('ehough_finder_FinderInterface');
        $mockSystemFinder->shouldReceive('followLinks')->once()->andReturn($mockSystemFinder);
        $mockSystemFinder->shouldReceive('files')->once()->andReturn($mockSystemFinder);
        $mockSystemFinder->shouldReceive('in')->once()->with(TUBEPRESS_ROOT . '/src/main/web/themes/')->andReturn($mockSystemFinder);
        $mockSystemFinder->shouldReceive('name')->once()->with('theme.json')->andReturn($mockSystemFinder);
        $mockSystemFinder->shouldReceive('depth')->once()->with('< 2')->andReturn($mockSystemThemeSplFileInfoArray);

        return $mockSystemFinder;
    }

    private function _setupUserThemeMocks()
    {
        $fs = new ehough_filesystem_Filesystem();
        $fs->copy(TUBEPRESS_ROOT . '/src/main/web/themes/youtube.com-clone/theme.json', $this->_mockUserThemeDirectory . '/themes/something/theme.json');

        $mockUserThemeSplFileInfo      = ehough_mockery_Mockery::mock('SplFileInfo');
        $mockUserThemeSplFileInfo->shouldReceive('getRealPath')->twice()->andReturn($this->_mockUserThemeDirectory . '/themes/something/theme.json');
        $mockUserThemeSplFileInfoArray = array(

            $mockUserThemeSplFileInfo
        );

        $mockUserFinder = ehough_mockery_Mockery::mock('ehough_finder_FinderInterface');
        $mockUserFinder->shouldReceive('followLinks')->once()->andReturn($mockUserFinder);
        $mockUserFinder->shouldReceive('files')->once()->andReturn($mockUserFinder);
        $mockUserFinder->shouldReceive('in')->once()->with($this->_mockUserThemeDirectory . '/themes')->andReturn($mockUserFinder);
        $mockUserFinder->shouldReceive('name')->once()->with('theme.json')->andReturn($mockUserFinder);
        $mockUserFinder->shouldReceive('depth')->once()->with('< 2')->andReturn($mockUserThemeSplFileInfoArray);

        return $mockUserFinder;
    }

}