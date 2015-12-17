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

/**
 *
 */
class tubepress_vimeo3_impl_oauth_VimeoOauth2Provider implements tubepress_spi_http_oauth_v2_Oauth2ProviderInterface
{
    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    public function __construct(tubepress_api_url_UrlFactoryInterface   $urlFactory,
                                tubepress_api_util_StringUtilsInterface $stringUtils)
    {
        $this->_urlFactory  = $urlFactory;
        $this->_stringUtils = $stringUtils;
    }

    /**
     * TubePress will use this to uniquely identify the OAuth provider.
     *
     * @return string The globally-unique name of this OAuth provider. Never empty or null.
     *                All lowercase alphanumerics and dashes.
     *
     * @api
     * @since 4.2.0
     */
    public function getName()
    {
        return 'vimeo.v3';
    }

    /**
     * @return string The human-readable name of this OAuth provider. This will be displayed to the user.
     *                e.g. "YouTube" or "Vimeo"
     *
     * @api
     * @since 4.2.0
     */
    public function getDisplayName()
    {
        return 'Vimeo';
    }

    /**
     *
     * See https://tools.ietf.org/html/rfc6749#section-3.1
     *
     * @return tubepress_api_url_UrlInterface The authorization API endpoint.
     *
     * @api
     * @since 4.2.0
     */
    public function getAuthorizationEndpoint()
    {
        return $this->_urlFactory->fromString('https://api.vimeo.com/oauth/authorize');
    }

    /**
     * Defines the authorization grant type. TubePress is not guaranteed to support anything other
     * than "code", though in the future clientCredentials might be implemented. It's very unlikely
     * that we will ever implement the implicit or resource owner password credentials grant types.
     *
     * See https://tools.ietf.org/html/rfc6749#section-4
     *
     * @return string Either code or client_credentials
     */
    public function getAuthorizationGrantType()
    {
        return 'code';
    }

    /**
     * Only invoked for authorization code grant type providers.
     *
     * Modify the URL to which the user will be redirected for authorization. TubePress will have already
     * added the response_type query parameter, at a minimum.
     *
     * See https://tools.ietf.org/html/rfc6749#section-4.1.1
     *
     * @param tubepress_api_url_UrlInterface $authorizationUrl The authorization URL.
     *
     * @return void
     *
     * @api
     * @since 4.2.0
     */
    public function onAuthorizationUrl(tubepress_api_url_UrlInterface $authorizationUrl,
                                       $clientId, $clientSecret = null)
    {
        //noop as we've already added everything we need.
    }

    /**
     * @return bool True if state is returned by the provider during authorization, false otherwise.
     *
     * @api
     * @since 4.2.0
     */
    public function isStateUsed()
    {
        return true;
    }

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
    public function getTokenEndpoint()
    {
        return $this->_urlFactory->fromString('https://api.vimeo.com/oauth/access_token');
    }

    /**
     * Get the expected type of access token.
     *
     * See https://tools.ietf.org/html/rfc6749#section-7.1
     *
     * @return string
     *
     * @api
     * @since 4.2.0
     */
    public function getAccessTokenType()
    {
        return 'bearer';
    }

    /**
     * Modify the request to the token endpoint to supply any necessary credentials, add
     * query parameters, etc. TubePress will have already added the grant_type query parameter,
     * at a minimum.
     *
     * See https://tools.ietf.org/html/rfc6749#section-6
     *
     * @param tubepress_api_http_message_RequestInterface $request The access token request about to be sent.
     * @param tubepress_api_http_oauth_v2_TokenInterface $token The existing stored token.
     *
     * @return void
     *
     * @api
     * @since 4.2.0
     */
    public function onRefreshTokenRequest(tubepress_api_http_message_RequestInterface $request,
                                          tubepress_api_http_oauth_v2_TokenInterface  $token,
                                          $clientId, $clientSecret = null)
    {
        //noop - Vimeo tokens are permanent
    }

