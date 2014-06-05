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
 * @covers tubepress_core_event_ioc_EventExtension
 */
class tubepress_test_core_event_ioc_EventExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_core_event_ioc_EventExtension
     */
    protected function buildSut()
    {
        return  new tubepress_core_event_ioc_EventExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            'ehough_tickertape_ContainerAwareEventDispatcher',
            'ehough_tickertape_ContainerAwareEventDispatcher'

        )->withArgument(new tubepress_api_ioc_Reference('ehough_iconic_ContainerInterface'));

        $this->expectRegistration(

            tubepress_core_event_api_EventDispatcherInterface::_,
            'tubepress_core_event_impl_tickertape_EventDispatcher'

        )->withArgument(new tubepress_api_ioc_Reference('ehough_tickertape_ContainerAwareEventDispatcher'));
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            tubepress_core_event_api_EventDispatcherInterface::_ => 'tubepress_core_event_impl_tickertape_EventDispatcher',
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            'ehough_iconic_ContainerInterface' => 'ehough_iconic_ContainerInterface'
        );
    }
}
