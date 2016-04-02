<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
class tubepress_embedplus_ioc_EmbedPlusExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_embedplus_impl_EmbedPlus',
            'tubepress_embedplus_impl_EmbedPlus'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->addTag('tubepress_spi_embedded_EmbeddedProviderInterface')
         ->addTag('tubepress_spi_template_PathProviderInterface')
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_event_Events::GALLERY_INIT_JS,
            'method'   => 'onGalleryInitJs',
            'priority' => 94000,
        ));
    }
}
