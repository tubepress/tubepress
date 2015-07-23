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
class tubepress_http_oauth2_impl_AuthorizationInitiator
{
    /**
     * @var tubepress_spi_http_oauth_v2_Oauth2ProviderInterface[]
     */
    private $_providers;

    /**
     * @var tubepress_api_http_RequestParametersInterface
     */
    private $_requestParams;

    /**
     * @var tubepress_api_http_NonceManagerInterface
     */
    private $_nonceManager;

    /**
     * @var tubepress_http_oauth2_impl_RedirectionEndpointCalculator
     */
    private $_redirectionEndpointCalculator;

    /**
     * @var tubepress_api_template_TemplatingInterface
     */
    private $_templating;

    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var string
     */
    private $_renderedResult;

    public function __construct(tubepress_api_http_NonceManagerInterface                 $nonceManager,
                                tubepress_api_http_RequestParametersInterface            $requestParams,
                                tubepress_http_oauth2_impl_RedirectionEndpointCalculator $redirectEndointCalculator,
                                tubepress_api_template_TemplatingInterface               $templating,
                                tubepress_api_event_EventDispatcherInterface             $eventDispatcher)
    {
        $this->_requestParams                 = $requestParams;
        $this->_nonceManager                  = $nonceManager;
        $this->_redirectionEndpointCalculator = $redirectEndointCalculator;
        $this->_templating                    = $templating;
        $this->_eventDispatcher               = $eventDispatcher;
    }

    /**
     * This function may be called after you have confirmed that the user is authenticated and
     * authorized to initiate new OAuth2 authorization.
     *
     * This function will
     *
     * 1. Check to ensure this was a POST (not GET) HTTP request.
     * 2. Check to ensure that there was a valid nonce supplied with this request.
     * 3. Check to ensure the presence of a request parameter named tubepress_oauth2_provider which is
     *    the name of a loaded OAuth2 provider.
     * 4. Check to ensure the presence of a request parameter named tubepress_oauth2_clientId which is
     *    the client ID that the user should have already registered.
     * 5. Store state in the session for use by the callback.
     * 6. Build the authorization URL and allow the OAuth2 provider to modify it.
     * 7. Assuming all of the above is OK, sends an HTTP 301 to redirect the user to the authorization server.
     */
    public function initiate()
    {
        try {

            $this->_wrappedInitiate();

        } catch (Exception $e) {

            if (!isset($this->_renderedResult)) {

                try {

                    $this->_bail($e->getMessage());

                } catch (Exception $e) {

                    //ignore
                }
            }

            print $this->_renderedResult;
        }
    }

    private function _wrappedInitiate()
    {
        $this->_ensureProvidersAvailable();
        $this->_ensureHttpMethod();
        $this->_ensureValidNonce();

        $provider = $this->_getProvider();
        $clientId = $this->_getClientId();
        $state    = $this->_storeState($provider);
        $url      = $this->_buildUrl($provider, $state, $clientId);

        $this->_redirect($url);

        $this->_renderSuccess($provider, $url);
    }

    private function _ensureProvidersAvailable()
    {
        if (!isset($this->_providers) || count($this->_providers) === 0) {

            throw new RuntimeException('No OAuth2 providers available.');
        }
    }

