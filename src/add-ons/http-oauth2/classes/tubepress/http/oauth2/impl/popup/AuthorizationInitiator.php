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
class tubepress_http_oauth2_impl_popup_AuthorizationInitiator extends tubepress_http_oauth2_impl_popup_AbstractPopupHandler
{
    /**
     * @var tubepress_api_http_NonceManagerInterface
     */
    private $_nonceManager;

    /**
     * @var tubepress_http_oauth2_impl_util_RedirectionEndpointCalculator
     */
    private $_redirectionEndpointCalculator;

    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_api_http_NonceManagerInterface                      $nonceManager,
                                tubepress_api_http_RequestParametersInterface                 $requestParams,
                                tubepress_http_oauth2_impl_util_RedirectionEndpointCalculator $redirectEndointCalculator,
                                tubepress_api_template_TemplatingInterface                    $templating,
                                tubepress_api_event_EventDispatcherInterface                  $eventDispatcher,
                                tubepress_http_oauth2_impl_util_PersistenceHelper             $persistenceHelper,
                                tubepress_api_url_UrlFactoryInterface                         $urlFactory)
    {
        parent::__construct($requestParams, $templating, $urlFactory, $persistenceHelper);

        $this->_nonceManager                  = $nonceManager;
        $this->_redirectionEndpointCalculator = $redirectEndointCalculator;
        $this->_eventDispatcher               = $eventDispatcher;
    }

    /**
     * This function may be called after you have confirmed that the user is authenticated and
     * authorized to initiate a new OAuth2 authorization.
     *
     * This function will
     *
     * 1. Check to ensure that there was a valid nonce supplied with this request.
     * 2. Check to ensure the presence of a request parameter named tubepress_oauth2_provider which is
     *    the name of a loaded OAuth2 provider.
     * 3. Store state in the session for use by the callback.
     * 4. Build the authorization URL and allow the OAuth2 provider to modify it.
     * 5. Assuming all of the above is OK, sends an HTTP 301 to redirect the user to the authorization server.
     */
    public function execute()
    {
        $this->_ensureValidNonce();

        $provider = $this->_getProvider();
        $state    = $this->saveState($provider);
        $url      = $this->_buildUrl($provider, $state);

        $this->_redirect($url);

        $this->renderSuccess('startAuthorization', 'Redirecting to %s', $provider, array(
            'url' => $url
        ));
    }

    private function _ensureValidNonce()
    {
        $nonce = $this->getRequestParams()->getParamValue('tubepress_oauth2_nonce');

        if (!$this->_nonceManager->isNonceValid($nonce)) {

            $this->bail('Invalid nonce.');
        }
    }

    /**
     * @return tubepress_spi_http_oauth_v2_Oauth2ProviderInterface
     */
    private function _getProvider()
    {
        $providerName = $this->getRequestParams()->getParamValue('tubepress_oauth2_provider');

        return $this->getProviderByName($providerName);
    }

    private function _buildUrl(tubepress_spi_http_oauth_v2_Oauth2ProviderInterface $provider, $state)
    {
        $authorizationUrl = $provider->getAuthorizationEndpoint();

        if (!($authorizationUrl instanceof tubepress_api_url_UrlInterface)) {

            $this->bail('OAuth2 provider returned a non URL.');
        }

        $type = $provider->getAuthorizationGrantType();

        if ($type !== 'code' && $type !== 'client_credentials') {

            $this->bail('Unsupported authorization grant type.');
        }

        $query = $authorizationUrl->getQuery();

        $query->set('grant_type', $type);

        if ($type === 'code') {

            $query->set('state', $state);

            $redirectUrl = $this->_redirectionEndpointCalculator->getRedirectionEndpoint($provider->getName());

            $query->set('redirect_uri', $redirectUrl->toString());
        }

        $clientId     = $this->getPersistenceHelper()->getClientId($provider);
        $clientSecret = $this->getPersistenceHelper()->getClientSecret($provider);

        $provider->onAuthorizationUrl($authorizationUrl, $clientId, $clientSecret);

        $event = $this->_eventDispatcher->newEventInstance($authorizationUrl, array(
            'provider' => $provider
        ));

        $this->_eventDispatcher->dispatch(tubepress_api_event_Events::OAUTH2_URL_AUTHORIZATION, $event);

        $newUrl = $event->getSubject();

        if (!($newUrl instanceof tubepress_api_url_UrlInterface)) {

            $this->bail('Non authorization URL returned.');
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

            @header('HTTP/1.1 302 Moved Temporarily');
            @header("Location: $url");

        } catch (Exception $e) {

            //ignore
        }
    }

    /**
     * @return string[]
     */
    protected function getRequiredParamNames()
    {
        return array(
            'tubepress_oauth2_nonce',
            'tubepress_oauth2_provider'
        );
    }
}