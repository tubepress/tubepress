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

abstract class tubepress_http_oauth2_impl_popup_AbstractPopupHandler extends tubepress_http_oauth2_impl_AbstractProviderConsumer
{
    /**
     * @var tubepress_api_http_RequestParametersInterface
     */
    private $_requestParams;

    /**
     * @var tubepress_api_template_TemplatingInterface
     */
    private $_templating;

    /**
     * @var tubepress_http_oauth2_impl_util_PersistenceHelper
     */
    private $_persistenceHelper;

    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var string
     */
    private $_renderedResult;

    /**
     * @var tubepress_http_oauth2_impl_util_AccessTokenFetcher
     */
    private $_accessTokenFetcher;

    /**
     * @var tubepress_api_http_oauth2_Oauth2EnvironmentInterface
     */
    private $_oauth2Environment;

    public function __construct(tubepress_api_http_RequestParametersInterface        $requestParams,
                                tubepress_api_template_TemplatingInterface           $templating,
                                tubepress_api_url_UrlFactoryInterface                $urlFactory,
                                tubepress_http_oauth2_impl_util_PersistenceHelper    $persistenceHelper,
                                tubepress_http_oauth2_impl_util_AccessTokenFetcher   $accessTokenFetcher,
                                tubepress_api_http_oauth2_Oauth2EnvironmentInterface $oauth2Environment)
    {
        $this->_requestParams      = $requestParams;
        $this->_templating         = $templating;
        $this->_urlFactory         = $urlFactory;
        $this->_persistenceHelper  = $persistenceHelper;
        $this->_accessTokenFetcher = $accessTokenFetcher;
        $this->_oauth2Environment  = $oauth2Environment;
    }

    /**
     * This function may be called after you have confirmed that the user is authenticated and
     * authorized to initiate new OAuth2 authorization. Basically you want to make sure that the user
     * is logged in and that they are permitted to manage OAuth2 configuration.
     *
     * This function will
     *
     * 1. Ensure we have OAuth2 providers available to work with.
     * 2. Ensure the presence of any required HTTP params.
     * 3. Pass off execution to the child.
     */
    public function initiate()
    {
        try {

            $this->ensureProvidersAvailable();
            $this->_ensureRequiredParamsPresent();
            $this->_validateCsrfToken();
            $this->execute();

        } catch (Exception $e) {

            if (!isset($this->_renderedResult)) {

                try {

                    $this->bail($e->getMessage());

                } catch (Exception $e) {

                    //quietly ignore
                }
            }

            echo $this->_renderedResult;
        }
    }

    /**
     * @return void
     */
    protected abstract function execute();

    /**
     * @return string[]
     */
    protected abstract function getRequiredParamNames();

    /**
     * {@inheritdoc}
     */
    protected function getProviderByName($providerName)
    {
        $provider = parent::getProviderByName($providerName);
        $clientId = $this->_persistenceHelper->getClientId($provider);

        if (!$clientId) {

            throw new RuntimeException(sprintf('No saved client ID for %s', $provider->getDisplayName()));
        }

        $clientSecret = $this->_persistenceHelper->getClientSecret($provider);

        if ($provider->isClientSecretUsed() && !$clientSecret) {

            throw new RuntimeException(sprintf('%s does not have a client secret', $provider->getDisplayName()));
        }

        return $provider;
    }

    protected function saveState(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        $sessionKey            = $this->_getSessionKey($provider);
        $state                 = md5(mt_rand());
        $_SESSION[$sessionKey] = $state;

        return $state;
    }

    protected function validateState(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        if (!$provider->isStateUsed()) {

            return;
        }

        $currentUrl        = $this->_urlFactory->fromCurrent();
        $stateFromProvider = $currentUrl->getQuery()->get('state');

        if (!$stateFromProvider) {

            $this->bail(sprintf('%s did not supply state. Possible replay attack.',
                $provider->getDisplayName()
            ));
        }

        $sessionKey = $this->_getSessionKey($provider);

        if (!isset($_SESSION[$sessionKey])) {

            $this->bail(sprintf('No stored state for %s. Try again.',
                $provider->getDisplayName()
            ));
        }

        if ($_SESSION[$sessionKey] !== $stateFromProvider) {

            $this->bail(sprintf('State from %s did not match our saved state. Possible reply attack. Please try again.',
                $provider->getDisplayName()
            ));
        }

        return;
    }

    protected function clearState(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        $sessionKey = $this->_getSessionKey($provider);

        unset($_SESSION[$sessionKey]);
    }

    protected function renderSuccess($templateName,
                                     $titleFormat,
                                     tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider,
                                     array $templateVars)
    {
        $vars      = array('provider' => $provider, 'titleFormat' => $titleFormat);
        $finalVars = array_merge($vars, $templateVars);
        $out       = $this->_templating->renderTemplate("oauth2/$templateName", $finalVars);

        echo $out;
    }

    protected function bail($message)
    {
        $this->_renderedResult = $this->_templating->renderTemplate('oauth2/error', array(
            'message' => $message,
        ));

        throw new RuntimeException();
    }

    /**
     * @return tubepress_api_http_RequestParametersInterface
     */
    protected function getRequestParams()
    {
        return $this->_requestParams;
    }

    /**
     * @return tubepress_http_oauth2_impl_util_PersistenceHelper
     */
    protected function getPersistenceHelper()
    {
        return $this->_persistenceHelper;
    }

    /**
     * @return tubepress_http_oauth2_impl_util_AccessTokenFetcher
     */
    protected function getAccessTokenFetcher()
    {
        return $this->_accessTokenFetcher;
    }

    /**
     * @return tubepress_api_http_oauth2_Oauth2EnvironmentInterface
     */
    protected function getOauth2Environment()
    {
        return $this->_oauth2Environment;
    }

    private function _ensureRequiredParamsPresent()
    {
        $required = array_merge(
            $this->getRequiredParamNames(),
            array('csrf_token', 'provider')
        );

        foreach ($required as $key) {

            if (!$this->_requestParams->hasParam($key)) {

                $this->bail(sprintf('Missing %s parameter.', $key));
            }
        }
    }

    private function _getSessionKey(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        if (version_compare(phpversion(), '5.4.0', '>=')) {

            $sessionStatus = session_status();
            $sessionActive = $sessionStatus === PHP_SESSION_ACTIVE;

        } else {

            $sessionActive = session_id() === '';
        }

        if (!$sessionActive) {

            $sessionStarted = @session_start();

            if (!$sessionStarted) {

                $this->bail('Unable to start session.');
            }
        }

        return 'tubepress_oauth2_state_' . $provider->getName();
    }

    private function _validateCsrfToken()
    {
        $actualToken   = (string) $this->getRequestParams()->getParamValue('csrf_token');
        $expectedToken = (string) $this->_oauth2Environment->getCsrfSecret();

        if ($actualToken !== $expectedToken) {

            $this->bail('Invalid csrf_token. Possible CSRF attack.');
        }
    }
}
