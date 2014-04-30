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

/**
 * @covers tubepress_addons_puzzle_impl_ioc_IocContainerExtension
 */
class tubepress_test_addons_puzzle_impl_ioc_IocContainerExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{
    /**
     * @return tubepress_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_addons_puzzle_impl_ioc_IocContainerExtension();
    }

    protected function prepareForLoad()
    {
        $this->_expectHttpClient();
        $this->_expectUrlFactory();
    }

    private function _expectHttpClient()
    {
        $this->expectRegistration(

            'puzzle.client',
            'puzzle_Client'
        );

        $this->expectRegistration(

            tubepress_spi_http_HttpClientInterface::_,
            'tubepress_addons_puzzle_impl_http_PuzzleHttpClient'
        )->withArgument(new tubepress_impl_ioc_Reference('puzzle.client'))
         ->withTag(tubepress_spi_http_HttpClientInterface::_);
    }

    private function _expectUrlFactory()
    {
        $this->expectRegistration(

            tubepress_spi_url_UrlFactoryInterface::_,
            'tubepress_addons_puzzle_impl_url_UrlFactory'
        );
    }
}