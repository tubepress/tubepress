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
 * @covers tubepress_core_impl_ioc_TaggedTypeConsumerPass<extended>
 */
class tubepress_test_core_impl_ioc_compiler_TaggedTypeConsumerPassTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_ioc_TaggedTypeConsumerPass
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainer;

    public function onSetup()
    {
        $this->_sut           = new tubepress_core_impl_ioc_TaggedTypeConsumerPass();
        $this->_mockContainer = $this->mock('tubepress_api_ioc_ContainerBuilderInterface');
    }

    public function testProcessRequired2()
    {
        $mockDefinition = $this->mock('tubepress_api_ioc_DefinitionInterface');

        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_core_api_const_ioc_Tags::TAGGED_SERVICE_CONSUMER)
            ->andReturn(array('id' => array(array('tag' => 'some-tag', 'method' => 'someMethod', 'type' => 'some-type', 'required' => false))));
        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with('some-tag')
            ->andReturn(array());

        $this->_mockContainer->shouldReceive('getDefinition')->once()->with('id')->andReturn($mockDefinition);

        $mockDefinition->shouldReceive('addMethodCall')->once()->with('someMethod', array(null));

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }

    public function testProcessRequired()
    {
        $this->setExpectedException('LogicException', 'No services match tag some-tag and type some-type');

        $mockDefinition = $this->mock('tubepress_api_ioc_DefinitionInterface');

        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_core_api_const_ioc_Tags::TAGGED_SERVICE_CONSUMER)
            ->andReturn(array('id' => array(array('tag' => 'some-tag', 'method' => 'someMethod', 'type' => 'some-type', 'required' => true))));
        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with('some-tag')
            ->andReturn(array());

        $this->_mockContainer->shouldReceive('getDefinition')->once()->with('id')->andReturn($mockDefinition);

        $this->_sut->process($this->_mockContainer);
    }

    public function testProcess()
    {
        $mockDefinition = $this->mock('tubepress_api_ioc_DefinitionInterface');

        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_core_api_const_ioc_Tags::TAGGED_SERVICE_CONSUMER)
            ->andReturn(array('id' => array(array('tag' => 'some-tag', 'method' => 'someMethod', 'type' => 'some-type'))));
        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with('some-tag')
            ->andReturn(array('some-other-id' => array(array('a' => 'b'))));

        $this->_mockContainer->shouldReceive('getDefinition')->once()->with('id')->andReturn($mockDefinition);

        $mockDefinition->shouldReceive('addMethodCall')->once()->with('someMethod', array('some-other-id'));

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }
}