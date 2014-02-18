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

abstract class tubepress_test_impl_boot_secondary_AbstractSecondaryBootstrapperTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_spi_boot_secondary_SecondaryBootstrapperInterface
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockSettingsFileReader;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockClassLoader;

    /**
     * @var string
     */
    private $_mockContainerPath;

    public function onSetup()
    {
        $this->_mockSettingsFileReader = ehough_mockery_Mockery::mock(tubepress_spi_boot_SettingsFileReaderInterface::_);
        $this->_mockClassLoader        = ehough_mockery_Mockery::mock('ehough_pulsar_ComposerClassLoader');
        $this->_sut                    = $this->buildSut();
        $this->_mockContainerPath      = tempnam(sys_get_temp_dir(), 'mockContainer');
    }

    public function onTearDown()
    {
        @unlink($this->_mockContainerPath);
    }

    /**
     * @return tubepress_spi_boot_secondary_SecondaryBootstrapperInterface
     */
    protected abstract function buildSut();

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockClassLoader()
    {
        return $this->_mockClassLoader;
    }

    /**
     * @return string
     */
    protected function getMockContainerPath()
    {
        return $this->_mockContainerPath;
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected function getMockSettingsFileReader()
    {
        return $this->_mockSettingsFileReader;
    }

    protected function getContainer()
    {
        return $this->_sut->getServiceContainer($this->_mockSettingsFileReader, $this->_mockClassLoader);
    }


}