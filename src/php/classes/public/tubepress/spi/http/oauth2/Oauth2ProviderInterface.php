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
 * Defines methods common among all OAuth services.
 */
interface tubepress_spi_http_oauth2_Oauth2ProviderInterface
{
    const _ = 'tubepress_spi_http_oauth2_Oauth2ProviderInterface';

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
     *                e.g. "YouTube" or "Vimeo"
     *
     * @api
     * @since 4.2.0
     */
    function getDisplayName();

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
     * Only invoked for authorization code grant type providers.
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
     * Defines the authorization grant type. TubePress is not guaranteed to support anything other
     * than "code" or "client_credentials". It's very unlikely that we will ever implement the implicit or resource
     * owner password credentials grant types.
     *
     * See https://tools.ietf.org/html/rfc6749#section-4
     *
     * @return string Either code or client_credentials
     */
    function getAuthorizationGrantType();

    /**
     * Only invoked for authorization code grant type providers.
     *
     * @return bool True if state is returned by the provider during authorization, false otherwise.
     *
     * @api
     * @since 4.2.0
     */
    function isStateUsed();

    /**
     * @return bool True if this provider uses the client secret, false otherwise.
     *
     * @api
     * @since 4.2.0
     */
    function isClientSecretUsed();

    /**
     * Only invoked for authorization code grant type providers.
     *
     * Modify the URL to which the user will be redirected for authorization. TubePress will have already
     * added the response_type, client_id, redirect_uri, and state parameters.
     *
     * See https://tools.ietf.org/html/rfc6749#section-4.1.1
     *
     * @param tubepress_api_url_UrlInterface $authorizationUrl The authorization URL.
     * @param string                         $clientId         The client ID.
     * @param string                         $clientSecret     The client secret, which may be null if the client
     *                                                         secret isn't used.
     *
     * @return void
     *
     * @api
     * @since 4.2.0
     */
    function onAuthorizationUrl(tubepress_api_url_UrlInterface $authorizationUrl,
                                $clientId,
                                $clientSecret = null);

    /**
     * Get the expected type of access token, or null if the provider does not return
     * an access token type (I'm looking at you, DailyMotion).
     *
     * See https://tools.ietf.org/html/rfc6749#section-7.1
     *
     * @return string|null
     *
     * @api
     * @since 4.2.0
     */
    function getAccessTokenType();

    /**
     * Modify the request to the token endpoint to supply any necessary credentials, add
     * parameters, etc. TubePress will have already added the grant_type and client_id parameters.
     *
     * In the case of "code" grant types, TubePress will have also added the code and redirect_uri parameters.
     *
     * See https://tools.ietf.org/html/rfc6749#section-4.1.3
     * See https://tools.ietf.org/html/rfc6749#section-4.4.2
     *
     * @param tubepress_api_http_message_RequestInterface $request      The access token request about to be sent.
     * @param string                                      $clientId     The client ID.
     * @param string                                      $clientSecret The client secret.
     *
     * @return void
     *
     * @api
     * @since 4.2.0
     */
    function onAccessTokenRequest(tubepress_api_http_message_RequestInterface $request,
                                  $clientId,
                                  $clientSecret = null);

    /**
     * Modify the request to the token endpoint to supply any necessary credentials, add
     * query parameters, etc. TubePress will have already added the grant_type and refresh_token parameters.
     *
     * See https://tools.ietf.org/html/rfc6749#section-6
     *
     * @param tubepress_api_http_message_RequestInterface $request      The access token request about to be sent.
     * @param tubepress_api_http_oauth_v2_TokenInterface  $token        The existing stored token.
     * @param string                                      $clientId     The client ID.
     * @param string                                      $clientSecret The client secret, which may be null if
     *                                                                  the client secret isn't used.
     *
     * @return void
     *
     * @api
     * @since 4.2.0
     */
    function onRefreshTokenRequest(tubepress_api_http_message_RequestInterface $request,
                                   tubepress_api_http_oauth_v2_TokenInterface  $token,
                                   $clientId,
                                   $clientSecret = null);

    /**
     * Generate a user-identifiable "slug" for this token. May contain alphanumerics, whitespace, and the following
     * characters: ()-_,
     *
     * The slug allows the user to easily identify the token. e.g. Eric Hough (eric@tubepress.com) or
     * Eric Hough (Vimeo username ehough).
     *
     * @param tubepress_api_http_oauth_v2_TokenInterface $token The access token.
     *
     * @return string The slug.
     *
     * @throws RuntimeException If the access token is invalid, or otherwise can't generate a slug for this token.
     *
     * @api
     * @since 4.2.0
     */
    function getSlugForToken(tubepress_api_http_oauth_v2_TokenInterface $token);

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
    function wantsToAuthorizeRequest(tubepress_api_http_message_RequestInterface $request);

    /**
     * Modify the outgoing protected resource request to supply authorization
     * using the given token.
     *
     * See https://tools.ietf.org/html/rfc6749#section-7
     *
     * @param tubepress_api_http_message_RequestInterface $request
     * @param tubepress_api_http_oauth_v2_TokenInterface  $token
     * @param string                                      $clientId
     * @param string                                      $clientSecret
     *
     * @return void
     *
     * @api
     * @since 4.2.0
     */
    function authorizeRequest(tubepress_api_http_message_RequestInterface $request,
                              tubepress_api_http_oauth_v2_TokenInterface  $token,
                              $clientId,
                              $clientSecret = null);

    /**
     * Get user instructions for client registration to be displayed in an ordered list. Each element of the return
     * value may be a string or array. If string, it's used as a top-level instruction. If it's an array, it will be
     * nested underneath the previous instruction.
     *
     * @param tubepress_api_translation_TranslatorInterface $translator  The translator.
     * @param tubepress_api_url_UrlInterface                $redirectUrl The redirect URL that should be used during registration.
     *
     * @return array
     *
     * @api
     * @since 4.2.0
     */
    function getTranslatedClientRegistrationInstructions(tubepress_api_translation_TranslatorInterface $translator,
                                                         tubepress_api_url_UrlInterface                $redirectUrl);

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
}