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
 * @covers tubepress_lib_template_impl_contemplate_TemplateFactory
 */
class tubepress_test_lib_template_impl_contemplate_TemplateFactoryTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_lib_template_impl_contemplate_TemplateFactory
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeLibrary;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLangUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFs;

    public function onSetup()
    {
        $this->_mockLangUtils    = $this->mock(tubepress_platform_api_util_LangUtilsInterface::_);
        $this->_mockLogger       = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $this->_mockThemeLibrary = $this->mock(tubepress_app_theme_api_ThemeLibraryInterface::_);
        $this->_mockFs           = $this->mock('ehough_filesystem_FilesystemInterface');
        $this->_mockThemeLibrary->shouldReceive('getCurrentThemeName')->atLeast(1)->andReturn('abc');

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_lib_template_impl_contemplate_TemplateFactory(
            $this->_mockLogger,
            $this->_mockLangUtils,
            $this->_mockThemeLibrary,
            $this->_mockFs
        );
    }

    public function testFromThemeFails()
    {
        $tempFile2 = tempnam('tubepress', 'test');
        file_put_contents($tempFile2, 'Hello, <?php echo $name; ?>!');

        $this->_mockFs->shouldReceive('isAbsolutePath')->once()->with($tempFile2)->andReturn(false);
        $this->_mockThemeLibrary->shouldReceive('getAbsolutePathToTemplate')->once()->with($tempFile2)->andReturn(null);

        $this->_mockLogger->shouldReceive('error')->once()->with('Unable to load template from any of 1 possible locations');

        $result = $this->_sut->fromFilesystem(array($tempFile2));

        $this->assertNull($result);
    }

    public function testFromTheme()
    {
        $tempFile = tempnam('tubepress', 'test');
        file_put_contents($tempFile, 'Hello, <?php echo $name; ?>!');

        $tempFile2 = tempnam('tubepress', 'test');
        file_put_contents($tempFile2, 'Hello, <?php echo $name; ?>!');

        $this->_mockFs->shouldReceive('isAbsolutePath')->once()->with($tempFile2)->andReturn(false);
        $this->_mockThemeLibrary->shouldReceive('getAbsolutePathToTemplate')->once()->with($tempFile2)->andReturn($tempFile);

        $result = $this->_sut->fromFilesystem(array($tempFile2));

        $this->assertInstanceOf('tubepress_lib_template_impl_contemplate_Template', $result);
    }

    public function testFromAbsPath()
    {
        $tempFile = tempnam('tubepress', 'test');
        file_put_contents($tempFile, 'Hello, <?php echo $name; ?>!');

        $this->_mockFs->shouldReceive('isAbsolutePath')->once()->with($tempFile)->andReturn(true);

        $result = $this->_sut->fromFilesystem(array($tempFile));

        $this->assertInstanceOf('tubepress_lib_template_impl_contemplate_Template', $result);
    }

    public function testFromFilesystemNonStringPath()
    {
        $this->_mockLogger->shouldReceive('error')->once()->with('Unable to load template from any of 1 possible locations');

        $result = $this->_sut->fromFilesystem(array(new stdClass()));

        $this->assertNull($result);
    }

    public function testFromFilesystemNoPathsGiven()
    {
        $this->_mockLogger->shouldReceive('error')->once()->with('Unable to load template from any of 0 possible locations');

        $result = $this->_sut->fromFilesystem(array());

        $this->assertNull($result);
    }
}