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

class tubepress_test_scripts_boot_MockOauth2Environment implements tubepress_api_http_oauth2_Oauth2EnvironmentInterface
{
    /**
     * @param tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider
     *
     * @return tubepress_api_url_UrlInterface
     *
     * @api
     * @since 4.2.0
     */
    public function getRedirectionUrl(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        // TODO: Implement getRedirectionUrl() method.
    }

    /**
     * @param tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider
     *
     * @return tubepress_api_url_UrlInterface
     *
     * @api
     * @since 4.2.0
     */
    public function getAuthorizationInitiationUrl(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        // TODO: Implement getAuthorizationInitiationUrl() method.
    }

    /**
     * The secret that will be added as a query parameter to the redirect URI in order to help prevent
     * CSRF attacks. The OAuth2 state parameter is normally used for this purpose, but not all OAuth providers
     * use it and state isn't used in the client_credentials grant type.
     *
     * This code should be kept secret, unique to each TubePress installation, and persistent (i.e. never changes).
     *
     * @return string
     *
     * @api
     * @since 4.2.0
     */
    public function getCsrfSecret()
    {
        // TODO: Implement getCsrfSecret() method.
    }
}