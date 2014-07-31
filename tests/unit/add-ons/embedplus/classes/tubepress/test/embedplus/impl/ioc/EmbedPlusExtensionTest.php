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
            'tubepress_embedplus_impl_listeners_js_JsOptionsListener',
            'tubepress_embedplus_impl_listeners_js_JsOptionsListener'
        )->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event' => tubepress_app_api_event_Events::GALLERY_INIT_JS,
                'method' => 'onGalleryInitJs',
                'priority' => 7000
            ));
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            'tubepress_embedplus_impl_listeners_embedded_EmbeddedListener' =>
                'tubepress_embedplus_impl_listeners_embedded_EmbeddedListener'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            tubepress_platform_api_url_UrlFactoryInterface::_ => tubepress_platform_api_url_UrlFactoryInterface::_,
            tubepress_lib_api_template_TemplatingInterface::_ => tubepress_lib_api_template_TemplatingInterface::_
        );
    }
}