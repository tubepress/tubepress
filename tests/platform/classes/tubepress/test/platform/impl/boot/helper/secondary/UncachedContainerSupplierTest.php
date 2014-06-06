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
 * @runTestsInS eparateProcesses
 * @preserveGl obalState disabled
 * @covers tubepress_impl_boot_helper_secondary_UncachedContainerSupplier<extended>
 */
class tubepress_test_impl_boot_secondary_UncachedSecondaryBootstrapperTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockIocHelper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockAddonDiscoverer;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockClassLoaderHelper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainerBuilder;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainerDumper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockSettingsFileReader;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockClassLoader;

    /**
     * @var tubepress_impl_boot_helper_secondary_UncachedContainerSupplier
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockLogger             = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockAddonDiscoverer    = $this->mock(tubepress_api_contrib_RegistryInterface::_);
        $this->_mockIocHelper          = $this->mock('tubepress_impl_boot_helper_secondary_IocCompiler');
        $this->_mockClassLoaderHelper  = $this->mock('tubepress_impl_boot_helper_secondary_ClassLoaderPrimer');
        $this->_mockContainerBuilder   = $this->mock('tubepress_impl_ioc_ContainerBuilder');
        $this->_mockContainerDumper    = $this->mock('ehough_iconic_dumper_DumperInterface');
        $this->_mockClassLoader        = $this->mock('ehough_pulsar_ComposerClassLoader');
        $this->_mockSettingsFileReader = $this->mock('tubepress_impl_boot_BootSettings');

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_impl_boot_helper_secondary_UncachedContainerSupplier(

            $this->_mockLogger,
            $this->_mockClassLoaderHelper,
            $this->_mockAddonDiscoverer,
            $this->_mockIocHelper,
            $this->_mockClassLoader,
            $this->_mockSettingsFileReader
        );

        $this->_sut->__setContainerBuilder($this->_mockContainerBuilder);
        $this->_sut->__setContainerDumper($this->_mockContainerDumper);
    }

    public function testGetContainerEvalFallback()
    {
        $this->_setupMocks(3);

        $this->_mockLogger->shouldReceive('error')->once()->with('Could not write service container to /abcdef.');
        $this->_mockSettingsFileReader->shouldReceive('getPathToContainerCacheFile')->once()->andReturn('/abcdef');

        $result = $this->_sut->getNewIconicContainer();

        $this->assertInstanceOf('ehough_iconic_ContainerInterface', $result);
    }

    public function testGetContainerSuccessfullySaved()
    {
        $this->_setupMocks(2);

        $tmp = tmpfile();
        $metaDatas = stream_get_meta_data($tmp);
        $tmpFilename = $metaDatas['uri'];
        $this->_mockSettingsFileReader->shouldReceive('getPathToContainerCacheFile')->once()->andReturn($tmpFilename);

        $result = $this->_sut->getNewIconicContainer();

        $this->assertInstanceOf('ehough_iconic_ContainerInterface', $result);
    }

    private function _setupMocks($times)
    {
        $mockAddons = $this->createMockAddonArray();
        $mockIconicBuilder = $this->mock('ehough_iconic_ContainerBuilder');

        $this->_mockSettingsFileReader->shouldReceive('isClassLoaderEnabled')->once()->andReturn(true);
        $this->_mockSettingsFileReader->shouldReceive('isContainerCacheEnabled')->once()->andReturn(true);
        $this->_mockClassLoaderHelper->shouldReceive('addClassHintsForAddons')->once()->with($mockAddons, $this->_mockClassLoader);
        $this->_mockAddonDiscoverer->shouldReceive('getAll')->once()->andReturn($mockAddons);
        $this->_mockIocHelper->shouldReceive('compile')->once()->with($this->_mockContainerBuilder, $mockAddons);
        $this->_mockContainerBuilder->shouldReceive('getDelegateContainerBuilder')->times($times)->andReturn($mockIconicBuilder);
        $this->_mockContainerDumper->shouldReceive('dump')->once()->with(array(

            'class' => 'TubePressServiceContainer'
        ))->andReturn('<?php class TubePressServiceContainer extends ehough_iconic_Container {}');

        $this->_mockContainerBuilder->shouldReceive('set')->once()->with('tubepress_impl_log_BootLogger', $this->_mockLogger);
        $this->_mockContainerBuilder->shouldReceive('set')->once()->with(tubepress_api_boot_BootSettingsInterface::_, $this->_mockSettingsFileReader);
        $this->_mockContainerBuilder->shouldReceive('set')->once()->with('ehough_iconic_ContainerInterface', $mockIconicBuilder);
        $this->_mockContainerBuilder->shouldReceive('set')->once()->with('tubepress_api_ioc_ContainerInterface', $this->_mockContainerBuilder);

        $this->_mockContainerBuilder->shouldReceive('setParameter')->once()->with('classMap', ehough_mockery_Mockery::on('is_array'));
    }

    private function createMockAddonArray()
    {
        return array(

            $this->mock('tubepress_api_addon_AddonInterface')
        );
    }
}