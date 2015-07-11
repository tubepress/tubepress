<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_options_ioc_OptionsExtension
 */
class tubepress_test_options_ioc_OptionsExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_options_ioc_OptionsExtension
     */
    protected function buildSut()
    {
        return  new tubepress_options_ioc_OptionsExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            tubepress_app_api_options_AcceptableValuesInterface::_,
            'tubepress_options_impl_AcceptableValues'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_));

        $this->expectRegistration(
            tubepress_app_api_options_ContextInterface::_,
            'tubepress_options_impl_Context'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_PersistenceInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ReferenceInterface::_));

        $this->expectRegistration(
            tubepress_app_api_options_ReferenceInterface::_,
            'tubepress_options_impl_DispatchingReference'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
            ->withTag(tubepress_lib_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_app_api_options_ReferenceInterface::_,
                'method' => 'setReferences'
            ));

        $this->expectRegistration(
            tubepress_app_api_options_PersistenceInterface::_,
            'tubepress_options_impl_Persistence'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ReferenceInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_PersistenceBackendInterface::_));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(
            tubepress_lib_api_event_EventDispatcherInterface::_ => tubepress_lib_api_event_EventDispatcherInterface::_,
            tubepress_app_api_options_PersistenceBackendInterface::_ => tubepress_app_api_options_PersistenceBackendInterface::_,
        );
    }
}
