<?php
/**
 * Copyright 2006 - 2013 Eric D. Hough (http://ehough.com)
 *
 * This file is part of coauthor (https://github.com/ehough/coauthor)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_addons_puzzle_impl_ioc_IocContainerExtension implements tubepress_api_ioc_ContainerExtensionInterface
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
     * @since 3.2.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerHttpClient($containerBuilder);
        $this->_registerUrlFactory($containerBuilder);
    }

    private function _registerUrlFactory(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_spi_url_UrlFactoryInterface::_,
            'tubepress_addons_puzzle_impl_url_UrlFactory'
        );
    }

    private function _registerHttpClient(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'puzzle.client',
            'puzzle_Client'
        );

        $containerBuilder->register(

            tubepress_spi_http_HttpClientInterface::_,
            'tubepress_addons_puzzle_impl_http_PuzzleHttpClient'
        )->addArgument(new tubepress_impl_ioc_Reference('puzzle.client'))
         ->addTag(tubepress_spi_http_HttpClientInterface::_);
    }
}