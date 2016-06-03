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

class tubepress_http_oauth2_impl_options_ui_TokenManagementField extends tubepress_options_ui_impl_fields_templated_AbstractTemplatedField
{
    /**
     * @var tubepress_http_oauth2_impl_util_PersistenceHelper
     */
    private $_persistenceHelper;

    /**
     * @var tubepress_spi_http_oauth2_Oauth2ProviderInterface
     */
    private $_provider;

    /**
     * @var tubepress_api_http_oauth2_Oauth2EnvironmentInterface
     */
    private $_oauth2Environment;

    public function __construct(tubepress_spi_http_oauth2_Oauth2ProviderInterface    $provider,
                                tubepress_api_options_PersistenceInterface           $persistence,
                                tubepress_api_http_RequestParametersInterface        $requestParams,
                                tubepress_api_template_TemplatingInterface           $templating,
                                tubepress_http_oauth2_impl_util_PersistenceHelper    $persistenceHelper,
                                tubepress_api_http_oauth2_Oauth2EnvironmentInterface $oauth2Environment)
    {
        parent::__construct('tokenManagement_' . $provider->getName(), $persistence, $requestParams, $templating, 'API Tokens');

        $this->_persistenceHelper = $persistenceHelper;
        $this->_provider          = $provider;
        $this->_oauth2Environment = $oauth2Environment;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateName()
    {
        return 'options-ui/fields/oauth2/token-management';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateVariables()
    {
        $clientId      = $this->_persistenceHelper->getClientId($this->_provider);
        $clientSecret  = $this->_persistenceHelper->getClientSecret($this->_provider);
        $tokens        = $this->getOptionPersistence()->fetch(tubepress_api_options_Names::OAUTH2_TOKENS);
        $decodedTokens = json_decode($tokens, true);
        $providerName  = $this->_provider->getName();

        if (!isset($decodedTokens[$providerName]) || !is_array($decodedTokens[$providerName])) {

            $slugs = array();

        } else {

            $slugs = array_keys($decodedTokens[$providerName]);
        }

        return array(
            'clientId'       => $clientId,
            'clientSecret'   => $clientSecret,
            'provider'       => $this->_provider,
            'oauth2StartUrl' => $this->_oauth2Environment->getAuthorizationInitiationUrl($this->_provider),
            'slugs'          => $slugs,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function onSubmit()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isProOnly()
    {
        return false;
    }
}
