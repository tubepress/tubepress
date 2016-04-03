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

class tubepress_http_oauth2_impl_util_PersistenceHelper
{
    private static $_ACCESS_TOKEN  = 'access_token';
    private static $_REFRESH_TOKEN = 'refresh_token';
    private static $_EXPIRY_UNIX   = 'expiry_unix';
    private static $_EXTRA         = 'extra';
    private static $_ID            = 'id';
    private static $_SECRET        = 'secret';

    /**
     * @var tubepress_api_options_PersistenceInterface
     */
    private $_persistence;

    /**
     * @var tubepress_api_array_ArrayReaderInterface
     */
    private $_arrayReader;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_api_options_PersistenceInterface $persistence,
                                tubepress_api_array_ArrayReaderInterface   $arrayReader,
                                tubepress_api_options_ContextInterface     $context)
    {
        $this->_persistence = $persistence;
        $this->_arrayReader = $arrayReader;
        $this->_context     = $context;
    }

    public function saveClientIdAndSecret(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider, $clientId, $clientSecret)
    {
        $clients        = $this->_persistence->fetch(tubepress_api_options_Names::OAUTH2_CLIENT_DETAILS);
        $decodedClients = json_decode($clients, true);
        $providerName   = $provider->getName();

        if (!isset($decodedClients[$providerName])) {

            $decodedClients[$providerName] = array();
        }

        if ($clientId) {

            $decodedClients[$providerName][self::$_ID] = $clientId;
        }

        if ($clientSecret) {

            $decodedClients[$providerName][self::$_SECRET] = $clientSecret;
        }

        $encodedClients = json_encode($decodedClients);

        $this->_persistence->queueForSave(tubepress_api_options_Names::OAUTH2_CLIENT_DETAILS, $encodedClients);
        $this->_persistence->flushSaveQueue();
    }

    public function getClientId(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        return $this->_getInfo($provider, self::$_ID);
    }

    public function getClientSecret(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        return $this->_getInfo($provider, self::$_SECRET);
    }

    public function getStoredToken(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        $requestedSlug = $this->_context->get(tubepress_api_options_Names::OAUTH2_TOKEN);
        $tokens        = $this->_context->get(tubepress_api_options_Names::OAUTH2_TOKENS);
        $decoded       = json_decode($tokens, true);
        $providerName  = $provider->getName();

        if (!isset($decoded[$providerName])) {

            return null;
        }

        $providerTokens = $decoded[$providerName];
        $explicitSlug   = $requestedSlug != '';

        if (!is_array($providerTokens)) {

            return null;
        }

        if ($explicitSlug) {

            if ($explicitSlug === 'none' || !isset($providerTokens[$requestedSlug])) {

                return null;
            }

        } else {

            if (count($providerTokens) === 0) {

                return null;
            }

            $slugs         = array_keys($providerTokens);
            $requestedSlug = $slugs[0];
        }

        $tokenData    = $providerTokens[$requestedSlug];
        $accessToken  = $tokenData[self::$_ACCESS_TOKEN];
        $refreshToken = isset($tokenData[self::$_REFRESH_TOKEN]) ? $tokenData[self::$_REFRESH_TOKEN] : null;
        $expiry       = intval($tokenData[self::$_EXPIRY_UNIX]);
        $extra        = $tokenData[self::$_EXTRA];

        $toReturn = new tubepress_http_oauth2_impl_token_Token();
        $toReturn->setAccessToken($accessToken);
        $toReturn->setExtraParams($extra);
        $toReturn->setEndOfLifeUnixTime($expiry);

        if ($refreshToken) {

            $toReturn->setRefreshToken($refreshToken);
        }

        return $toReturn;
    }

    public function updateToken(tubepress_api_http_oauth_v2_TokenInterface $oldToken,
                                tubepress_api_http_oauth_v2_TokenInterface $newToken)
    {

    }

    public function saveToken(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider,
                              $slug,
                              tubepress_api_http_oauth_v2_TokenInterface          $token)
    {
        $tokens       = $this->_context->get(tubepress_api_options_Names::OAUTH2_TOKENS);
        $decoded      = json_decode($tokens, true);
        $providerName = $provider->getName();

        if (!isset($decoded[$providerName])) {

            $decoded[$providerName] = array();
        }

        $providerTokens = $decoded[$providerName];
        $toSave         = array(
            self::$_ACCESS_TOKEN => $token->getAccessToken(),
            self::$_EXPIRY_UNIX  => $token->getEndOfLifeUnixTime(),
            self::$_EXTRA        => $token->getExtraParams(),
        );

        if ($token->getRefreshToken()) {

            $toSave[self::$_REFRESH_TOKEN] = $token->getRefreshToken();
        }

        $providerTokens[$slug]  = $toSave;
        $decoded[$providerName] = $providerTokens;

        $this->_persistence->queueForSave(tubepress_api_options_Names::OAUTH2_TOKENS, json_encode($decoded));
        $this->_persistence->flushSaveQueue();
    }

    private function _getInfo(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider, $dataPoint)
    {
        $clients        = $this->_persistence->fetch(tubepress_api_options_Names::OAUTH2_CLIENT_DETAILS);
        $decodedClients = json_decode($clients, true);
        $providerName   = $provider->getName();
        $path           = $providerName . '.' . $dataPoint;

        return $this->_arrayReader->getAsString($decodedClients, $path, null);
    }
}
