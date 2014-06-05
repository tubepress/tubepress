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
 * @covers tubepress_core_contrib_ioc_ContributableExtension
 */
class tubepress_test_core_contrib_ioc_ContributableExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{
    /**
     * @return tubepress_core_contrib_ioc_ContributableExtension
     */
    protected function buildSut()
    {
        return new tubepress_core_contrib_ioc_ContributableExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            tubepress_core_contrib_api_ContributableValidatorInterface::_,
            'tubepress_core_contrib_impl_ContributableValidator'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_url_api_UrlFactoryInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_LangUtilsInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_))
         ->withTag(tubepress_core_contrib_api_ContributableValidatorInterface::_, array('type' => tubepress_core_theme_api_ThemeInterface::_));
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            tubepress_core_contrib_api_ContributableValidatorInterface::_ => 'tubepress_core_contrib_impl_ContributableValidator',
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            tubepress_core_url_api_UrlFactoryInterface::_ => tubepress_core_url_api_UrlFactoryInterface::_,
            tubepress_api_util_LangUtilsInterface::_      => tubepress_api_util_LangUtilsInterface::_,
            tubepress_api_util_StringUtilsInterface::_    => tubepress_api_util_StringUtilsInterface::_,
        );
    }
}