    /**
     * Generate a user-identifiable "slug" for this token. May contains alphanumerics, whitespace, and the following
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
    public function getSlugForToken(tubepress_api_http_oauth_v2_TokenInterface $token)
    {
        $extraParams = $token->getExtraParams();

        if (isset($extraParams['user']) && isset($extraParams['user']['name'])) {

            $name = $extraParams['user']['name'];

            if (!isset($extraParams['scope'])) {

                return $name;
            }

            $scopeList = $extraParams['scope'];

            if (strpos($scopeList, 'private') !== false) {

                return "$name (All Access)";
            }

            return $name;
        }

        return 'Vimeo-' . md5($token->getAccessToken());
    }

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
    public function wantsToAuthorizeRequest(tubepress_api_http_message_RequestInterface $request)
    {
        $url = $request->getUrl();

        if ($url->getHost() !== 'api.vimeo.com') {

            return false;
        }

        $path      = $url->getPath();
        $oauthPath = $this->_stringUtils->startsWith($path, '/oauth');

        return !$oauthPath;
    }

    /**
     * Modify the outgoing protected resource request to supply authorization
     * using the given token.
     *
     * See https://tools.ietf.org/html/rfc6749#section-7
     *
     * @param tubepress_api_http_message_RequestInterface $request
     * @param tubepress_api_http_oauth_v2_TokenInterface $token
     *
     * @return void
     *
     * @api
     * @since 4.2.0
     */
    public function authorizeRequest(tubepress_api_http_message_RequestInterface $request,
                                     tubepress_api_http_oauth_v2_TokenInterface  $token,
                                     $clientId, $clientSecret = null)
    {
        $request->setHeader('Authorization', 'bearer ' . $token->getAccessToken());
        $request->setHeader('Accept', 'application/vnd.vimeo.*+json;version=3.2');
    }

    /**
     * @param tubepress_api_translation_TranslatorInterface $translator The translator to use.
     *
     * @return string[]
     *
     * @api
     * @since 4.2.0
     */
    public function getTranslatedClientRegistrationInstructions(tubepress_api_translation_TranslatorInterface $translator)
    {
        /** @noinspection HtmlUnknownTarget */
        return array(

            $translator->trans(

                '<a href="%client-registration-url%" target="_blank">Click here</a> to "Create a new app" with Vimeo', //>(translatable)<
                array('%client-registration-url%' => 'https://developer.vimeo.com/apps/new')
            ),
            $translator->trans(
                'Use anything you\'d like for the App Name, App Description, and App URL'   //>(translatable)<
            ),
        );
    }

    /**
     * @param tubepress_api_translation_TranslatorInterface $translator The translator to use.
     *
     * @return string
     *
     * @api
     * @since 4.2.0
     */
    public function getTranslatedTermForClientId(tubepress_api_translation_TranslatorInterface $translator)
    {
        return $translator->trans('Client Identifier'); //>(translatable)<
    }

    /**
     * @param tubepress_api_translation_TranslatorInterface $translator The translator to use.
     *
     * @return string
     *
     * @api
     * @since 4.2.0
     */
    public function getTranslatedTermForClientSecret(tubepress_api_translation_TranslatorInterface $translator)
    {
        return $translator->trans('Client Secret'); //>(translatable)<
    }

    /**
     * @param tubepress_api_translation_TranslatorInterface $translator The translator to use.
     *
     * @return string
     *
     * @api
     * @since 4.2.0
     */
    public function getTranslatedTermForRedirectEndpoint(tubepress_api_translation_TranslatorInterface $translator)
    {
        return $translator->trans('App Callback URL');  //>(translatable)<
    }

    /**
     * @return bool True if this provider uses the client secret, false otherwise.
     *
     * @api
     * @since 4.2.0
     */
    public function isClientSecretUsed()
    {
        return true;
    }

    /**
     * Modify the request to the token endpoint to supply any necessary credentials, add
     * parameters, etc. TubePress will have already added the grant_type and client_id parameters.
     *
     * In the case of "code" grant types, TubePress will have also added the code and redirect_uri parameters.
     *
     * See https://tools.ietf.org/html/rfc6749#section-4.1.3
     * See https://tools.ietf.org/html/rfc6749#section-4.4.2
     *
     * @param tubepress_api_http_message_RequestInterface $request The access token request about to be sent.
     * @param string $clientId The client ID.
     * @param string $clientSecret The client secret.
     *
     * @return void
     *
     * @api
     * @since 4.2.0
     */
    public function onAccessTokenRequest(tubepress_api_http_message_RequestInterface $request,
                                  $clientId,
                                  $clientSecret = null)
    {
        $request->setHeader('Authorization', 'basic ' . base64_encode($clientId . ':' . $clientSecret));
    }
}