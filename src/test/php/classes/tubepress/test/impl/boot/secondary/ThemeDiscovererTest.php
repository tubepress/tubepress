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
 * @covers tubepress_impl_boot_secondary_ThemeDiscoverer<extended>
 */
class tubepress_test_impl_boot_secondary_ThemeDiscovererTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_boot_secondary_ThemeDiscoverer
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

    public function onSetup()
    {
        $this->_mockFinderFactory       = ehough_mockery_Mockery::mock('ehough_finder_FinderFactoryInterface');
        $this->_mockEnvironmentDetector = ehough_mockery_Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_sut                     = new tubepress_impl_boot_secondary_ThemeDiscoverer(

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

    public function testGetContainerParameter()
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

        $mockTemplate = ehough_mockery_Mockery::mock('SplFileInfo');
        $mockTemplate->shouldReceive('getRealPath')->once()->andReturn('mock template path');
        $mockTemplates = array(

            $mockTemplate
        );

        $mockTemplateFinder = ehough_mockery_Mockery::mock('ehough_finder_FinderInterface');
        $mockTemplateFinder->shouldReceive('files')->once()->andReturn($mockTemplateFinder);
        $mockTemplateFinder->shouldReceive('in')->once()->with($this->_mockLegacyThemeDirectory . '/themes/hiya/')->andReturn($mockTemplateFinder);
        $mockTemplateFinder->shouldReceive('name')->once()->with('*.tpl.php')->andReturn($mockTemplates);


        $mockUserThemeSplFileInfo      = ehough_mockery_Mockery::mock('SplFileInfo');
        $mockUserThemeSplFileInfo->shouldReceive('getRealPath')->once()->andReturn('lucky');
        $mockUserThemeSplFileInfoArray = array(

            $mockUserThemeSplFileInfo
        );

        $mockUserFinder = ehough_mockery_Mockery::mock('ehough_finder_FinderInterface');
        $mockUserFinder->shouldReceive('followLinks')->once()->andReturn($mockUserFinder);
        $mockUserFinder->shouldReceive('files')->once()->andReturn($mockUserFinder);
        $mockUserFinder->shouldReceive('in')->once()->with($this->_mockUserThemeDirectory . '/themes')->andReturn($mockUserFinder);
        $mockUserFinder->shouldReceive('name')->once()->with('theme.json')->andReturn($mockUserFinder);
        $mockUserFinder->shouldReceive('depth')->once()->with('< 2')->andReturn($mockUserThemeSplFileInfoArray);

        $mockSystemThemeSplFileInfo      = ehough_mockery_Mockery::mock('SplFileInfo');
        $mockSystemThemeSplFileInfo->shouldReceive('getRealPath')->once()->andReturn('luciano');
        $mockSystemThemeSplFileInfoArray = array(

            $mockSystemThemeSplFileInfo
        );

        $mockSystemFinder = ehough_mockery_Mockery::mock('ehough_finder_FinderInterface');
        $mockSystemFinder->shouldReceive('followLinks')->once()->andReturn($mockSystemFinder);
        $mockSystemFinder->shouldReceive('files')->once()->andReturn($mockSystemFinder);
        $mockSystemFinder->shouldReceive('in')->once()->with(TUBEPRESS_ROOT . '/src/main/resources/default-themes/')->andReturn($mockSystemFinder);
        $mockSystemFinder->shouldReceive('name')->once()->with('theme.json')->andReturn($mockSystemFinder);
        $mockSystemFinder->shouldReceive('depth')->once()->with('< 2')->andReturn($mockSystemThemeSplFileInfoArray);

        $this->_mockFinderFactory->shouldReceive('createFinder')->andReturn($mockSystemFinder, $mockUserFinder, $mockLegacyFinder, $mockTemplateFinder);

        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->twice()->andReturn($this->_mockUserThemeDirectory, $this->_mockLegacyThemeDirectory);
        $param = $this->_sut->getThemesContainerParameterValue();

        $expected = array(

            'unknown/legacy-hiya' => array(
                'title'       => 'Hiya (legacy)',
                'rootAbsPath' => $this->_mockLegacyThemeDirectory . '/themes/hiya/',
                'styles'      => array(),
                'scripts'     => array(),
                'parent'      => null,
                'templates'   => array(
                    'mock template path'
                ),
                'isSystemTheme' => false,
            )
        );

        $this->assertEquals($expected, $param);
    }

}