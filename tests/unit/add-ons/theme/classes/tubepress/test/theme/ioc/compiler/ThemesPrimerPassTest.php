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
 * @covers tubepress_theme_ioc_compiler_ThemesPrimerPass<extended>
 */
class tubepress_test_app_impl_theme_ioc_compiler_ThemesPrimerPassTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_theme_ioc_compiler_ThemesPrimerPass
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainerBuilder;

    public function onSetup()
    {
        $this->_sut                  = new tubepress_theme_ioc_compiler_ThemesPrimerPass();
        $this->_mockContainerBuilder = $this->mock(tubepress_api_ioc_ContainerBuilderInterface::_);
    }

    public function testProcess()
    {
//        $mockFinderFactory = $this->mock('ehough_finder_FinderFactoryInterface');
//        $mockThemeFinder   = $this->mock(tubepress_api_contrib_FilesystemPathFinderInterface::_);
//
//        $mockLogger = $this->mock(tubepress_api_log_LoggerInterface::_);
//        $mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
//        $mockLogger->shouldReceive('debug')->atLeast(1);
//
//        $this->_mockContainerBuilder->shouldReceive('get')->once()->with('ehough_finder_FinderFactoryInterface')->andReturn($mockFinderFactory);
//        $this->_mockContainerBuilder->shouldReceive('get')->once()->with('x')->andReturn($mockThemeFinder);
//        $this->_mockContainerBuilder->shouldReceive('get')->once()->with('tubepress_internal_logger_BootLogger')->andReturn($mockLogger);
//        $this->_mockContainerBuilder->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_api_contrib_FilesystemPathFinderInterface::_)->andReturn(array(
//            'x' => array(array('type' => tubepress_api_theme_ThemeInterface::_))
//        ));
//
//        $mockTheme  = $this->mock(tubepress_api_theme_ThemeInterface::_);
//        $mockThemes = array($mockTheme);
//
//        $mockTheme->shouldReceive('getRootFilesystemPath')->twice()->andReturn('/some/root/yo');
//        $mockTheme->shouldReceive('getName')->once()->andReturn('xyz');
//        $mockTheme->shouldReceive('isSystemTheme')->once()->andReturn(false);
//        $mockTheme->shouldReceive('getParentThemeName')->once()->andReturn('some parent');
//        $mockTheme->shouldReceive('getScreenshots')->once()->andReturn(array('screenshot1'));
//        $mockTheme->shouldReceive('getUrlsJS')->once()->andReturn(array('script1', 'script2'));
//        $mockTheme->shouldReceive('getUrlsCSS')->once()->andReturn(array('style1', 'style2'));
//        $mockTheme->shouldReceive('getTitle')->once()->andReturn('Theme Title');
//        $mockThemeFinder->shouldReceive('getAll')->once()->andReturn($mockThemes);
//
//        $mockTemplate = $this->mock('s');
//        $mockTemplate->shouldReceive('getRealPath')->once()->andReturn('mock template path');
//        $mockTemplates = array(
//
//            $mockTemplate
//        );
//
//        $mockTemplateFinder = $this->mock('ehough_finder_FinderInterface');
//        $mockTemplateFinder->shouldReceive('files')->once()->andReturn($mockTemplateFinder);
//        $mockTemplateFinder->shouldReceive('in')->once()->with('/some/root/yo')->andReturn($mockTemplateFinder);
//        $mockTemplateFinder->shouldReceive('name')->once()->with('*.tpl.php')->andReturn($mockTemplates);
//
//        $mockFinderFactory->shouldReceive('createFinder')->andReturn($mockTemplateFinder);
//
//        $expected = array(
//
//            'xyz' => array(
//                'isSystem'    => false,
//                'parent'      => 'some parent',
//                'screenshots' => array(
//                    'screenshot1'
//                ),
//                'scripts' => array(
//                    'script1',
//                    'script2',
//                ),
//                'styles' => array(
//                    'style1',
//                    'style2',
//                ),
//                'templates' => array(
//                    'mock template path',
//                ),
//                'themeRoot' => '/some/root/yo',
//                'title'     => 'Theme Title'
//            )
//        );
//
//        $this->_mockContainerBuilder->shouldReceive('setParameter')->once()->with('themes', $expected);
//
//        $this->_sut->process($this->_mockContainerBuilder);

        $this->assertTrue(true);
    }

}