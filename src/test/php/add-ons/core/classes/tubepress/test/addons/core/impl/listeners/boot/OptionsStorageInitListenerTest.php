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
 * @covers tubepress_addons_core_impl_listeners_boot_OptionsStorageInitListener
 */
class tubepress_test_addons_core_impl_listeners_boot_OptionsStorageInitListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_boot_OptionsStorageInitListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionDescriptorReference;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStorageManager;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_core_impl_listeners_boot_OptionsStorageInitListener();

        $this->_mockOptionDescriptorReference = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockStorageManager       = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
    }

    public function testBoot()
    {
        $mockOd1 = new tubepress_spi_options_OptionDescriptor('name');
        $mockOd1->setDefaultValue('value');
        $mockOd2 = new tubepress_spi_options_OptionDescriptor('other');
        $mockOd2->setDefaultValue('value2');

        $mockOds = array($mockOd1, $mockOd2);

        $this->_mockOptionDescriptorReference->shouldReceive('findAll')->once()->andReturn($mockOds);
        $this->_mockStorageManager->shouldReceive('createEachIfNotExists')->once()->with(array(

            'name' => 'value',
            'other' => 'value2'
        ));

        $this->_sut->onBoot(ehough_mockery_Mockery::mock('tubepress_api_event_EventInterface'));

        $this->assertTrue(true);
    }
}