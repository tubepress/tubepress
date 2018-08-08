<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_ioc_compiler_TaggedServicesConsumerPass<extended>
 */
class tubepress_test_ioc_compiler_TaggedServicesConsumerPassTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_ioc_compiler_TaggedServicesConsumerPass
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContainer;

    public function onSetup()
    {
        $this->_sut           = new tubepress_ioc_compiler_TaggedServicesConsumerPass();
        $this->_mockContainer = $this->mock('tubepress_api_ioc_ContainerBuilderInterface');
    }

    public function testProcessMultiple()
    {
        $mockDefinition = $this->mock('tubepress_api_ioc_DefinitionInterface');

        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER)
            ->andReturn(array('id' => array(array('tag' => 'some-tag', 'method' => 'someMethod'))));
        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with('some-tag')
            ->andReturn(array('some-other-id' => array(array('a' => 'b'))));

        $this->_mockContainer->shouldReceive('getDefinition')->once()->with('id')->andReturn($mockDefinition);

        $validator = function ($refs) {

            $ok = is_array($refs) && is_array($refs[0]);

            if (!$ok) {

                return false;
            }

            $first = $refs[0][0];

            return "$first" === 'some-other-id';
        };

        $mockDefinition->shouldReceive('addMethodCall')->once()->with('someMethod', Mockery::on($validator));

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }

    public function testProcessNoTagSet()
    {
        $this->setExpectedException('LogicException', 'Service id must specify tag in its tag data');

        $mockDefinition = $this->mock('tubepress_api_ioc_DefinitionInterface');

        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER)
            ->andReturn(array('id' => array(array())));

        $this->_mockContainer->shouldReceive('getDefinition')->once()->with('id')->andReturn($mockDefinition);

        $this->_sut->process($this->_mockContainer);
    }
}
