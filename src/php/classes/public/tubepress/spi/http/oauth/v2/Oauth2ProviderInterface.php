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
 * Defines methods common among all OAuth services.
 */
interface tubepress_spi_http_oauth_v2_Oauth2ProviderInterface
{
    const _ = 'tubepress_spi_http_oauth_v2_Oauth2ProviderInterface';

    /**
     * TubePress will use this to uniquely identify the OAuth provider.
     *
     * @return string The globally-unique name of this OAuth provider. Never empty or null.
     *                All lowercase alphanumerics and dashes.
     *
     * @api
     * @since 4.2.0
     */
    function getName();

    /**
     * @return string The human-readable name of this OAuth provider. This will be displayed to the user.
     *
     * @api
     * @since 4.2.0
     */
    function getDisplayName();



    /**
     *
     * See https://tools.ietf.org/html/rfc6749#section-3.1
     *
     * @return tubepress_api_url_UrlInterface The authorization API endpoint.
     *
     * @api
     * @since 4.2.0
     */
    function getAuthorizationEndpoint();

    /**
     * Defines the authorization grant type. TubePress is not guaranteed to work with anything other
     * than "code", though in the future clientCredentials might be implemented. It's very unlikely
     * that we will ever implement the implicit or resource owner password credentials grant types.
     *
     * See https://tools.ietf.org/html/rfc6749#section-4
     *
     * @return string Either code or client_credentials
     */
    function getAuthorizationGrantType();

    /**
     * Modify the URL to which the user will be redirected for authorization. You may add or remove query parameters
     * if you wish. If the client credentials grant type is in use, this function will not be invoked.
     *
     * TubePress will have already added the response_type query parameter, at a minimum.
     *
     * See https://tools.ietf.org/html/rfc6749#section-4.1.1
     * See https://tools.ietf.org/html/rfc6749#section-4.4.1
     *
     * @param tubepress_api_url_UrlInterface $authorizationUrl
     *
     * @return void
     *
     * @api
     * @since 4.2.0
     */
    function onAuthorizationUrl(tubepress_api_url_UrlInterface $authorizationUrl);

    /**
     * Returns the access token API endpoint.
     *
     * See https://tools.ietf.org/html/rfc6749#section-3.2
     *
     * @return tubepress_api_url_UrlInterface
     *
     * @api
     * @since 4.2.0
     */
    function getTokenEndpoint();

    /**
     * Modify the request to the token endpoint to supply any necessary credentials, add
     * query parameters, etc. TubePress will have already added the grant_type query parameter,
     * at a minimum.
     *
     * See https://tools.ietf.org/html/rfc6749#section-4.1.3
     * See https://tools.ietf.org/html/rfc6749#section-4.4.2
     * See https://tools.ietf.org/html/rfc6749#section-6
     *
     * @param tubepress_api_http_message_RequestInterface $request          The access token request about to be sent.
     * @param boolean                                     $isRefreshRequest True if this is a refresh request,
     *                                                                      false otherwise.
     *
     * @return void
     *
     * @api
     * @since 4.2.0
     */
    function onAccessTokenRequest(tubepress_api_http_message_RequestInterface $request, $isRefreshRequest);

    /**
     * Called before TubePress sends out an API request.
     *
     * See https://tools.ietf.org/html/rfc6749#section-7
     *
     * @param tubepress_api_http_message_RequestInterface $request
     *
     * @return boolean True if this provider is interested in authorizing the request, false otherwise.
     *
     * @api
     * @since 4.2.0
     */
    function wantsToAuthorizeProtectedResourceRequest(tubepress_api_http_message_RequestInterface $request);

    /**
     * Modify the outgoing protected resource request to supply authorization
     * using the given token.
     *
     * See https://tools.ietf.org/html/rfc6749#section-7
     *
     * @param tubepress_api_http_message_RequestInterface $request
     * @param tubepress_api_http_oauth_v2_TokenInterface  $token
     *
     * @return void
     *
     * @api
     * @since 4.2.0
     */
    function authorizeProtectedResourceRequest(tubepress_api_http_message_RequestInterface $request,
                                               tubepress_api_http_oauth_v2_TokenInterface  $token);

    /**
     * @return string
     *
     * @api
     * @since 4.2.0
     */
    function getUntranslatedTermForClientId();

    /**
     * @return string
     *
     * @api
     * @since 4.2.0
     */
    function getUntranslatedTermForClientSecret();

    /**
     * @return string
     *
     * @api
     * @since 4.2.0
     */
    function getUntranslatedTermForRedirectEndpoint();
}
