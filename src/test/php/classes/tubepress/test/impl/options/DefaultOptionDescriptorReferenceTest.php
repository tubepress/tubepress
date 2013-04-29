<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
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
    private $_mockStorageManager;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_options_DefaultOptionDescriptorReference();

        $this->_mockStorageManager  = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
        $this->_mockEventDispatcher = $this->createMockSingletonService('ehough_tickertape_EventDispatcherInterface');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRegisterDuplicate()
    {
        $od = new tubepress_spi_options_OptionDescriptor('name');
        $od->setDefaultValue('xyz');

        $this->_mockStorageManager->shouldReceive('createIfNotExists')->once()->with('name', 'xyz');

        $this->_setupEventDispatcher($od);

        $this->_sut->registerOptionDescriptor($od);
        $this->_sut->registerOptionDescriptor($od);
    }

    public function testGetAll()
    {
        $od = new tubepress_spi_options_OptionDescriptor('name');

        $od->setDefaultValue('xyz');

        $this->_mockStorageManager->shouldReceive('createIfNotExists')->once()->with('name', 'xyz');

        $this->_setupEventDispatcher($od);

        $this->_sut->registerOptionDescriptor($od);

        $result = $this->_sut->findAll();

        $this->assertTrue(is_array($result));
        $this->assertSame($od, $result[0]);
    }

    public function testFindOne()
    {
        $result = $this->_sut->findOneByName('x');

        $this->assertNull($result);

        $od = new tubepress_spi_options_OptionDescriptor('name');
        $od->setDefaultValue('xyz');

        $this->_mockStorageManager->shouldReceive('createIfNotExists')->once()->with('name', 'xyz');

        $this->_setupEventDispatcher($od);

        $this->_sut->registerOptionDescriptor($od);

        $result = $this->_sut->findOneByName('name');

        $this->assertSame($od, $result);
    }

    private function _setupEventDispatcher(tubepress_spi_options_OptionDescriptor $od)
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::OPTIONS_DESCRIPTOR_REGISTRATION,
        ehough_mockery_Mockery::on(function ($event) use ($od) {

            return $event instanceof tubepress_api_event_TubePressEvent && $event->getSubject()->getName() === $od->getName();
        }));
    }
}

