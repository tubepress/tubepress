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
 * @covers tubepress_embedplus_ioc_EmbedPlusExtension
 */
class tubepress_test_embedplus_ioc_EmbedPlusExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    protected function buildSut()
    {
        return new tubepress_embedplus_ioc_EmbedPlusExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_embedplus_impl_EmbedPlus',
            'tubepress_embedplus_impl_EmbedPlus'
        )->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_url_UrlFactoryInterface::_))
            ->withTag('tubepress_app_api_embedded_EmbeddedProviderInterface')
        ->withTag('tubepress_lib_api_template_PathProviderInterface')
            ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_app_api_event_Events::GALLERY_INIT_JS,
                'method'   => 'onGalleryInitJs',
                'priority' => 94000
            ));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            tubepress_platform_api_url_UrlFactoryInterface::_ => tubepress_platform_api_url_UrlFactoryInterface::_,
        );
    }
}