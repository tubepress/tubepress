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
 * @covers tubepress_dailymotion_ioc_DailymotionExtension
 */
class tubepress_test_dailymotion_ioc_DailymotionExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{

    /**
     * @return tubepress_spi_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_dailymotion_ioc_DailymotionExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerEmbedded();
        $this->_registerApiUtility();
        $this->_registerMediaProvider();
        $this->_registerPlayer();
    }

    private function _registerPlayer()
    {
        $this->expectRegistration(
            'tubepress_dailymotion_impl_player_DailymotionPlayerLocation',
            'tubepress_dailymotion_impl_player_DailymotionPlayerLocation'
        )->withTag('tubepress_spi_player_PlayerLocationInterface');
    }

    private function _registerMediaProvider()
    {
        $this->expectRegistration(
            'tubepress_dailymotion_impl_media_FeedHandler',
            'tubepress_dailymotion_impl_media_FeedHandler'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_array_ArrayReaderInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference('tubepress_dailymotion_impl_ApiUtility'));

        $this->expectRegistration(
            'tubepress_dailymotion_impl_media_MediaProvider',
            'tubepress_dailymotion_impl_media_MediaProvider'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_media_HttpCollectorInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference('tubepress_dailymotion_impl_media_FeedHandler'))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->withTag(tubepress_spi_media_MediaProviderInterface::__);
    }

    private function _registerApiUtility()
    {
        $this->expectRegistration(
            'tubepress_dailymotion_impl_ApiUtility',
            'tubepress_dailymotion_impl_ApiUtility'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_HttpClientInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_array_ArrayReaderInterface::_));
    }

    private function _registerEmbedded()
    {
        $this->expectRegistration(
            'tubepress_dailymotion_impl_embedded_DailymotionEmbeddedProvider',
            'tubepress_dailymotion_impl_embedded_DailymotionEmbeddedProvider'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->withTag('tubepress_spi_embedded_EmbeddedProviderInterface')
         ->withTag('tubepress_spi_template_PathProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        $mockLogger      = $this->mock(tubepress_api_log_LoggerInterface::_);
        $mockBaseUrl     = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockEnvironment = $this->mock(tubepress_api_environment_EnvironmentInterface::_);

        $mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);
        $mockEnvironment->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);
        $mockBaseUrl->shouldReceive('getClone')->once()->andReturn($mockBaseUrl);
        $mockBaseUrl->shouldReceive('addPath')->once()->with('src/add-ons/provider-dailymotion/web/images/icons/dailymotion-icon-34w_x_34h.png')->andReturn($mockBaseUrl);
        $mockBaseUrl->shouldReceive('toString')->once()->andReturn('mock-base-url');

        return array(
            tubepress_api_options_ContextInterface::_         => tubepress_api_options_ContextInterface::_,
            tubepress_api_util_LangUtilsInterface::_          => tubepress_api_util_LangUtilsInterface::_,
            tubepress_api_url_UrlFactoryInterface::_          => tubepress_api_url_UrlFactoryInterface::_,
            tubepress_api_log_LoggerInterface::_              => $mockLogger,
            tubepress_api_http_HttpClientInterface::_         => tubepress_api_http_HttpClientInterface::_,
            tubepress_api_array_ArrayReaderInterface::_       => tubepress_api_array_ArrayReaderInterface::_,
            tubepress_api_media_HttpCollectorInterface::_     => tubepress_api_media_HttpCollectorInterface::_,
            tubepress_api_environment_EnvironmentInterface::_ => $mockEnvironment,
            tubepress_api_util_StringUtilsInterface::_        => tubepress_api_util_StringUtilsInterface::_,
        );
    }
}