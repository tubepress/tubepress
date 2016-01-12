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
 * @covers tubepress_internal_ioc_ContainerBuilder
 */
class tubepress_test_internal_ioc_ContainerBuilderTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_internal_ioc_ContainerBuilder
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_internal_ioc_ContainerBuilder();
    }

    public function testGetParameterNoSuchParam()
    {
        $this->setExpectedException('InvalidArgumentException', 'You have requested a non-existent parameter "foo".');

        $this->_sut->getParameter('foo');
    }

    public function testGetNoSuchDef()
    {
        $this->assertNull($this->_sut->getDefinition('x'));
    }

    public function testGetNotExist()
    {
        $this->assertNull($this->_sut->get('x'));
    }

    public function testTags()
    {
        $this->assertEmpty($this->_sut->findTags());
        $this->assertEmpty($this->_sut->findTaggedServiceIds('bar'));

        $this->_sut->register('x', 'foo')->addTag('bar', array('some', 'attributes'));

        $this->assertEquals(array('bar'), $this->_sut->findTags());
        $this->assertEquals(array('x' => array(array('some', 'attributes'))), $this->_sut->findTaggedServiceIds('bar'));
    }

    public function testRegister()
    {
        $this->assertFalse($this->_sut->has('ABC'));

        $this->_sut->register('ABC', 'some clazz');

        $this->assertTrue($this->_sut->has('ABC'));

        $result = $this->_sut->getDefinition('ABC');

        $this->assertEquals($result->getClass(), 'some clazz');
    }

    public function testRemoveDefinition()
    {
        $tubePressDefinition = $this->mock('tubepress_internal_ioc_Definition');
        $iconicDefinition = $this->mock('ehough_iconic_Definition');
        $tubePressDefinition->shouldReceive('getUnderlyingIconicDefinition')->once()->andReturn($iconicDefinition);

        $this->assertFalse($this->_sut->has('x'));

        $this->_sut->setDefinition('x', $tubePressDefinition);

        $this->assertTrue($this->_sut->has('x'));

        $this->_sut->removeDefinition('x');

        $this->assertFalse($this->_sut->has('x'));
    }

    public function testSetServiceFrozen()
    {
        $this->setExpectedException('BadMethodCallException', 'Setting service "x" on a frozen container is not allowed.');

        $this->_sut->compile();

        $service = new stdClass();

        $this->_sut->set('x', $service);
    }

    public function testSetService()
    {
        $service = new stdClass();

        $this->_sut->set('x', $service);

        $this->assertSame($service, $this->_sut->get('x'));
    }

    public function testSetDefinitionFrozen()
    {
        $this->setExpectedException('BadMethodCallException', 'Adding definition to a frozen container is not allowed');

        $tubePressDefinition = $this->mock('tubepress_internal_ioc_Definition');
        $iconicDefinition = $this->mock('ehough_iconic_Definition');
        $tubePressDefinition->shouldReceive('getUnderlyingIconicDefinition')->once()->andReturn($iconicDefinition);
        $this->_sut->compile();

        $this->_sut->setDefinition('x', $tubePressDefinition);
    }

    public function testSetDefinition()
    {
        $tubePressDefinition = $this->mock('tubepress_internal_ioc_Definition');
        $iconicDefinition = $this->mock('ehough_iconic_Definition');
        $tubePressDefinition->shouldReceive('getUnderlyingIconicDefinition')->once()->andReturn($iconicDefinition);

        $this->assertFalse($this->_sut->has('x'));
        $this->assertFalse($this->_sut->hasDefinition('x'));

        $this->_sut->setDefinition('x', $tubePressDefinition);

        $this->assertTrue($this->_sut->has('x'));
        $this->assertTrue($this->_sut->hasDefinition('x'));

        $result = $this->_sut->getDefinition('x');

        $this->assertInstanceOf('tubepress_api_ioc_DefinitionInterface', $result);
    }

    public function testSetDefinitionsFrozen()
    {
        $this->setExpectedException('BadMethodCallException', 'Adding definition to a frozen container is not allowed');

        $this->_sut->compile();

        $tubePressDefinition = $this->mock('tubepress_internal_ioc_Definition');
        $iconicDefinition = $this->mock('ehough_iconic_Definition');
        $tubePressDefinition->shouldReceive('getUnderlyingIconicDefinition')->once()->andReturn($iconicDefinition);

        $this->_sut->setDefinitions(array($tubePressDefinition));
    }

    public function testSetDefinitions()
    {
        $tubePressDefinition = $this->mock('tubepress_internal_ioc_Definition');
        $iconicDefinition = $this->mock('ehough_iconic_Definition');
        $tubePressDefinition->shouldReceive('getUnderlyingIconicDefinition')->once()->andReturn($iconicDefinition);
        $this->assertEmpty($this->_sut->getDefinitions());

        $this->_sut->setDefinitions(array($tubePressDefinition));

        $result = $this->_sut->getDefinitions();
        $this->assertCount(1, $result);
    }

    public function testSetParameterFrozen()
    {
        $this->setExpectedException('LogicException', 'Impossible to call set() on a frozen ehough_iconic_parameterbag_ParameterBag.');

        $this->_sut->compile();

        $this->_sut->setParameter('foo', 'bar');

        $this->assertTrue(true);
    }

    public function testSetParameter()
    {
        $this->assertFalse($this->_sut->hasParameter('foo'));

        $this->_sut->setParameter('foo', 'bar');

        $this->assertTrue($this->_sut->hasParameter('foo'));

        $this->assertEquals('bar', $this->_sut->getParameter('foo'));
    }

    public function testMerge()
    {
        $mockDef = $this->mock('tubepress_api_ioc_DefinitionInterface');
        $mockDefs = array($mockDef);

        $mockContainer = $this->mock('tubepress_api_ioc_ContainerBuilderInterface');
        $mockContainer->shouldReceive('getDefinitions')->once()->andReturn($mockDefs);
        $mockContainer->shouldReceive('addDefinitions')->once()->with($mockDefs);

        $mockExtension = $this->mock('tubepress_spi_ioc_ContainerExtensionInterface');
        $mockExtension->shouldReceive('load')->once()->with(Mockery::any('tubepress_internal_ioc_ContainerBuilder'));

        $this->_sut->registerExtension($mockExtension);

        $this->_sut->process($mockContainer);

        $this->assertTrue(true);
    }

    public function testAddDefinitionsWhenFrozen()
    {
        $this->setExpectedException('BadMethodCallException', 'Adding definition to a frozen container is not allowed');

        $this->_sut->compile();
        $this->_sut->addDefinitions(array(new tubepress_internal_ioc_Definition('clazz')));
    }
}