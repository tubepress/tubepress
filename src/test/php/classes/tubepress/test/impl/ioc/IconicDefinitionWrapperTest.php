<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_impl_ioc_IconicDefinitionWrapper
 */
class tubepress_test_impl_ioc_IconicDefinitionWrapperTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_ioc_IconicDefinitionWrapper
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockDefinition;

    public function onSetup()
    {
        $this->_mockDefinition = ehough_mockery_Mockery::mock('tubepress_api_ioc_DefinitionInterface');
        $this->_mockDefinition->shouldReceive('getClass')->andReturn('some class');
        $this->_mockDefinition->shouldReceive('setClass')->once()->with('some class');
        $this->_mockDefinition->shouldReceive('getArguments')->andReturn(array('a', 'b', '3'));
        $this->_mockDefinition->shouldReceive('setArguments')->once()->with(array('a', 'b', '3'));

        $this->_sut = new tubepress_impl_ioc_IconicDefinitionWrapper($this->_mockDefinition);
    }

    public function testClearTags()
    {
        $this->_testSetter('clearTags');
    }

    public function testClearTag()
    {
        $this->_testSetter('clearTag', 'tag name');
    }

    public function testAddTag()
    {
        $this->_testSetterTwoArgs('addTag', 'tag name', array('attributes'));
    }

    public function testHasTag()
    {
        $this->_testGetter('hasTag', true, 'some tag');
    }

    public function testHasMethodCall()
    {
        $this->_testGetter('hasMethodCall', false, 'some method');
    }

    public function testRemoveMethodCall()
    {
        $this->_testSetter('removeMethodCall', 'some call');
    }

    public function testGetArgumentOutOfBounds()
    {
        $this->setExpectedException('ehough_iconic_exception_OutOfBoundsException', 'hi');

        $this->_mockDefinition->shouldReceive('getArgument')->once()->with(9)->andThrow(new OutOfBoundsException('hiya'));

        $this->_sut->getArgument(9);
    }

    public function testGetArgument()
    {
        $this->_testGetter('getArgument', 'some arg', 1);
    }

    public function testReplaceArgumentOutOfBounds()
    {
        $this->setExpectedException('ehough_iconic_exception_OutOfBoundsException', 'hi');

        $this->_mockDefinition->shouldReceive('replaceArgument')->once()->with(9, 'something')->andThrow(new OutOfBoundsException('hi'));

        $this->_sut->replaceArgument(9, 'something');
    }

    public function testReplaceArgumentInBounds()
    {
        $this->_testSetterTwoArgs('replaceArgument', 2, 'something');
    }

    public function testAddArgument()
    {
        $this->_testSetter('addArgument', 'some argument');
    }

    public function testGetTubePressDefinition()
    {
        $this->assertSame($this->_mockDefinition, $this->_sut->getTubePressDefinition());
    }

    public function testGetConfigurator()
    {
        $this->_testGetter('getConfigurator', 'some config');
    }

    public function testGetFile()
    {
        $this->_testGetter('getFile', 'some file');
    }

    public function testGetTags()
    {
        $this->_testGetter('getTags', array('tag'));
    }

    public function testGetTag()
    {
        $this->_testGetter('getTag', 'some tag', 'expected tag');
    }

    public function testGetMethodCalls()
    {
        $this->_testGetter('getMethodCalls', array('foo', 33));
    }

    public function testGetArguments()
    {
        $this->assertEquals(array('a', 'b', '3'), $this->_sut->getArguments());
    }

    public function testGetProperties()
    {
        $this->_testGetter('getProperties', array('some', 'thing', 4));
    }

    public function testGetClass()
    {
        $this->assertEquals('some class', $this->_sut->getClass());
    }

    public function testGetFactoryService()
    {
        $this->_testGetter('getFactoryService', 'some service');
    }

    public function testGetFactoryClass()
    {
        $this->_testGetter('getFactoryClass', 'foobarrrr');
    }

    public function testGetFactoryMethod()
    {
        $this->_testGetter('getFactoryMethod', 'some method');
    }

    public function testSetTags()
    {
        $this->_testSetter('setTags', array('one', 'two'));
    }

    public function testSetFile()
    {
        $this->_testSetter('setFile', 'a file');
    }

    public function testSetConfigurator()
    {
        $this->_testSetter('setConfigurator', 'another config');
    }

    public function testAddMethodCallInvalid()
    {
        $this->setExpectedException('ehough_iconic_exception_InvalidArgumentException', 'foobar');

        $this->_mockDefinition->shouldReceive('addMethodCall')->once()->with('some call', array('x'))->andThrow(new InvalidArgumentException('foobar'));

        $this->_sut->addMethodCall('some call', array('x'));
    }

    public function testSetMethodCalls()
    {
        $this->_mockDefinition->shouldReceive('addMethodCall')->once()->with('some calls', array('x'));

        $this->_testSetter('setMethodCalls', array(array('some calls', array('x'))));
    }

    public function testSetClass()
    {
        $this->_testSetter('setClass', 'another clazz');
    }

    public function testSetProperty()
    {
        $this->_testSetterTwoArgs('setProperty', 'foobar', array(5, 4, '3'));
    }

    public function testSetProperties()
    {
        $this->_testSetter('setProperties', array('foo' => 'bar'));
    }

    public function testSetFactoryService()
    {
        $this->_testSetter('setFactoryService', 'another service');
    }

    public function testSetFactoryMethod()
    {
        $this->_testSetter('setFactoryMethod', 'another method');
    }

    public function testSetArguments()
    {
        $this->_testSetter('setArguments', array('foo', 'x'));
    }

    public function testSetFactoryClass()
    {
        $this->_testSetter('setFactoryClass', 'another class');
    }

    private function _testGetter($method, $expected, $arg = null)
    {

        if ($arg) {

            $this->_mockDefinition->shouldReceive($method)->once()->with($arg)->andReturn($expected);
            $this->assertEquals($expected, $this->_sut->$method($arg));

        } else {

            $this->_mockDefinition->shouldReceive($method)->once()->andReturn($expected);
            $this->assertEquals($expected, $this->_sut->$method());
        }
    }

    private function _testSetter($method, $args = null)
    {
        if ($args) {

            $this->_mockDefinition->shouldReceive($method)->once()->with($args);
            $result = $this->_sut->$method($args);

        } else {

            $this->_mockDefinition->shouldReceive($method)->once();
            $result = $this->_sut->$method();
        }

        $this->assertTrue($result instanceof ehough_iconic_Definition);
    }

    private function _testSetterTwoArgs($method, $first, $second)
    {
        $this->_mockDefinition->shouldReceive($method)->once()->with($first, $second);

        $result = $this->_sut->$method($first, $second);

        $this->assertTrue($result instanceof ehough_iconic_Definition);
    }
}
