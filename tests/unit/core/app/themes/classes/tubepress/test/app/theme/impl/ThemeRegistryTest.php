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
 * @covers tubepress_app_theme_impl_ThemeRegistry<extended>
 */
class tubepress_test_app_theme_impl_ThemeRegistryTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_theme_impl_ThemeRegistry
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFinderFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockBootSettings;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockValidator;

    /**
     * @var string
     */
    private $_mockUserThemeDirectory;

    /**
     * @var string
     */
    private $_mockLegacyThemeDirectory;


    public function onSetup()
    {
        $this->_mockFinderFactory = $this->mock('ehough_finder_FinderFactoryInterface');
        $this->_mockBootSettings  = $this->mock(tubepress_platform_api_boot_BootSettingsInterface::_);
        $this->_mockLogger        = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $this->_mockValidator     = $this->mock(tubepress_app_contrib_api_ContributableValidatorInterface::_);

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1)->andReturn(true);

        $this->_sut               = new tubepress_app_theme_impl_ThemeRegistry(

            $this->_mockLogger,
            $this->_mockBootSettings,
            $this->_mockFinderFactory,
            $this->_mockValidator
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
        $this->recursivelyDeleteDirectory(TUBEPRESS_ROOT . '/web');
    }

    public function testFindAllThemes()
    {
        $mockLegacyFinder = $this->_setupLegacyThemeMocks();
        $mockSystemFinder = $this->_setupSystemThemeMocks();
        $mockUserFinder   = $this->_setupUserThemeMocks();

        $this->_mockValidator->shouldReceive('isValid')->with('tubepress_app_theme_api_ThemeInterface')->andReturn(true);

        $this->_mockFinderFactory->shouldReceive('createFinder')->andReturn($mockSystemFinder, $mockUserFinder, $mockLegacyFinder);
        $this->_mockBootSettings->shouldReceive('getUserContentDirectory')->twice()->andReturn($this->_mockUserThemeDirectory, $this->_mockLegacyThemeDirectory);

        $themes = $this->_sut->getAll();

        $this->assertTrue(is_array($themes));
        $this->assertTrue(count($themes) === 3);

        $first = $themes[0];
        $this->assertInstanceOf(tubepress_app_theme_api_ThemeInterface::_, $first);
        $this->assertTrue($first->getName() === 'tubepress/default');

        $second = $themes[1];
        $this->assertInstanceOf(tubepress_app_theme_api_ThemeInterface::_, $second);
        $this->assertTrue($second->getName() === 'tubepress/youtube.com-clone');

        $third = $themes[2];
        $this->assertInstanceOf(tubepress_app_theme_api_ThemeInterface::_, $third);
        $this->assertEquals('unknown/legacy-hiya', $third->getName());
    }

    private function _setupLegacyThemeMocks()
    {
        $mockLegacyThemeSplFileInfo = $this->mock('stdClass');
        $mockLegacyThemeSplFileInfo->shouldReceive('getPathname')->andReturn($this->_mockLegacyThemeDirectory . '/themes/hiya');
        $mockLegacyThemesSplFileInfoArray = array(

            $mockLegacyThemeSplFileInfo
        );

        $mockLegacyFinder = $this->mock('ehough_finder_FinderInterface');
        $mockLegacyFinder->shouldReceive('directories')->once()->andReturn($mockLegacyFinder);
        $mockLegacyFinder->shouldReceive('in')->once()->with($this->_mockLegacyThemeDirectory . '/themes')->andReturn($mockLegacyFinder);
        $mockLegacyFinder->shouldReceive('depth')->once()->with('< 1')->andReturn($mockLegacyThemesSplFileInfoArray);

        return $mockLegacyFinder;
    }

    private function _setupSystemThemeMocks()
    {
        mkdir(TUBEPRESS_ROOT . '/web/themes', 0755, true);

        $fs = new ehough_filesystem_Filesystem();
        $fs->mirror(TUBEPRESS_ROOT . '/src/core/app/themes/web', TUBEPRESS_ROOT . '/web/themes');

        $mockSystemThemeSplFileInfo = $this->mock('stdClass');
        $mockSystemThemeSplFileInfo->shouldReceive('getPathname')->twice()->andReturn(TUBEPRESS_ROOT . '/web/themes/default/theme.json');
        $mockSystemThemeSplFileInfoArray = array(

            $mockSystemThemeSplFileInfo
        );

        $mockSystemFinder = $this->mock('ehough_finder_FinderInterface');
        $mockSystemFinder->shouldReceive('followLinks')->once()->andReturn($mockSystemFinder);
        $mockSystemFinder->shouldReceive('files')->once()->andReturn($mockSystemFinder);
        $mockSystemFinder->shouldReceive('in')->once()->with(TUBEPRESS_ROOT . '/web/themes')->andReturn($mockSystemFinder);
        $mockSystemFinder->shouldReceive('name')->once()->with('theme.json')->andReturn($mockSystemFinder);
        $mockSystemFinder->shouldReceive('depth')->once()->with('< 3')->andReturn($mockSystemThemeSplFileInfoArray);

        return $mockSystemFinder;
    }

    private function _setupUserThemeMocks()
    {
        $fs = new ehough_filesystem_Filesystem();
        $fs->copy(TUBEPRESS_ROOT . '/src/core/app/themes/web/youtube.com-clone/theme.json', $this->_mockUserThemeDirectory . '/themes/something/theme.json');

        $mockUserThemeSplFileInfo      = $this->mock('stdClass');
        $mockUserThemeSplFileInfo->shouldReceive('getPathname')->twice()->andReturn($this->_mockUserThemeDirectory . '/themes/something/theme.json');
        $mockUserThemeSplFileInfoArray = array(

            $mockUserThemeSplFileInfo
        );

        $mockUserFinder = $this->mock('ehough_finder_FinderInterface');
        $mockUserFinder->shouldReceive('followLinks')->once()->andReturn($mockUserFinder);
        $mockUserFinder->shouldReceive('files')->once()->andReturn($mockUserFinder);
        $mockUserFinder->shouldReceive('in')->once()->with($this->_mockUserThemeDirectory . '/themes')->andReturn($mockUserFinder);
        $mockUserFinder->shouldReceive('name')->once()->with('theme.json')->andReturn($mockUserFinder);
        $mockUserFinder->shouldReceive('depth')->once()->with('< 3')->andReturn($mockUserThemeSplFileInfoArray);

        return $mockUserFinder;
    }

}