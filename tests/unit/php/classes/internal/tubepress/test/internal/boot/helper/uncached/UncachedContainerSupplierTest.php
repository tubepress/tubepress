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
 * @covers tubepress_internal_boot_helper_uncached_UncachedContainerSupplier<extended>
 */
class tubepress_test_internal_boot_helper_uncached_UncachedContainerSupplierTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var Mockery\MockInterface
     */
    private $_mockCompiler;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockManifestFinder;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContainerBuilder;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContainerDumper;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockLogger;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockBootSettings;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockAddonFactory;

    /**
     * @var tubepress_internal_boot_helper_uncached_UncachedContainerSupplier
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockLogger           = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockManifestFinder   = $this->mock('tubepress_internal_boot_helper_uncached_contrib_ManifestFinder');
        $this->_mockCompiler         = $this->mock('tubepress_internal_boot_helper_uncached_Compiler');
        $this->_mockContainerBuilder = $this->mock('tubepress_internal_ioc_ContainerBuilder');
        $this->_mockContainerDumper  = $this->mock('ehough_iconic_dumper_DumperInterface');
        $this->_mockBootSettings     = $this->mock('tubepress_internal_boot_BootSettings');
        $this->_mockAddonFactory     = $this->mock('tubepress_internal_boot_helper_uncached_contrib_AddonFactory');

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_internal_boot_helper_uncached_UncachedContainerSupplier(

            $this->_mockLogger,
            $this->_mockManifestFinder,
            $this->_mockAddonFactory,
            $this->_mockCompiler,
            $this->_mockBootSettings
        );

        $this->_sut->__setContainerBuilder($this->_mockContainerBuilder);
        $this->_sut->__setContainerDumper($this->_mockContainerDumper);
        $this->_sut->__setSerializer(array($this, '__callbackSerialize'));

        Mockery::ducktype();
    }

    public function onTeardown()
    {
        $this->recursivelyDeleteDirectory(sys_get_temp_dir() . '/tubepress-uncached-test');
    }

    public function testCannotWriteToDir()
    {
        $this->_setupMocks(3);

        $this->_mockLogger->shouldReceive('error')->once()->with('Failed to create all the parent directories of /abcdef/TubePress-99.99.99-ServiceContainer.php');
        $this->_mockBootSettings->shouldReceive('getPathToSystemCacheDirectory')->once()->andReturn('/abcdef');

        $result = $this->_sut->getNewIconicContainer();

        $this->assertInstanceOf('ehough_iconic_ContainerInterface', $result);
    }

    public function testGetContainerSuccessfullySaved()
    {
        $this->_setupMocks(2);

        $mockSystemDir = sys_get_temp_dir();

        $this->_mockBootSettings->shouldReceive('getPathToSystemCacheDirectory')->once()->andReturn($mockSystemDir);

        $result = $this->_sut->getNewIconicContainer();

        $this->assertFileExists($mockSystemDir . '/TubePress-99.99.99-ServiceContainer.php');

        $this->assertInstanceOf('ehough_iconic_ContainerInterface', $result);
    }

    private function _setupMocks($times)
    {
        $mockManifestPaths = array(
            'one' => array('two', 'three'),
            'foo' => array('baz' => array('somethin'))
        );
        $mockIconicBuilder = $this->mock('ehough_iconic_ContainerBuilder');
        $mockAddon1        = $this->mock('tubepress_api_contrib_AddonInterface');
        $mockAddon2        = $this->mock('tubepress_api_contrib_AddonInterface');
        $mockAddons        = array($mockAddon1, $mockAddon2);

        $mockAddon1->shouldReceive('getName')->atLeast(1)->andReturn('add-on 1 name');
        $mockAddon2->shouldReceive('getName')->atLeast(1)->andReturn('add-on 2 name');
        $mockAddon1->shouldReceive('getClassMap')->once()->andReturn(array('classA' => 'classARelativePath'));
        $mockAddon2->shouldReceive('getClassMap')->once()->andReturn(array('classB' => 'classBRelativePath'));

        $this->_mockBootSettings->shouldReceive('isClassLoaderEnabled')->twice()->andReturn(true);
        $this->_mockBootSettings->shouldReceive('isSystemCacheEnabled')->once()->andReturn(true);

        $this->_mockManifestFinder->shouldReceive('find')->once()->andReturn($mockManifestPaths);

        $this->_mockAddonFactory->shouldReceive('fromManifestData')->once()
            ->with('one', array('two', 'three'))->andReturn($mockAddon1);
        $this->_mockAddonFactory->shouldReceive('fromManifestData')->once()
            ->with('foo', array('baz' => array('somethin')))->andReturn($mockAddon2);

        $this->_mockCompiler->shouldReceive('compile')->once()->with($this->_mockContainerBuilder, $mockAddons);

        $this->_mockContainerDumper->shouldReceive('dump')->once()->with(array(

            'class' => 'TubePressServiceContainer'
        ))->andReturn('<?php class TubePressServiceContainer extends ehough_iconic_Container {}');

        $this->_mockContainerBuilder->shouldReceive('getDelegateContainerBuilder')->times($times)->andReturn($mockIconicBuilder);
        $this->_mockContainerBuilder->shouldReceive('set')->once()->with('tubepress_internal_logger_BootLogger', $this->_mockLogger);
        $this->_mockContainerBuilder->shouldReceive('set')->once()->with(tubepress_api_boot_BootSettingsInterface::_, $this->_mockBootSettings);
        $this->_mockContainerBuilder->shouldReceive('set')->once()->with('ehough_iconic_ContainerInterface', $mockIconicBuilder);
        $this->_mockContainerBuilder->shouldReceive('set')->once()->with('tubepress_api_ioc_ContainerInterface', $this->_mockContainerBuilder);
        $this->_mockContainerBuilder->shouldReceive('setParameter')->once()->with(
            tubepress_internal_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS,
            array('add-ons' => 'hiya')
        );
        $this->_mockContainerBuilder->shouldReceive('getParameter')->once()->with(tubepress_internal_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS)->andReturn(array('ww' => 'xx'));
        $this->_mockContainerBuilder->shouldReceive('setParameter')->once()->with(tubepress_internal_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS, Mockery::on(function ($arr) {

            $ok = is_array($arr);

            $ok = $ok && $arr['ww'] === 'xx';
            $ok = $ok && is_array($arr['classloading']['map']);

            return $ok;
        }));
    }

    public function __callbackSerialize(array $addons, tubepress_api_boot_BootSettingsInterface $bootSettings)
    {
        return 'hiya';
    }
}