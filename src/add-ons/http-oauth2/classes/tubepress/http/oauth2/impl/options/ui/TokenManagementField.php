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
     * @var tubepress_spi_http_oauth2_Oauth2UrlProviderInterface
     */
    private $_oauth2UrlProvider;

    public function __construct(tubepress_spi_http_oauth2_Oauth2ProviderInterface    $provider,
                                tubepress_api_options_PersistenceInterface             $persistence,
                                tubepress_api_http_RequestParametersInterface          $requestParams,
                                tubepress_api_template_TemplatingInterface             $templating,
                                tubepress_http_oauth2_impl_util_PersistenceHelper      $persistenceHelper,
                                tubepress_spi_http_oauth2_Oauth2UrlProviderInterface $oauth2UrlProvider)
    {
        parent::__construct('tokenManagement_' . $provider->getName(), $persistence, $requestParams, $templating, 'Accounts');

        $this->_persistenceHelper = $persistenceHelper;
        $this->_provider          = $provider;
        $this->_oauth2UrlProvider = $oauth2UrlProvider;
    }

    /**
     * @return string The absolute path to the template for this field.
     */
    protected function getTemplateName()
    {
        return 'options-ui/fields/oauth2/token-management';
    }

    /**
     * @return array An associative array of template variables for this field.
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
            'clientId'     => $clientId,
            'clientSecret' => $clientSecret,
            'provider'     => $this->_provider,
            'callbackUri'  => $this->_oauth2UrlProvider->getRedirectionUrl($this->_provider),
            'slugs'        => $slugs,
        );
    }

    /**
     * Invoked when the element is submitted by the user.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     *
     * @api
     * @since 4.0.0
     */
    public function onSubmit()
    {
        return null;
    }

    /**
     * Gets whether or not this field is TubePress Pro only.
     *
     * @return boolean True if this field is TubePress Pro only. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isProOnly()
    {
        return false;
    }
}