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
 * @covers tubepress_core_ioc_VendorsExtension<extended>
 */
class tubepress_test_core_impl_ioc_compiler_VendorsExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{
    /**
     * @return tubepress_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_core_ioc_VendorsExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            'ehough_filesystem_FilesystemInterface',
            'ehough_filesystem_Filesystem'
        );

        $this->expectRegistration(

            'ehough_finder_FinderFactoryInterface',
            'ehough_finder_FinderFactory'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            'service_container' => 'tubepress_api_ioc_ContainerInterface'
        );
    }
}