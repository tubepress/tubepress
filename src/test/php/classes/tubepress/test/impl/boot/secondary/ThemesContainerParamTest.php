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
 * @covers tubepress_impl_boot_secondary_ThemesContainerParam<extended>
 */
class tubepress_test_impl_boot_secondary_ThemesContainerParamTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_boot_secondary_ThemesContainerParam
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFinderFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeFinder;

    public function onSetup()
    {
        $this->_mockFinderFactory = ehough_mockery_Mockery::mock('ehough_finder_FinderFactoryInterface');
        $this->_mockThemeFinder   = ehough_mockery_Mockery::mock(tubepress_spi_theme_ThemeFinderInterface::_);
        $this->_sut               = new tubepress_impl_boot_secondary_ThemesContainerParam(

            $this->_mockThemeFinder,
            $this->_mockFinderFactory
        );
    }

    public function testGetContainerParameter()
    {
        $mockTheme  = ehough_mockery_Mockery::mock(tubepress_spi_theme_ThemeInterface::_);
        $mockThemes = array($mockTheme);

        $mockTheme->shouldReceive('getRootFilesystemPath')->twice()->andReturn('/some/root/yo');
        $mockTheme->shouldReceive('getName')->once()->andReturn('xyz');
        $mockTheme->shouldReceive('isSystemTheme')->once()->andReturn(false);
        $mockTheme->shouldReceive('getParentThemeName')->once()->andReturn('some parent');
        $mockTheme->shouldReceive('getScreenshots')->once()->andReturn(array('screenshot1'));
        $mockTheme->shouldReceive('getScripts')->once()->andReturn(array('script1', 'script2'));
        $mockTheme->shouldReceive('getStyles')->once()->andReturn(array('style1', 'style2'));
        $mockTheme->shouldReceive('getTitle')->once()->andReturn('Theme Title');
        $this->_mockThemeFinder->shouldReceive('findAllThemes')->once()->andReturn($mockThemes);

        $mockTemplate = ehough_mockery_Mockery::mock('SplFileInfo');
        $mockTemplate->shouldReceive('getRealPath')->once()->andReturn('mock template path');
        $mockTemplates = array(

            $mockTemplate
        );

        $mockTemplateFinder = ehough_mockery_Mockery::mock('ehough_finder_FinderInterface');
        $mockTemplateFinder->shouldReceive('files')->once()->andReturn($mockTemplateFinder);
        $mockTemplateFinder->shouldReceive('in')->once()->with('/some/root/yo')->andReturn($mockTemplateFinder);
        $mockTemplateFinder->shouldReceive('name')->once()->with('*.tpl.php')->andReturn($mockTemplates);

        $this->_mockFinderFactory->shouldReceive('createFinder')->andReturn($mockTemplateFinder);

        $actual = $this->_sut->getThemesContainerParameterValue();

        $expected = array(

            'xyz' => array(
                'isSystem'    => false,
                'parent'      => 'some parent',
                'screenshots' => array(
                    'screenshot1'
                ),
                'scripts' => array(
                    'script1',
                    'script2',
                ),
                'styles' => array(
                    'style1',
                    'style2',
                ),
                'templates' => array(
                    'mock template path',
                ),
                'themeRoot' => '/some/root/yo',
                'title'     => 'Theme Title'
            )
        );

        $this->assertEquals($expected, $actual);
    }

}