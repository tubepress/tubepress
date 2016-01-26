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
        return array(
            tubepress_api_options_ContextInterface::_ => tubepress_api_options_ContextInterface::_,
            tubepress_api_util_LangUtilsInterface::_  => tubepress_api_util_LangUtilsInterface::_,
            tubepress_api_url_UrlFactoryInterface::_  => tubepress_api_url_UrlFactoryInterface::_,
        );
    }
}