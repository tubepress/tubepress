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
interface tubepress_spi_url_UrlFactoryInterface
{
    const _ = 'tubepress_spi_url_UrlFactoryInterface';

    /**
     * @param string $url The URL to parse.
     *
     * @return tubepress_api_url_UrlInterface
     *
     * @throws InvalidArgumentException If unable to parse URL.
     */
    function fromString($url);
}