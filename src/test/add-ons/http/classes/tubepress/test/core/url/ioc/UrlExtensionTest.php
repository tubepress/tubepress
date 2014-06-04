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
 * @covers tubepress_core_url_ioc_UrlExtension
 */
class tubepress_test_core_url_ioc_UrlExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{
    /**
     * @return tubepress_core_url_ioc_UrlExtension
     */
    protected function buildSut()
    {
        return  new tubepress_core_url_ioc_UrlExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            tubepress_core_url_api_UrlFactoryInterface::_,
            'tubepress_core_url_impl_puzzle_UrlFactory'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

        );
    }
}
