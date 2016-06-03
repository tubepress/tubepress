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

class tubepress_http_oauth2_impl_options_ui_TokenDeletionField extends tubepress_options_ui_impl_fields_AbstractField
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
        parent::__construct('oauth2TokenDeletion', $persistence, $requestParams);

        $this->_persistenceHelper = $persistenceHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function onSubmit()
    {
        $requestParams = $this->getHttpRequestParameters();
        $currentValues = $this->getOptionPersistence()->fetch(tubepress_api_options_Names::OAUTH2_TOKENS);
        $toSave        = json_decode($currentValues, true);
        $hasChanges    = false;

        foreach ($this->_oauth2Providers as $oauth2Provider) {

            $providerName = $oauth2Provider->getName();
            $fieldName    = 'oauth2-token-delete-' . $providerName;

            if (!isset($toSave[$providerName])) {

                //no saved tokens for this provider - something fishy
                continue;
            }

            if (!$requestParams->hasParam($fieldName) || !is_array($requestParams->getParamValue($fieldName))) {

                continue;
            }

            $slugsToDelete = $requestParams->getParamValue($fieldName);

            foreach ($slugsToDelete as $slugToDelete) {

                $hasChanges = true;

                unset($toSave[$providerName][$slugToDelete]);
            }

            if (count($toSave[$providerName]) === 0) {

                unset($toSave[$providerName]);
            }
        }

        if ($hasChanges) {

            if (count($toSave) === 0) {

                //force json_encode to save this as {}
                $toSave = new stdClass();
            }

            return $this->sendToStorage(tubepress_api_options_Names::OAUTH2_TOKENS, json_encode($toSave));
        }

        return null;
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
