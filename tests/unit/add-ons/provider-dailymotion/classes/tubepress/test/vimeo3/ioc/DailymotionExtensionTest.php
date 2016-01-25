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

    }

    protected function getExpectedExternalServicesMap()
    {
        return array();
    }
}