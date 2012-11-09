<?php
/**
 * Copyright 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of epilog (https://github.com/ehough/epilog)
 *
 * epilog is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * epilog is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

abstract class TubePressUnitTest extends PHPUnit_Framework_TestCase
{
    private $_mocks;

    private $_mockIocContainer;

    /**
     * Sets up a mock IoC container that spits out mock services on demand. This reduces boilerplate
     * code in our unit tests.
     */
    public final function setUp()
    {
        $this->_mockIocContainer = Mockery::mock('ehough_iconic_api_IContainer');

        $this->_mockIocContainer->shouldReceive('get')->andReturnUsing(array($this, '_getMockServiceById'));
        $this->_mockIocContainer->shouldReceive('findTaggedServiceIds')->andReturnUsing(array($this, '_getMockServiceIdsByTag'));

        /** @noinspection PhpParamsInspection */
        tubepress_impl_patterns_sl_ServiceLocator::setIocContainer($this->_mockIocContainer);

        $this->onSetup();
    }

    public final function tearDown()
    {
        $this->onTearDown();

        Mockery::close();
    }

    public static function setUpBeforeClass()
    {
        if (! defined('TUBEPRESS_ROOT')) {

            define('TUBEPRESS_ROOT', realpath(__DIR__ . '/../../../'));
        }
    }

    protected function onSetup()
    {
        //override point
    }

    protected function onTearDown()
    {
        //override point
    }

    protected final function createMockSingletonService($type)
    {
        $mockDescriptor           = new stdClass();
        $mockDescriptor->id       = $type;
        $mockDescriptor->instance = Mockery::mock($type);

        $this->_mocks[] = $mockDescriptor;

        return $mockDescriptor->instance;
    }

    protected final function createMockPluggableService($type)
    {
        $mockDescriptor           = new stdClass();
        $mockDescriptor->id       = mt_rand();
        $mockDescriptor->tag      = $type;
        $mockDescriptor->instance = Mockery::mock($type);

        $this->_mocks[] = $mockDescriptor;

        return $mockDescriptor->instance;
    }

    protected final function getMockIocContainer()
    {
        return $this->_mockIocContainer;
    }

    public final function _getMockServiceById($id)
    {
        foreach ($this->_mocks as $mock) {

            if ($mock->id === $id) {

                return $mock->instance;
            }
        }

        throw new RuntimeException("Failed to find singleton service with ID $id. Did you forget to call createMockSingletonService()?");
    }

    public final function _getMockServiceIdsByTag($tag)
    {
        $toReturn = array();

        foreach ($this->_mocks as $mock) {

            if (isset($mock->tag) && $mock->tag === $tag) {

                $toReturn[$mock->id] = array();
            }
        };

        return $toReturn;
    }
}