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
class tubepress_test_impl_options_DefaultOptionDescriptorReferenceTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_options_DefaultOptionDescriptorReference
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockProvider1;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockProvider2;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_options_DefaultOptionDescriptorReference();

        $this->_mockEventDispatcher = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockProvider1       = ehough_mockery_Mockery::mock(tubepress_spi_options_PluggableOptionDescriptorProvider::_);
        $this->_mockProvider2       = ehough_mockery_Mockery::mock(tubepress_spi_options_PluggableOptionDescriptorProvider::_);
        $this->_bootConfigService   = $this->createMockSingletonService(tubepress_spi_boot_SettingsFileReaderInterface::_);

        $this->_sut->setPluggableOptionDescriptorProviders(array($this->_mockProvider1, $this->_mockProvider2));
    }

    public function testRegisterDuplicate()
    {
        $od = new tubepress_spi_options_OptionDescriptor('name');
        $od->setDefaultValue('xyz');

        $this->_mockProvider1->shouldReceive('getOptionDescriptors')->once()->andReturn(array($od));
        $this->_mockProvider2->shouldReceive('getOptionDescriptors')->once()->andReturn(array($od));

        $this->_setupEventDispatcher($od);

        $this->assertSame($od, $this->_sut->findOneByName('name'));
    }

    public function testGetAll()
    {
        $od = new tubepress_spi_options_OptionDescriptor('name');
        $od->setDefaultValue('xyz');

        $this->_mockProvider1->shouldReceive('getOptionDescriptors')->once()->andReturn(array());
        $this->_mockProvider2->shouldReceive('getOptionDescriptors')->once()->andReturn(array($od));

        $this->_setupEventDispatcher($od);

        $result = $this->_sut->findAll();

        $this->assertTrue(is_array($result));
        $this->assertSame($od, $result[0]);
    }

    public function testFindOne()
    {
        $od = new tubepress_spi_options_OptionDescriptor('name');
        $od->setDefaultValue('xyz');

        $this->_mockProvider1->shouldReceive('getOptionDescriptors')->once()->andReturn(array());
        $this->_mockProvider2->shouldReceive('getOptionDescriptors')->once()->andReturn(array($od));

        $this->_setupEventDispatcher($od);

        $result = $this->_sut->findOneByName('name');

        $this->assertSame($od, $result);
    }

    private function _setupEventDispatcher(tubepress_spi_options_OptionDescriptor $od)
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTIONS_DESCRIPTOR_REGISTRATION,
        ehough_mockery_Mockery::on(function ($event) use ($od) {

            return $event instanceof tubepress_api_event_EventInterface && $event->getSubject()->getName() === $od->getName();
        }));
    }
}

