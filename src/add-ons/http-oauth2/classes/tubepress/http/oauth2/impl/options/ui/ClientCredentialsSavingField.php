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

class tubepress_http_oauth2_impl_options_ui_ClientCredentialsSavingField extends tubepress_options_ui_impl_fields_AbstractField
{
    /**
     * @var tubepress_http_oauth2_impl_util_PersistenceHelper
     */
    private $_persistenceHelper;

    /**
     * @var tubepress_spi_http_oauth2_Oauth2ProviderInterface[]
     */
    private $_oauth2Providers = array();

    public function __construct(tubepress_api_options_PersistenceInterface        $persistence,
                                tubepress_api_http_RequestParametersInterface     $requestParams,
                                tubepress_http_oauth2_impl_util_PersistenceHelper $persistenceHelper)
    {
        parent::__construct('oauth2ClientCredentials', $persistence, $requestParams);

        $this->_persistenceHelper = $persistenceHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function onSubmit()
    {
        $requestParams = $this->getHttpRequestParameters();
        $currentValues = $this->getOptionPersistence()->fetch(tubepress_api_options_Names::OAUTH2_CLIENT_DETAILS);
        $toSave        = json_decode($currentValues, true);

        foreach ($this->_oauth2Providers as $oauth2Provider) {

            $providerName        = $oauth2Provider->getName();
            $clientIdFieldId     = 'clientId_' . $providerName;
            $clientSecretFieldId = 'clientSecret_' . $providerName;

            if (!$requestParams->hasParam($clientIdFieldId) && !$requestParams->hasParam($clientSecretFieldId)) {

                continue;
            }

            if (!isset($toSave[$providerName]) || !is_array($toSave[$providerName])) {

                $toSave[$providerName] = array();
            }

            if ($requestParams->hasParam($clientIdFieldId)) {

                $toSave[$providerName]['id'] = $requestParams->getParamValue($clientIdFieldId);
            }

            if ($oauth2Provider->isClientSecretUsed() && $requestParams->hasParam($clientSecretFieldId)) {

                $toSave[$providerName]['secret'] = $requestParams->getParamValue($clientSecretFieldId);
            }
        }

        return $this->sendToStorage(tubepress_api_options_Names::OAUTH2_CLIENT_DETAILS, json_encode($toSave));
    }

    /**
     * {@inheritdoc}
     */
    public function isProOnly()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetHTML()
    {
        return '';
    }

    public function setOauth2Providers(array $providers)
    {
        $this->_oauth2Providers = $providers;
    }
}
