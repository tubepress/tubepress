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

abstract class tubepress_test_TubePressUnitTest extends PHPUnit_Framework_TestCase
{
    private $_mocks;

    private $_mockIocContainer;

    /**
     * Sets up a mock IoC container that spits out mock services on demand. This reduces boilerplate
     * code in our unit tests.
     */
    public final function setUp()
    {
        $this->_mockIocContainer = ehough_mockery_Mockery::mock('ehough_iconic_ContainerInterface');

        $this->_mockIocContainer->shouldReceive('get')->andReturnUsing(array($this, '_getMockServiceById'));
        $this->_mockIocContainer->shouldReceive('findTaggedServiceIds')->andReturnUsing(array($this, '_getMockServiceIdsByTag'));

        /** @noinspection PhpParamsInspection */
        tubepress_impl_patterns_sl_ServiceLocator::setIocContainer($this->_mockIocContainer);

        date_default_timezone_set('America/New_York');

        $this->onSetup();
    }

    public final function tearDown()
    {
        $this->onTearDown();

        ehough_mockery_Mockery::close();
    }

    public static function setUpBeforeClass()
    {
        if (! defined('TUBEPRESS_ROOT')) {

            define('TUBEPRESS_ROOT', realpath(__DIR__ . '/../../../../../../'));
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
        $mockDescriptor->instance = ehough_mockery_Mockery::mock($type);

        $this->_mocks[] = $mockDescriptor;

        return $mockDescriptor->instance;
    }

    protected final function createMockPluggableService($type)
    {
        $mockDescriptor           = new stdClass();
        $mockDescriptor->id       = mt_rand();
        $mockDescriptor->tag      = $type;
        $mockDescriptor->instance = ehough_mockery_Mockery::mock($type);

        $this->_mocks[] = $mockDescriptor;

        return $mockDescriptor->instance;
    }

    /**
     * @return ehough_mockery_mockery_MockInterface
     */
    protected final function getMockIocContainer()
    {
        return $this->_mockIocContainer;
    }

    public final function _getMockServiceById($id)
    {
        if (! is_array($this->_mocks)) {

            throw new RuntimeException("Failed to find singleton service with ID $id. Did you forget to call createMockSingletonService()?");
        }

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