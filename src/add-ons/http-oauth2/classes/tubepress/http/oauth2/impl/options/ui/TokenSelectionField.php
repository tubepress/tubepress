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
class tubepress_http_oauth2_impl_options_ui_TokenSelectionField extends tubepress_options_ui_impl_fields_templated_AbstractTemplatedField implements tubepress_api_options_ui_MultiSourceFieldInterface
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

    /**
     * @var string
     */
    private $_multiSourcePrefix = '';

    public function __construct(tubepress_spi_http_oauth2_Oauth2ProviderInterface    $provider,
                                tubepress_api_options_PersistenceInterface           $persistence,
                                tubepress_api_http_RequestParametersInterface        $requestParams,
                                tubepress_api_template_TemplatingInterface           $templating,
                                tubepress_http_oauth2_impl_util_PersistenceHelper    $persistenceHelper,
                                tubepress_api_http_oauth2_Oauth2EnvironmentInterface $oauth2Environment)
    {
        parent::__construct('tokenSelection_' . $provider->getName(), $persistence, $requestParams, $templating, 'API Token');

        $this->_persistenceHelper = $persistenceHelper;
        $this->_provider          = $provider;
        $this->_oauth2Environment = $oauth2Environment;
    }

    /**
     * @return string The page-unique identifier for this item.
     *
     * @api
     * @since 4.0.0
     */
    public function getId()
    {
        return $this->_multiSourcePrefix . parent::getId();
    }

    private function setMultiSourcePrefix($prefix)
    {
        $this->_multiSourcePrefix = $prefix;
    }

    /**
     * @return string The absolute path to the template for this field.
     */
    protected function getTemplateName()
    {
        return 'options-ui/fields/oauth2/token-selection';
    }

    /**
     * @return array An associative array of template variables for this field.
     */
    protected function getTemplateVariables()
    {
        $tokens        = $this->getOptionPersistence()->fetch(tubepress_api_options_Names::OAUTH2_TOKENS);
        $selected      = $this->getOptionPersistence()->fetch(tubepress_api_options_Names::OAUTH2_TOKEN);
        $decodedTokens = json_decode($tokens, true);
        $providerName  = $this->_provider->getName();

        if (!isset($decodedTokens[$providerName]) || !is_array($decodedTokens[$providerName])) {

            $slugs = array();

        } else {

            $slugs = array_keys($decodedTokens[$providerName]);
            $slugs = array_combine($slugs, $slugs);
        }

        return array(
            'ungroupedChoices' => $slugs,
            'value'            => $selected,
            'id'               => $this->getId(),
            'provider'         => $this->_provider,
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

    /**
     * @param $prefix
     * @param tubepress_api_options_PersistenceInterface $persistence
     *
     * @return tubepress_api_options_ui_FieldInterface
     */
    public function cloneForMultiSource($prefix, tubepress_api_options_PersistenceInterface $persistence)
    {
        $toReturn = new self(

            $this->_provider,
            $this->getOptionPersistence(),
            $this->getHttpRequestParameters(),
            $this->getTemplating(),
            $this->_persistenceHelper,
            $this->_oauth2Environment
        );

        $toReturn->setMultiSourcePrefix($prefix);

        return $toReturn;
    }
}