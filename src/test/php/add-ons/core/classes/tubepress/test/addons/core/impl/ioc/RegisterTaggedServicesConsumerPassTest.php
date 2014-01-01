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
 * @covers tubepress_addons_core_impl_ioc_RegisterTaggedServicesConsumerPass<extended>
 */
class tubepress_test_addons_core_impl_ioc_RegisterTaggedServicesConsumerPassTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_ioc_RegisterTaggedServicesConsumerPass
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainer;

    public function onSetup()
    {
        $this->_sut           = new tubepress_addons_core_impl_ioc_RegisterTaggedServicesConsumerPass();
        $this->_mockContainer = ehough_mockery_Mockery::mock('tubepress_api_ioc_ContainerInterface');
    }

    public function testProcess()
    {
        $mockDefinition = ehough_mockery_Mockery::mock('tubepress_api_ioc_DefinitionInterface');

        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER)
            ->andReturn(array('id' => array(array('tag' => 'some-tag', 'method' => 'someMethod'))));
        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with('some-tag')
            ->andReturn(array('some-other-id' => array(array('a' => 'b'))));

        $this->_mockContainer->shouldReceive('getDefinition')->once()->with('id')->andReturn($mockDefinition);

        $mockDefinition->shouldReceive('addMethodCall')->once()->with('someMethod', ehough_mockery_Mockery::on(array($this, '__validateReferences')));

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }

    public function testProcessNoMethod()
    {
        $this->setExpectedException('InvalidArgumentException', 'Tagged set consumers must specify which method to run for each tagged service ID');

        $mockDefinition = ehough_mockery_Mockery::mock('tubepress_api_ioc_DefinitionInterface');

        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER)
            ->andReturn(array('id' => array(array('tag' => 'some-tag'))));
        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with('some-tag')
            ->andReturn(array('some-other-id'));

        $this->_mockContainer->shouldReceive('getDefinition')->once()->with('id')->andReturn($mockDefinition);

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }

    public function testProcessNoTaggedServices()
    {
        $mockDefinition = ehough_mockery_Mockery::mock('tubepress_api_ioc_DefinitionInterface');

        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER)
            ->andReturn(array('id' => array(array('tag' => 'some-tag'))));
        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with('some-tag')
            ->andReturn(array());

        $this->_mockContainer->shouldReceive('getDefinition')->once()->with('id')->andReturn($mockDefinition);

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }

    public function testProcessNoTagSet()
    {
        $this->setExpectedException('InvalidArgumentException', 'Tagged set consumers must specify which tagged services they would like to consume');

        $mockDefinition = ehough_mockery_Mockery::mock('tubepress_api_ioc_DefinitionInterface');

        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_api_ioc_ContainerExtensionInterface::TAG_TAGGED_SERVICES_CONSUMER)
            ->andReturn(array('id' => array(array())));

        $this->_mockContainer->shouldReceive('getDefinition')->once()->with('id')->andReturn($mockDefinition);

        $this->_sut->process($this->_mockContainer);
    }

    public function __validateReferences($refs)
    {
        $ok = is_array($refs) && is_array($refs[0]);

        if (!$ok) {

            return false;
        }

        $first = $refs[0][0];

        return "$first" === 'some-other-id';
    }
}