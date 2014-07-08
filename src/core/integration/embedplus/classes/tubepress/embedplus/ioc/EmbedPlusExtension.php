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
 * Registers a few extensions to allow TubePress to work with EmbedPlus.
 */
class tubepress_embedplus_ioc_EmbedPlusExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
{
    /**
     * Allows extensions to load services into the TubePress IOC container.
     *
     * @param tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder A tubepress_platform_api_ioc_ContainerBuilderInterface instance.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function load(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_embedplus_impl_embedded_EmbedPlusProvider',
            'tubepress_embedplus_impl_embedded_EmbedPlusProvider'

        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_url_api_UrlFactoryInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_template_api_TemplateFactoryInterface::_))
         ->addTag(tubepress_app_embedded_api_EmbeddedProviderInterface::_);

        $containerBuilder->register(
            'tubepress_embedplus_impl_listeners_js_JsOptionsListener',
            'tubepress_embedplus_impl_listeners_js_JsOptionsListener'
        )->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event' => tubepress_app_feature_gallery_api_Constants::EVENT_GALLERY_INIT_JS,
            'method' => 'onGalleryInitJs',
            'priority' => 7000
        ));
    }
}