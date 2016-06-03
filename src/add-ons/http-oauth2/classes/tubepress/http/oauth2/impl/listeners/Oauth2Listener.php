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

class tubepress_http_oauth2_impl_listeners_Oauth2Listener extends tubepress_http_oauth2_impl_AbstractProviderConsumer
{
    /**
     * @var tubepress_http_oauth2_impl_util_PersistenceHelper
     */
    private $_persistenceHelper;

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    /**
     * @var tubepress_http_oauth2_impl_util_AccessTokenFetcher
     */
    private $_accessTokenFetcher;

    /**
     * @var tubepress_api_options_PersistenceInterface
     */
    private $_persistence;

    public function __construct(tubepress_api_log_LoggerInterface                  $logger,
                                tubepress_api_options_PersistenceInterface         $persistence,
                                tubepress_http_oauth2_impl_util_PersistenceHelper  $persistenceHelper,
                                tubepress_http_oauth2_impl_util_AccessTokenFetcher $accessTokenFetcher)
    {
        $this->_persistenceHelper  = $persistenceHelper;
        $this->_logger             = $logger;
        $this->_shouldLog          = $logger->isEnabled();
        $this->_accessTokenFetcher = $accessTokenFetcher;
        $this->_persistence        = $persistence;
    }

    public function onAcceptableValues(tubepress_api_event_EventInterface $event)
    {
        $tokens  = $this->_persistence->fetch(tubepress_api_options_Names::OAUTH2_TOKENS);
        $decoded = json_decode($tokens, true);
        $toSet   = is_array($decoded) ? array_keys($decoded) : array();

        $event->setSubject($toSet);
    }

    public function onHttpRequest(tubepress_api_event_EventInterface $event)
    {
        /**
         * @var tubepress_api_http_message_RequestInterface
         */
        $request       = $event->getSubject();
        $providers     = $this->getAllProviders();
        $requestConfig = $request->getConfig();

        if (!array_key_exists('tubepress-remote-api-call', $requestConfig)) {

            return;
        }

        if ($requestConfig['tubepress-remote-api-call'] !== true) {

            return;
        }

        if ($this->_shouldLog) {

            $this->_logDebug(sprintf('OAuth2 signing listener invoked for <code>%s</code> to <code>%s</code> with <code>%d</code> registered OAuth2 provider(s)',
                $request->getMethod(),
                $request->getUrl(),
                count($providers)
            ));
        }

        foreach ($providers as $provider) {

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('Seeing if %s wants to authorize <code>%s</code> to <code>%s</code>',
                    $provider->getDisplayName(),
                    $request->getMethod(),
                    $request->getUrl()
                ));
            }

            if (!$provider->wantsToAuthorizeRequest($request)) {

                if ($this->_shouldLog) {

                    $this->_logDebug(sprintf('%s declined to authorize <code>%s</code> to <code>%s</code>',
                        $provider->getDisplayName(),
                        $request->getMethod(),
                        $request->getUrl()
                    ));
                }

                continue;
            }

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('%s wants to authorize <code>%s</code> to <code>%s</code>',
                    $provider->getDisplayName(),
                    $request->getMethod(),
                    $request->getUrl()
                ));
            }

            $token = $this->_persistenceHelper->getStoredToken($provider);

            if (!$token) {

                if ($this->_shouldLog) {

                    $this->_logDebug(sprintf('No saved token for %s to use, or user requested no signing.',
                        $provider->getDisplayName()
                    ));
                }

                break;
            }

            if ($token->isExpired()) {

                if ($this->_shouldLog) {

                    $this->_logDebug('Existing token has expired.');
                }

                if (!$token->getRefreshToken()) {

                    if ($this->_shouldLog) {

                        $this->_logDebug(sprintf('Token for %s has expired and no refresh token available.',
                            $provider->getDisplayName()
                        ));
                    }

                    break;
                }

                if ($this->_shouldLog) {

                    $this->_logDebug(sprintf('Token for %s has expired. We will try to refresh it.',
                        $provider->getDisplayName()
                    ));
                }

                $oldToken = $token;
                $newToken = $this->_accessTokenFetcher->fetchWithRefreshToken($provider, $token);

                if (!$newToken) {

                    if ($this->_shouldLog) {

                        $this->_logDebug(sprintf('Unable to refresh token for %s. Boo.',
                            $provider->getDisplayName()
                        ));
                    }

                    break;
                }

                if ($this->_shouldLog) {

                    $this->_logDebug(sprintf('Successfully refreshed token for %s. Yay.',
                        $provider->getDisplayName()
                    ));
                }

                $token = $newToken;

                $this->_persistenceHelper->updateToken($oldToken, $newToken);
            }

            $clientId     = $this->_persistenceHelper->getClientId($provider);
            $clientSecret = $this->_persistenceHelper->getClientSecret($provider);

            if ($token && $clientId) {

                $provider->authorizeRequest($request, $token, $clientId, $clientSecret);
                break;
            }
        }
    }

    private function _logDebug($msg)
    {
        $this->_logger->debug(sprintf('(OAuth2 Listener) %s', $msg));
    }
}
