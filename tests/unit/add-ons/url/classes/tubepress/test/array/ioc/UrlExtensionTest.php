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
 * @covers tubepress_url_ioc_UrlExtension
 */
class tubepress_test_url_ioc_ArrayExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_url_ioc_UrlExtension
     */
    protected function buildSut()
    {
        return  new tubepress_url_ioc_UrlExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            tubepress_platform_api_url_UrlFactoryInterface::_,
            'tubepress_url_impl_puzzle_UrlFactory'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        return array();
    }
}
