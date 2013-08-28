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
abstract class tubepress_test_impl_ioc_AbstractIocContainerExtensionTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockDefinition;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainer;

    /**
     * @var tubepress_api_ioc_ContainerExtensionInterface
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = $this->buildSut();

        $this->_mockContainer = ehough_mockery_Mockery::mock('tubepress_api_ioc_ContainerInterface');
    }

    public function testLoad()
    {
        $this->prepareForLoad();

        $this->_sut->load($this->_mockContainer);

        $this->assertTrue(true);
    }

    protected function expectRegistration($id, $class)
    {
        $this->_startChain($class);

        $this->_mockContainer->shouldReceive('register')->once()->with($id, $class)->andReturn($this->_mockDefinition);

        return $this;
    }

    protected function expectDefinition($id, tubepress_api_ioc_DefinitionInterface $definition)
    {
        $this->_startChain($definition->getClass());

        $this->_mockContainer->shouldReceive('setDefinition')->once()->with($id, ehough_mockery_Mockery::on(function ($actualDefinition) use ($definition) {

            return $actualDefinition instanceof tubepress_api_ioc_DefinitionInterface
                && $actualDefinition->getClass() === $definition->getClass();

        }))->andReturn($this->_mockDefinition);

        return $this;
    }

    protected function withArgument($arg)
    {
        $this->_mockDefinition->shouldReceive('addArgument')->once()->with(ehough_mockery_Mockery::on(function ($actual) use ($arg) {

            return "$actual" === "$arg";

        }))->andReturn($this->_mockDefinition);

        return $this;
    }


    protected function withFactoryService($service)
    {
        $this->_mockDefinition->shouldReceive('setFactoryService')->once()->with(ehough_mockery_Mockery::on(function ($actual) use ($service) {

            return $actual === $service;

        }))->andReturn($this->_mockDefinition);

        return $this;
    }

    protected function withFactoryClass($class)
    {
        $this->_mockDefinition->shouldReceive('setFactoryClass')->once()->with(ehough_mockery_Mockery::on(function ($actual) use ($class) {

            return $actual === $class;

        }))->andReturn($this->_mockDefinition);

        return $this;
    }

    protected function withFactoryMethod($method)
    {
        $this->_mockDefinition->shouldReceive('setFactoryMethod')->once()->with(ehough_mockery_Mockery::on(function ($actual) use ($method) {

            return $actual === $method;

        }))->andReturn($this->_mockDefinition);

        return $this;
    }

    protected function withTag($tag, $tagAttributes = array())
    {
        if (empty($tagAttributes)) {

            $this->_mockDefinition->shouldReceive('addTag')->once()->with($tag)->andReturn($this->_mockDefinition);

        } else {

            $this->_mockDefinition->shouldReceive('addTag')->once()->with($tag, $tagAttributes)->andReturn($this->_mockDefinition);
        }

        return $this;
    }

    protected function andReturnDefinition()
    {
        return $this->_mockDefinition;
    }

    /**
     * @return tubepress_api_ioc_ContainerExtensionInterface
     */
    protected abstract function buildSut();

    protected abstract function prepareForLoad();

    private function _startChain($class)
    {
        $this->_mockDefinition = ehough_mockery_Mockery::mock('tubepress_api_ioc_DefinitionInterface');

        $this->_mockDefinition->shouldReceive('getClass')->andReturn($class);
    }
}