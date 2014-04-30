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
class tubepress_addons_puzzle_impl_url_UrlFactory implements tubepress_spi_url_UrlFactoryInterface
{
    /**
     * @param string $url The URL to parse.
     *
     * @return tubepress_api_url_UrlInterface
     *
     * @throws InvalidArgumentException If unable to parse URL.
     */
    public function fromString($url)
    {
        if (!is_string($url)) {

            throw new InvalidArgumentException('tubepress_addons_puzzle_impl_url_UrlFactory::fromString() can only accept strings.');
        }

        return new tubepress_addons_puzzle_impl_url_PuzzleBasedUrl(puzzle_Url::fromString($url));
    }
}