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
 * @api
 * @since 4.0.0
 */
interface tubepress_api_url_UrlFactoryInterface extends tubepress_platform_api_url_UrlFactoryInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_api_url_UrlFactoryInterface';

    /**
     * @param string $url The URL to parse.
     *
     * @return tubepress_api_url_UrlInterface
     *
     * @throws InvalidArgumentException If unable to parse URL.
     *
     * @api
     * @since 4.0.0
     */
    function fromString($url);

    /**
     * The current URL shown to the user.
     *
     * @return tubepress_api_url_UrlInterface
     *
     * @throws RuntimeException If unable to determine current URL.
     *
     * @api
     * @since 4.0.0
     */
    function fromCurrent();
}