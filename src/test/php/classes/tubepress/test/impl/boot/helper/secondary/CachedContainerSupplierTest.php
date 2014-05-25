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
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 * @covers tubepress_impl_boot_helper_secondary_CachedContainerSupplier<extended>
 */
class tubepress_test_impl_boot_helper_secondary_CachedSecondaryBootstrapperTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_boot_helper_secondary_CachedContainerSupplier
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockSettingsFileReader;

    public function onSetup()
    {
        $this->_mockLogger             = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockSettingsFileReader = $this->mock('tubepress_impl_boot_BootSettings');
        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_impl_boot_helper_secondary_CachedContainerSupplier($this->_mockLogger, $this->_mockSettingsFileReader);
    }

    public function testGetContainer()
    {
        $tmpFile = tmpfile();
        $metaDatas = stream_get_meta_data($tmpFile);
        $tmpFilename = $metaDatas['uri'];

        fwrite($tmpFile, $this->getDumpedEmptyIconicContainerBuilder());

        $this->_mockSettingsFileReader->shouldReceive('getPathToContainerCacheFile')->once()->andReturn($tmpFilename);
        $result = $this->_sut->getServiceContainer();

        $this->assertInstanceOf('TubePressServiceContainer', $result);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetContainerNoSuchFile()
    {
        $this->_mockLogger->shouldReceive('error')->once();
        $this->_mockSettingsFileReader->shouldReceive('getPathToContainerCacheFile')->once()->andReturn('abc');
        $this->_sut->getServiceContainer();
    }

    protected function getDumpedEmptyIconicContainerBuilder()
    {
        return <<<XYZ
<?php

/**
 * TubePressServiceContainer
 *
 * This class has been auto-generated
 * by the Symfony Dependency Injection Component.
 */
class TubePressServiceContainer extends ehough_iconic_Container
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
}

XYZ;

    }
}