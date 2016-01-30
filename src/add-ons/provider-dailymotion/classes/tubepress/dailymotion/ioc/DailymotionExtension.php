<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Registers a few extensions to allow TubePress to work with YouTube.
 */
class tubepress_dailymotion_ioc_DailymotionExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * Allows extensions to load services into the TubePress IOC container.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder A tubepress_api_ioc_ContainerBuilderInterface instance.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerEmbedded($containerBuilder);
        $this->_registerApiUtility($containerBuilder);
        $this->_registerMediaProvider($containerBuilder);
        $this->_registerPlayer($containerBuilder);
    }

    private function _registerPlayer(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_dailymotion_impl_player_DailymotionPlayerLocation',
            'tubepress_dailymotion_impl_player_DailymotionPlayerLocation'
        )->addTag('tubepress_spi_player_PlayerLocationInterface');
    }

    private function _registerMediaProvider(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_dailymotion_impl_media_FeedHandler',
            'tubepress_dailymotion_impl_media_FeedHandler'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_array_ArrayReaderInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_dailymotion_impl_ApiUtility'));

        $containerBuilder->register(
            'tubepress_dailymotion_impl_media_MediaProvider',
            'tubepress_dailymotion_impl_media_MediaProvider'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_media_HttpCollectorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_dailymotion_impl_media_FeedHandler'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->addTag(tubepress_spi_media_MediaProviderInterface::__);
    }

    private function _registerApiUtility(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_dailymotion_impl_ApiUtility',
            'tubepress_dailymotion_impl_ApiUtility'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_HttpClientInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_array_ArrayReaderInterface::_));
    }

    private function _registerEmbedded(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_dailymotion_impl_embedded_DailymotionEmbeddedProvider',
            'tubepress_dailymotion_impl_embedded_DailymotionEmbeddedProvider'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->addTag('tubepress_spi_embedded_EmbeddedProviderInterface')
         ->addTag('tubepress_spi_template_PathProviderInterface');
    }
}