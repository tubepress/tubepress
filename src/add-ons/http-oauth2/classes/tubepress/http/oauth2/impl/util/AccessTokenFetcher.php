<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_http_oauth2_impl_util_AccessTokenFetcher
{
    /**
     * @var tubepress_api_http_HttpClientInterface
     */
    private $_httpClient;

    /**
     * @var tubepress_http_oauth2_impl_util_PersistenceHelper
     */
    private $_persistenceHelper;

    /**
     * @var tubepress_api_http_oauth2_Oauth2EnvironmentInterface
     */
    private $_oauth2Environment;

    public function __construct(tubepress_api_http_HttpClientInterface               $httpClient,
                                tubepress_http_oauth2_impl_util_PersistenceHelper    $clientHelper,
                                tubepress_api_http_oauth2_Oauth2EnvironmentInterface $oauth2Environment)
    {
        $this->_httpClient        = $httpClient;
        $this->_persistenceHelper = $clientHelper;
        $this->_oauth2Environment = $oauth2Environment;
    }

    /**
     * @param tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider
     * @param $code
     *
     * @return tubepress_api_http_oauth_v2_TokenInterface
     */
    public function fetchWithCodeGrant(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider, $code)
    {
        $tokenUrl    = $provider->getTokenEndpoint();
        $redirectUri = $this->_oauth2Environment->getRedirectionUrl($provider);
        $request     = $this->_httpClient->createRequest('POST', $tokenUrl, array(
            'body' => array(
                'code'         => $code,
                'grant_type'   => 'authorization_code',
                'redirect_uri' => "$redirectUri",
            ),
        ));
        $clientId     = $this->_persistenceHelper->getClientId($provider);
        $clientSecret = $this->_persistenceHelper->getClientSecret($provider);

        $provider->onAccessTokenRequest($request, $clientId, $clientSecret);

        return $this->_fetchAndBuildToken($request, $provider);
    }

    public function fetchWithRefreshToken(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider,
                                          tubepress_api_http_oauth_v2_TokenInterface        $token)
    {
        $tokenUrl     = $provider->getTokenEndpoint();
        $clientId     = $this->_persistenceHelper->getClientId($provider);
        $clientSecret = $this->_persistenceHelper->getClientSecret($provider);
        $request      = $this->_httpClient->createRequest('POST', $tokenUrl, array(
            'body' => array(
                'grant_type'    => 'refresh_token',
                'refresh_token' => $token->getRefreshToken(),
            ),
        ));

        $provider->onRefreshTokenRequest($request, $token, $clientId, $clientSecret);

        return $this->_fetchAndBuildToken($request, $provider);
    }

    public function fetchWithClientCredentials(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        $tokenUrl     = $provider->getTokenEndpoint();
        $clientId     = $this->_persistenceHelper->getClientId($provider);
        $clientSecret = $this->_persistenceHelper->getClientSecret($provider);
        $request      = $this->_httpClient->createRequest('POST', $tokenUrl, array(
            'body' => array(
                'grant_type' => 'client_credentials',
            ),
        ));

        $provider->onAccessTokenRequest($request, $clientId, $clientSecret);

        return $this->_fetchAndBuildToken($request, $provider);
    }

    private function _fetchAndBuildToken(tubepress_api_http_message_RequestInterface         $request,
                                         tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        $response = $this->_httpClient->send($request);

        $this->_checkResponseForError($provider, $response);

        return $this->_buildTokenFromResponse($provider, $response);
    }

    private function _buildTokenFromResponse(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider,
                                             tubepress_api_http_message_ResponseInterface        $response)
    {
        $body    = $response->getBody()->toString();
        $decoded = json_decode($body, true);

        if (!is_array($decoded)) {

            throw new RuntimeException(sprintf('%s returned invalid JSON in their access token response',
                $provider->getDisplayName()
            ));
        }

        if (!isset($decoded['access_token'])) {

            throw new RuntimeException(sprintf('%s did not return an access token in their response',
                $provider->getDisplayName()
            ));
        }

        if ($provider->getAccessTokenType() != '' && !isset($decoded['token_type'])) {

            throw new RuntimeException(sprintf('%s did not return a token type in their response',
                $provider->getDisplayName()
            ));
        }

        $tokenType = $decoded['token_type'];

        if ($tokenType !== $provider->getAccessTokenType()) {

            throw new RuntimeException(sprintf('%s should have returned a token type of %s but instead returned %s',
                $provider->getDisplayName(),
                $provider->getAccessTokenType(),
                $tokenType
            ));
        }

        $toReturn = new tubepress_http_oauth2_impl_token_Token();
        $toReturn->setAccessToken($decoded['access_token']);

        if (isset($decoded['expires_in'])) {

            $toReturn->setLifetimeInSeconds(intval($decoded['expires_in']));
        }

        if (isset($decoded['refresh_token'])) {

            $toReturn->setRefreshToken($decoded['refresh_token']);
        }

        $keysToFilter = array('access_token', 'expires_in', 'refresh_token');
        $extraParams  = array_diff_key($decoded, array_flip($keysToFilter));

        $toReturn->setExtraParams($extraParams);

        return $toReturn;
    }

    private function _checkResponseForError(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider,
                                            tubepress_api_http_message_ResponseInterface $response)
    {
        if (intval($response->getStatusCode()) === 200) {

            return;
        }

        if (intval($response->getStatusCode()) === 400) {

            $body    = $response->getBody()->toString();
            $decoded = json_decode($body, true);

            if (is_array($decoded) && isset($decoded['error'])) {

                throw new RuntimeException(sprintf('%s responded with an HTTP 400: %s',
                    $provider->getDisplayName(),
                    $decoded['error']
                ));
            }
        }

        throw new RuntimeException(sprintf('%s responded with an HTTP %s: %s',
            $provider->getDisplayName(),
            $response->getStatusCode(),
            $response->getBody()->toString()
        ));
    }
}