    private function _ensureHttpMethod()
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {

            $this->_bail('HTTP POST method required.');
        }
    }

    private function _ensureValidNonce()
    {
        if (!$this->_requestParams->hasParam('tubepress_oauth2_nonce')) {

            $this->_bail('Missing tubepress_oauth2_nonce parameter.');
        }

        $nonce = $this->_requestParams->getParamValue('tubepress_oauth2_nonce');

        if (!$this->_nonceManager->isNonceValid($nonce)) {

            $this->_bail('Invalid nonce.');
        }
    }

    /**
     * @return tubepress_spi_http_oauth_v2_Oauth2ProviderInterface
     */
    private function _getProvider()
    {
        if (!$this->_requestParams->hasParam('tubepress_oauth2_provider')) {

            $this->_bail('Missing tubepress_oauth2_provider parameter.');
        }

        $providerName   = $this->_requestParams->getParamValue('tubepress_oauth2_provider');
        $actualProvider = null;

        foreach ($this->_providers as $provider) {

            if ($provider->getName() === $providerName) {

                $actualProvider = $provider;
                break;
            }
        }

        if (!$actualProvider) {

            $this->_bail('No such OAuth2 provider.');
        }

        return $actualProvider;
    }

    private function _getClientId()
    {
        if (!$this->_requestParams->hasParam('tubepress_oauth2_clientId')) {

            $this->_bail('Missing tubepress_oauth2_clientId parameter.');
        }

        return $this->_requestParams->getParamValue('tubepress_oauth2_clientId');
    }

    private function _storeState(tubepress_spi_http_oauth_v2_Oauth2ProviderInterface $provider)
    {
        $sessionStarted = @session_start();

        if (!$sessionStarted) {

            $this->_bail('Unable to start session.');
        }

        $sessionKey            = 'tubepress_oauth2_state_' . $provider->getName();
        $state                 = md5(mt_rand());
        $_SESSION[$sessionKey] = $state;

        return $state;
    }

    private function _buildUrl(tubepress_spi_http_oauth_v2_Oauth2ProviderInterface $provider, $state, $clientId)
    {
        $authorizationUrl = $provider->getAuthorizationEndpoint();

        if (!($authorizationUrl instanceof tubepress_api_url_UrlInterface)) {

            $this->_bail('OAuth2 provider returned a non URL.');
        }

        $type = $provider->getAuthorizationGrantType();

        if ($type !== 'code' && $type !== 'client_credentials') {

            $this->_bail('Unsupported authorization grant type.');
        }

        $query = $authorizationUrl->getQuery();

        $query->set('grant_type', $type);

        if ($type === 'code') {

            $query->set('client_id', $clientId);
            $query->set('state', $state);

            $redirectUrl = $this->_redirectionEndpointCalculator->getRedirectionEndpoint($provider->getName());

            $query->set('redirect_uri', $redirectUrl->toString());
        }

        $provider->onAuthorizationUrl($authorizationUrl);

        $event = $this->_eventDispatcher->newEventInstance($authorizationUrl, array(
            'provider' => $provider
        ));

        $this->_eventDispatcher->dispatch(tubepress_api_event_Events::OAUTH2_URL_AUTHORIZATION, $event);

        $newUrl = $event->getSubject();

        if (!($newUrl instanceof tubepress_api_url_UrlInterface)) {

            $this->_bail('Non authorization URL returned.');
        }

        return $newUrl;
    }

    /**
     * @param tubepress_api_url_UrlInterface $url
     *
     * @return void
     */
    private function _redirect(tubepress_api_url_UrlInterface $url)
    {
        try {

            @header("Location: $url");

        } catch (Exception $e) {

            //ignore
        }
    }

    private function _renderSuccess(tubepress_spi_http_oauth_v2_Oauth2ProviderInterface $provider,
                                    tubepress_api_url_UrlInterface                      $url)
    {
        $out = $this->_templating->renderTemplate('oauth2/authorization/success', array(
            'url'      => $url,
            'provider' => $provider
        ));

        print $out;
    }

    private function _bail($message)
    {
        $this->_renderedResult = $this->_templating->renderTemplate('oauth2/authorization/error', array(
            'message' => $message
        ));

        throw new RuntimeException();
    }

    public function setOauth2Providers(array $providers)
    {
        foreach ($providers as $provider) {

            if (!($provider instanceof tubepress_spi_http_oauth_v2_Oauth2ProviderInterface)) {

                throw new InvalidArgumentException('Non tubepress_spi_http_oauth_v2_Oauth2ProviderInterface in incoming providers.');
            }
        }

        $this->_providers = $providers;
    }
}