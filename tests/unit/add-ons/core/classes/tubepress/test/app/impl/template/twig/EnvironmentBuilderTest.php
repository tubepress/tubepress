<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_app_impl_template_twig_EnvironmentBuilder<extended>
 */
class tubepress_test_app_impl_template_twig_EnvironmentBuilderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_template_twig_EnvironmentBuilder
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockBootSettings;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTwigLoader;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTranslator;

    /**
     * @var string
     */
    private $_mockSystemCacheDir;

    public function onSetup()
    {
        $this->_mockBootSettings = $this->mock(tubepress_platform_api_boot_BootSettingsInterface::_);
        $this->_mockTwigLoader   = $this->mock('Twig_LoaderInterface');
        $this->_mockContext      = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockTranslator   = $this->mock(tubepress_lib_api_translation_TranslatorInterface::_);

        $this->_mockSystemCacheDir = sys_get_temp_dir() . '/environment-builder-test';
        mkdir($this->_mockSystemCacheDir, 0755, true);

        $this->_sut = new tubepress_app_impl_template_twig_EnvironmentBuilder(
            $this->_mockTwigLoader,
            $this->_mockBootSettings,
            $this->_mockContext,
            $this->_mockTranslator
        );
    }

    public function onTearDown()
    {
        $this->recursivelyDeleteDirectory($this->_mockSystemCacheDir);
    }

    public function testBuild()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::TEMPLATE_CACHE_ENABLED)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::TEMPLATE_CACHE_DIR)->andReturn('/abc');
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::TEMPLATE_CACHE_AUTORELOAD)->andReturn(true);
        $this->_mockBootSettings->shouldReceive('getPathToSystemCacheDirectory')->once()->andReturn($this->_mockSystemCacheDir);

        $environment = $this->_sut->buildTwigEnvironment();

        $this->assertInstanceOf('Twig_Environment', $environment);
    }
}