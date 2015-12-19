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

class tubepress_test_integration_mocks_MocksExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{

    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_api_translation_TranslatorInterface::_,
            'tubepress_test_integration_mocks_MockTranslator'
        );

        $containerBuilder->register(
            tubepress_spi_options_PersistenceBackendInterface::_,
            'tubepress_test_integration_mocks_MockPersistence'
        );

        $containerBuilder->register(

            'ehough_stash_interfaces_DriverInterface',
            'tubepress_test_integration_mocks_MockCacheDriver'
        );

        $containerBuilder->register(

            tubepress_spi_http_oauth_v2_Oauth2UrlProviderInterface::_,
            'tubepress_test_integration_mocks_MockOauth2UrlProvider'
        );
    }
}