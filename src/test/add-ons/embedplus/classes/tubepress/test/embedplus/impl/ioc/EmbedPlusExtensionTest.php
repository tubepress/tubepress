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
 * @covers tubepress_embedplus_impl_ioc_EmbedPlusExtension
 */
class tubepress_test_embedplus_impl_ioc_EmbedPlusExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{
    protected function buildSut()
    {
        return new tubepress_embedplus_impl_ioc_EmbedPlusExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_embedplus_impl_embedded_EmbedPlusEmbeddedProviderService',
            'tubepress_embedplus_impl_embedded_EmbedPlusEmbeddedProviderService'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_url_api_UrlFactoryInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_))
         ->withTag(tubepress_core_embedded_api_EmbeddedProviderInterface::_);
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            'tubepress_embedplus_impl_embedded_EmbedPlusEmbeddedProviderService' =>
                'tubepress_embedplus_impl_embedded_EmbedPlusEmbeddedProviderService'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            tubepress_core_url_api_UrlFactoryInterface::_ => tubepress_core_url_api_UrlFactoryInterface::_,
            tubepress_core_template_api_TemplateFactoryInterface::_ => tubepress_core_template_api_TemplateFactoryInterface::_
        );
    }
}