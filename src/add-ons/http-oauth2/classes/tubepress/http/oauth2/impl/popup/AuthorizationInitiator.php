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

class tubepress_http_oauth2_impl_popup_AuthorizationInitiator extends tubepress_http_oauth2_impl_popup_AbstractPopupHandler
{
    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_api_http_RequestParametersInterface        $requestParams,
                                tubepress_api_template_TemplatingInterface           $templating,
                                tubepress_api_url_UrlFactoryInterface                $urlFactory,
                                tubepress_http_oauth2_impl_util_PersistenceHelper    $persistenceHelper,
                                tubepress_http_oauth2_impl_util_AccessTokenFetcher   $accessTokenFetcher,
                                tubepress_api_http_oauth2_Oauth2EnvironmentInterface $oauth2Environment,
                                tubepress_api_event_EventDispatcherInterface         $eventDispatcher)
    {
        parent::__construct($requestParams, $templating, $urlFactory, $persistenceHelper, $accessTokenFetcher, $oauth2Environment);

        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * This function may be called after you have confirmed that the user is authenticated and
     * authorized to initiate a new OAuth2 authorization.
     *
     * 1. Check to ensure that there was a valid nonce supplied with this request.
     * 2. Check to ensure the presence of a request parameter named provider which is
     *    the name of a loaded OAuth2 provider.
     * 3. If clientId and (optionally) clientSecret are given in the query, save them to the DB.
     *
     * For "code" authorization grant types, this method will:
     *
     * 1. Store state in the session for use by the callback.
     * 2. Build the authorization URL and allow the OAuth2 provider to modify it.
     * 3. Assuming all of the above is OK, sends an HTTP 301 to redirect the user to the authorization server.
     *
     * For "client_credentials" authorization grant types, this method will:
     *
     * 1. Fetch an access token from the provider.
     * 2. Store the access token.
     * 3. Render a success page.
     */
    protected function execute()
    {
        $provider  = $this->_getProvider();
        $grantType = $provider->getAuthorizationGrantType();

        $this->_saveClientIdAndSecretIfPresent($provider);

        switch ($grantType) {

            case 'code':

                $this->_executeCodeGrantType($provider);
                break;

            case 'client_credentials':

                $this->_executeClientCredentialsGrantType($provider);
                break;

            default:

                throw new InvalidArgumentException('Unsupported authorization grant type.');
        }
    }

    private function _saveClientIdAndSecretIfPresent(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        $requestParams = $this->getRequestParams();

        if (!$requestParams->hasParam('clientId')) {

            return;
        }

        $clientId = $requestParams->getParamValue('clientId');

        if (!$requestParams->hasParam('clientSecret')) {

            $clientSecret = null;

        } else {

            $clientSecret = $requestParams->getParamValue('clientSecret');
        }

        $this->getPersistenceHelper()->saveClientIdAndSecret($provider, $clientId, $clientSecret);
    }

    private function _executeCodeGrantType(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        $state = $this->saveState($provider);
        $url   = $this->_buildUrl($provider, $state);

        $this->_redirect($url);

        $this->renderSuccess('start', 'Redirecting to %s', $provider, array(
            'url' => $url,
        ));
    }

    private function _executeClientCredentialsGrantType(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        $token = $this->getAccessTokenFetcher()->fetchWithClientCredentials($provider);
        $slug  = $provider->getSlugForToken($token);

        $this->getPersistenceHelper()->saveToken($provider, $slug, $token);

        $this->renderSuccess('finish', 'Successfully connected to %s', $provider, array(
            'slug' => $slug,
        ));
    }

    /**
     * @return tubepress_spi_http_oauth2_Oauth2ProviderInterface
     */
    private function _getProvider()
    {
        $providerName = $this->getRequestParams()->getParamValue('provider');

        return $this->getProviderByName($providerName);
    }

    private function _buildUrl(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider, $state)
    {
        $authorizationUrl = $provider->getAuthorizationEndpoint();

        if (!($authorizationUrl instanceof tubepress_api_url_UrlInterface)) {

            $this->bail('OAuth2 provider returned a non URL.');
        }

        $clientId     = $this->getPersistenceHelper()->getClientId($provider);
        $clientSecret = $this->getPersistenceHelper()->getClientSecret($provider);
        $query        = $authorizationUrl->getQuery();
        $redirectUrl  = $this->getOauth2Environment()->getRedirectionUrl($provider);

        $query->set('response_type', 'code')
              ->set('client_id', $clientId)
              ->set('state', $state)
              ->set('redirect_uri', $redirectUrl->toString());

        $provider->onAuthorizationUrl($authorizationUrl, $clientId, $clientSecret);

        $event = $this->_eventDispatcher->newEventInstance($authorizationUrl, array(
            'provider' => $provider,
        ));

        $this->_eventDispatcher->dispatch(tubepress_api_event_Events::OAUTH2_URL_AUTHORIZATION, $event);

        $authorizationUrl = $event->getSubject();

        if (!($authorizationUrl instanceof tubepress_api_url_UrlInterface)) {

            $this->bail('Non authorization URL returned.');
        }

        return $authorizationUrl;
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
        return array();
    }
}
