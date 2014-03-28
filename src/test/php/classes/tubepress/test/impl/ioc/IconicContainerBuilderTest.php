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

    public function testConvertToIconicDefinitionWrongClass()
    {
        $this->setExpectedException('InvalidArgumentException', 'Can only add tubepress_api_ioc_DefinitionInterface instances to the container. You supplied an instance of stdClass');

        $def = new stdClass();

        $this->_sut->_callbackConvertToIconicDefinition($def);
    }

    public function testConvertToIconicDefinitionAlreadyInstance()
    {
        $def = ehough_mockery_Mockery::mock('tubepress_api_ioc_DefinitionInterface');
        $def->shouldReceive('getClass')->twice()->andReturn('some clazz');
        $def->shouldReceive('setClass')->once()->with('some clazz');
        $def->shouldReceive('getArguments')->twice()->andReturn(array(1, 3, 2));
        $def->shouldReceive('setArguments')->once()->with(array(1, 3, 2));

        $d = new tubepress_impl_ioc_IconicDefinitionWrapper($def);

        $result = $this->_sut->_callbackConvertToIconicDefinition($d);

        $this->assertSame($d, $result);
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
        $def = ehough_mockery_Mockery::mock('tubepress_api_ioc_DefinitionInterface');
        $def->shouldReceive('getClass')->twice()->andReturn('some clazz');
        $def->shouldReceive('setClass')->once()->with('some clazz');
        $def->shouldReceive('getArguments')->twice()->andReturn(array(1, 3, 2));
        $def->shouldReceive('setArguments')->once()->with(array(1, 3, 2));

        $this->assertFalse($this->_sut->has('x'));

        $this->_sut->setDefinition('x', $def);

        $this->assertTrue($this->_sut->has('x'));

        $this->_sut->removeDefinition('x');

        $this->assertFalse($this->_sut->has('x'));
    }

    public function testSetServiceFrozen()
    {
        $this->setExpectedException('BadMethodCallException', 'Cannot set a service on a frozen container');

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
        $this->setExpectedException('BadMethodCallException', 'Setting a definition on a frozen container is not allowed');

        $def = ehough_mockery_Mockery::mock('tubepress_api_ioc_DefinitionInterface');

        $this->_sut->compile();

        $this->_sut->setDefinition('x', $def);
    }

    public function testSetDefinition()
    {
        $def = ehough_mockery_Mockery::mock('tubepress_api_ioc_DefinitionInterface');
        $def->shouldReceive('getClass')->twice()->andReturn('some clazz');
        $def->shouldReceive('setClass')->once()->with('some clazz');
        $def->shouldReceive('getArguments')->twice()->andReturn(array(1, 3, 2));
        $def->shouldReceive('setArguments')->once()->with(array(1, 3, 2));

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
        $this->setExpectedException('BadMethodCallException', 'Cannot set definitions on a frozen container');

        $this->_sut->compile();

        $this->_sut->setDefinitions(array());
    }

    public function testSetDefinitions()
    {
        $def = ehough_mockery_Mockery::mock('tubepress_api_ioc_DefinitionInterface');
        $def->shouldReceive('getClass')->twice()->andReturn('some clazz');
        $def->shouldReceive('setClass')->once()->with('some clazz');
        $def->shouldReceive('getArguments')->twice()->andReturn(array(1, 3, 2));
        $def->shouldReceive('setArguments')->once()->with(array(1, 3, 2));

        $this->assertEmpty($this->_sut->getDefinitions());

        $this->_sut->setDefinitions(array($def));

        $this->assertEquals(array($def), $this->_sut->getDefinitions());
    }

    public function testSetParameterFrozen()
    {
        $this->setExpectedException('LogicException', 'Cannot set a parameter on a frozen container');

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

    public function testMergeFrozen()
    {
        $this->setExpectedException('BadMethodCallException', 'Cannot merge on a frozen container.');

        $this->_sut->compile();

        $mockDef = ehough_mockery_Mockery::mock('tubepress_api_ioc_DefinitionInterface');
        $mockDefs = array($mockDef);

        $mockContainer = ehough_mockery_Mockery::mock('tubepress_api_ioc_ContainerBuilderInterface');
        $mockContainer->shouldReceive('getDefinitions')->once()->andReturn($mockDefs);

        $mockExtension = ehough_mockery_Mockery::mock('tubepress_api_ioc_ContainerExtensionInterface');
        $mockExtension->shouldReceive('load')->once()->with(ehough_mockery_Mockery::any('tubepress_impl_ioc_IconicContainerBuilder'));
        $this->_sut->registerExtension($mockExtension);

        $this->_sut->process($mockContainer);
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
        $this->setExpectedException('BadMethodCallException', 'Cannot set definitions on a frozen container');

        $this->_sut->compile();
        $this->_sut->addDefinitions(array(new tubepress_impl_ioc_Definition('clazz')));
    }

    public function testCompile()
    {
        $mockCompilerPass = ehough_mockery_Mockery::mock('tubepress_api_ioc_CompilerPassInterface');

        $mockCompilerPass->shouldReceive('process')->once()->with($this->_sut);

        $this->_sut->addCompilerPass($mockCompilerPass);

        $this->assertFalse($this->_sut->isFrozen());

        $this->_sut->compile();

        $this->assertTrue($this->_sut->isFrozen());
    }
}