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

class tubepress_test_scripts_boot_MockOauth2UrlProvider implements tubepress_spi_http_oauth_v2_Oauth2UrlProviderInterface
{
    /**
     * @param tubepress_spi_http_oauth_v2_Oauth2ProviderInterface $provider
     *
     * @return tubepress_api_url_UrlInterface
     *
     * @api
     * @since 4.2.0
     */
    public function getRedirectionUrl(tubepress_spi_http_oauth_v2_Oauth2ProviderInterface $provider)
    {
        // TODO: Implement getRedirectionUrl() method.
    }

    /**
     * @param tubepress_spi_http_oauth_v2_Oauth2ProviderInterface $provider
     *
     * @return tubepress_api_url_UrlInterface
     *
     * @api
     * @since 4.2.0
     */
    public function getAuthorizationInitiationUrl(tubepress_spi_http_oauth_v2_Oauth2ProviderInterface $provider)
    {
        // TODO: Implement getAuthorizationInitiationUrl() method.
    }
}