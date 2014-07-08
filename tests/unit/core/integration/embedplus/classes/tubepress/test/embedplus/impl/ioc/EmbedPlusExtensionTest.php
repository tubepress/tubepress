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
class tubepress_test_embedplus_impl_ioc_EmbedPlusExtensionTest extends tubepress_test_ioc_AbstractIocContainerExtensionTest
{
    protected function buildSut()
    {
        return new tubepress_embedplus_ioc_EmbedPlusExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_embedplus_impl_embedded_EmbedPlusProvider',
            'tubepress_embedplus_impl_embedded_EmbedPlusProvider'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_url_api_UrlFactoryInterface::_))
         ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_template_api_TemplateFactoryInterface::_))
         ->withTag(tubepress_app_embedded_api_EmbeddedProviderInterface::_);

        $this->expectRegistration(
            'tubepress_embedplus_impl_listeners_js_JsOptionsListener',
            'tubepress_embedplus_impl_listeners_js_JsOptionsListener'
        )->withTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event' => tubepress_app_feature_gallery_api_Constants::EVENT_GALLERY_INIT_JS,
                'method' => 'onGalleryInitJs',
                'priority' => 7000
            ));
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            'tubepress_embedplus_impl_embedded_EmbedPlusProvider' =>
                'tubepress_embedplus_impl_embedded_EmbedPlusProvider'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            tubepress_lib_url_api_UrlFactoryInterface::_ => tubepress_lib_url_api_UrlFactoryInterface::_,
            tubepress_lib_template_api_TemplateFactoryInterface::_ => tubepress_lib_template_api_TemplateFactoryInterface::_
        );
    }
}