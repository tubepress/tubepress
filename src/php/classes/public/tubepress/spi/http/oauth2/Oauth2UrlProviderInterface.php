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

interface tubepress_spi_http_oauth2_Oauth2UrlProviderInterface
{
    const _ = 'tubepress_spi_http_oauth2_Oauth2UrlProviderInterface';

    /**
     * @param tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider
     *
     * @return tubepress_api_url_UrlInterface
     *
     * @api
     * @since 4.2.0
     */
    function getRedirectionUrl(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider);

    /**
     * @param tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider
     *
     * @return tubepress_api_url_UrlInterface
     *
     * @api
     * @since 4.2.0
     */
    function getAuthorizationInitiationUrl(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider);
}