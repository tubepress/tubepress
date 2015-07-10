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
 * @covers tubepress_array_ioc_ArrayExtension
 */
class tubepress_test_array_ioc_ArrayExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_array_ioc_ArrayExtension
     */
    protected function buildSut()
    {
        return  new tubepress_array_ioc_ArrayExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            tubepress_lib_api_array_ArrayReaderInterface::_,
            'tubepress_array_impl_ArrayReader'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        return array();
    }
}
