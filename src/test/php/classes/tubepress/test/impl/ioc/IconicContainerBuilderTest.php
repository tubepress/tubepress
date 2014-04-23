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
 * @covers tubepress_impl_ioc_IconicContainerBuilder
 */
class tubepress_test_impl_ioc_IconicContainerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_ioc_IconicContainerBuilder
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_ioc_IconicContainerBuilder();
    }

    public function testGetParameterNoSuchParam()
    {
        $this->setExpectedException('InvalidArgumentException', 'Parameter foo not found');

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
        $def = ehough_mockery_Mockery::mock('tubepress_impl_ioc_Definition');

        $this->assertFalse($this->_sut->has('x'));

        $this->_sut->setDefinition('x', $def);

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

        $def = ehough_mockery_Mockery::mock('tubepress_impl_ioc_Definition');

        $this->_sut->compile();

        $this->_sut->setDefinition('x', $def);
    }

    public function testSetDefinition()
    {
        $def = ehough_mockery_Mockery::mock('tubepress_impl_ioc_Definition');

        $this->assertFalse($this->_sut->has('x'));
        $this->assertFalse($this->_sut->hasDefinition('x'));

        $this->_sut->setDefinition('x', $def);

        $this->assertTrue($this->_sut->has('x'));
        $this->assertTrue($this->_sut->hasDefinition('x'));

        $result = $this->_sut->getDefinition('x');

        $this->assertSame($def, $result);
    }

    public function testSetDefinitionsFrozen()
    {
        $this->setExpectedException('BadMethodCallException', 'Adding definition to a frozen container is not allowed');

        $this->_sut->compile();

        $def = ehough_mockery_Mockery::mock('tubepress_impl_ioc_Definition');

        $this->_sut->setDefinitions(array($def));
    }

    public function testSetDefinitions()
    {
        $def = ehough_mockery_Mockery::mock('tubepress_impl_ioc_Definition');

        $this->assertEmpty($this->_sut->getDefinitions());

        $this->_sut->setDefinitions(array($def));

        $this->assertEquals(array($def), $this->_sut->getDefinitions());
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
        $mockDef = ehough_mockery_Mockery::mock('tubepress_api_ioc_DefinitionInterface');
        $mockDefs = array($mockDef);

        $mockContainer = ehough_mockery_Mockery::mock('tubepress_api_ioc_ContainerBuilderInterface');
        $mockContainer->shouldReceive('getDefinitions')->once()->andReturn($mockDefs);
        $mockContainer->shouldReceive('addDefinitions')->once()->with($mockDefs);

        $mockExtension = ehough_mockery_Mockery::mock('tubepress_api_ioc_ContainerExtensionInterface');
        $mockExtension->shouldReceive('load')->once()->with(ehough_mockery_Mockery::any('tubepress_impl_ioc_IconicContainerBuilder'));

        $this->_sut->registerExtension($mockExtension);

        $this->_sut->process($mockContainer);

        $this->assertTrue(true);
    }

    public function testAddDefinitionsWhenFrozen()
    {
        $this->setExpectedException('BadMethodCallException', 'Adding definition to a frozen container is not allowed');

        $this->_sut->compile();
        $this->_sut->addDefinitions(array(new tubepress_impl_ioc_Definition('clazz')));
    }
}