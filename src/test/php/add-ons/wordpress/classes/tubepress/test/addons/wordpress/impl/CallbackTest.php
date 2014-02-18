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
 * @covers tubepress_addons_wordpress_impl_Callback
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class tubepress_test_addons_wordpress_impl_CallbackTest extends tubepress_test_TubePressUnitTest
{
    public function onSetup()
    {
        $mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $mockWpFunctions         = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);

        $mockWpFunctions->shouldReceive('content_url')->once()->andReturn('booya');
        $mockEnvironmentDetector->shouldReceive('setBaseUrl')->once()->with('booya/plugins/tubepress');
    }

    public function testFilter()
    {
        $mock = $this->createMockSingletonService('wordpress.filter.someFilter');

        $args = array(1, 'two', array('three'));

        $mock->shouldReceive('filter')->once()->with($args)->andReturn('xyz');

        $result = tubepress_addons_wordpress_impl_Callback::onFilter('someFilter', $args);

        $this->assertEquals('xyz', $result);
    }

    public function testAction()
    {
        $mock = $this->createMockSingletonService('wordpress.action.someAction');

        $args = array(1, 'two', array('three'));

        $mock->shouldReceive('execute')->once()->with($args);

        tubepress_addons_wordpress_impl_Callback::onAction('someAction', $args);

        $this->assertTrue(true);
    }

    public function testPluginActivation()
    {
        $mock = $this->createMockSingletonService('wordpress.pluginActivator');

        $mock->shouldReceive('execute')->once();

        tubepress_addons_wordpress_impl_Callback::onPluginActivation();

        $this->assertTrue(true);
    }
}