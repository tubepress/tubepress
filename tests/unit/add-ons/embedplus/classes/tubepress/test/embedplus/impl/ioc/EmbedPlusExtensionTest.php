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
 * @covers tubepress_embedplus_ioc_EmbedPlusExtension
 */
class tubepress_test_embedplus_impl_ioc_EmbedPlusExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    protected function buildSut()
    {
        return new tubepress_embedplus_ioc_EmbedPlusExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_embedplus_impl_listeners_EmbedPlusListener',
            'tubepress_embedplus_impl_listeners_EmbedPlusListener'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ContextInterface::_))
         ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::GALLERY_INIT_JS,
                'method'   => 'onGalleryInitJs',
                'priority' => 7000
            ))->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::TEMPLATE_SELECT . '.single/embedded',
                'method'   => 'onEmbeddedTemplateSelect',
                'priority' => 10500,
            ))->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL,
                'method'   => 'onPlayerImplAcceptableValues',
                'priority' => 10000,
            ));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            tubepress_app_api_options_ContextInterface::_ => tubepress_app_api_options_ContextInterface::_,
        );
    }
}