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
class tubepress_plugins_core_impl_patterns_ioc_CoreIocContainerCompilerPassTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_core_impl_patterns_ioc_CoreIocContainerCompilerPass
     */
    private $_sut;

    /**
     * @var ehough_iconic_ContainerBuilder
     */
    private $_mockParentContainer;

    public function onSetup()
    {
        $this->_sut = new tubepress_plugins_core_impl_patterns_ioc_CoreIocContainerCompilerPass();

        $this->_mockParentContainer = new ehough_iconic_ContainerBuilder();
    }

    public function testProcess()
    {
        $this->_sut->process($this->_mockParentContainer);

        foreach ($this->_getExpectedServices() as $service) {

            $definition = $this->_mockParentContainer->getDefinition($service->id);

            $this->assertNotNull($definition);

            $this->assertTrue($definition->getClass() === $service->type);

            if (isset($service->tag)) {

                $this->assertTrue($definition->hasTag($service->tag));
            }
        }
    }

    private function _getExpectedServices()
    {
        $map = array(

            array('ehough_shortstop_api_HttpResponseHandler', 'ehough_shortstop_impl_HttpResponseHandlerChain'),
        );

        $toReturn = array();

        foreach ($map as $s) {

            $service = new stdClass();
            $service->id = $s[0];
            $service->type = $s[1];

            if (isset($s[2])) {

                $service->tag = $s[2];
            }

            $toReturn[] = $service;
        }

        return $toReturn;
    }
}