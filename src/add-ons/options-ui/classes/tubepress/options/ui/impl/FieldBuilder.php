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

/**
 *
 */
class tubepress_options_ui_impl_FieldBuilder implements tubepress_api_options_ui_FieldBuilderInterface
{
    /**
     * @var tubepress_api_options_PersistenceInterface
     */
    private $_persistence;

    /**
     * @var tubepress_api_http_RequestParametersInterface
     */
    private $_requestParams;

    /**
     * @var tubepress_api_template_TemplatingInterface
     */
    private $_templating;

    /**
     * @var tubepress_api_options_ReferenceInterface
     */
    private $_optionReference;

    /**
     * @var tubepress_api_util_LangUtilsInterface
     */
    private $_langUtils;

    /**
     * @var tubepress_api_options_AcceptableValuesInterface
     */
    private $_acceptableValues;

    /**
     * @var tubepress_api_contrib_RegistryInterface
     */
    private $_themeRegistry;

    /**
     * @var tubepress_spi_media_MediaProviderInterface[]
     */
    private $_mediaProviders = array();

    /**
     * @var tubepress_http_oauth2_impl_util_PersistenceHelper
     */
    private $_persistenceHelper;

    /**
     * @var tubepress_api_http_oauth2_Oauth2EnvironmentInterface
     */
    private $_oauth2Environment;

    /**
     * @var tubepress_api_translation_TranslatorInterface
     */
    private $_translator;

    public function __construct(tubepress_api_options_PersistenceInterface           $persistence,
                                tubepress_api_http_RequestParametersInterface        $requestParams,
                                tubepress_api_template_TemplatingInterface           $templating,
                                tubepress_api_options_ReferenceInterface             $optionReference,
                                tubepress_api_util_LangUtilsInterface                $langUtils,
                                tubepress_api_options_AcceptableValuesInterface      $acceptableValues,
                                tubepress_api_contrib_RegistryInterface              $themeRegistry,
                                tubepress_http_oauth2_impl_util_PersistenceHelper    $persistenceHelper,
                                tubepress_api_http_oauth2_Oauth2EnvironmentInterface $oauth2Environment,
                                tubepress_api_translation_TranslatorInterface        $translator)
    {
        $this->_persistence       = $persistence;
        $this->_requestParams     = $requestParams;
        $this->_templating        = $templating;
        $this->_optionReference   = $optionReference;
        $this->_langUtils         = $langUtils;
        $this->_acceptableValues  = $acceptableValues;
        $this->_themeRegistry     = $themeRegistry;
        $this->_persistenceHelper = $persistenceHelper;
        $this->_oauth2Environment = $oauth2Environment;
        $this->_translator        = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function newInstance($id, $type, array $options = array())
    {
        switch ($type) {

            case 'gallerySource':
                return $this->_buildGallerySource();

            case 'gallerySourceRadio':
                return $this->_buildGallerySourceRadio($id, $options);

            case 'bool':
            case 'boolean':
                return $this->_buildBooleanField($id);

            case 'multiSourceBool':
            case 'multiSourceBoolean':
                return $this->_buildMultiSourceBoolean($id);

            case 'dropdown':
                return $this->_buildDropdown($id);

            case 'multiSourceDropdown':
                return $this->_buildMultiSourceDropdown($id);

            case 'hidden':
                return $this->_buildHidden($id);

            case 'spectrum':
                return $this->_buildSpectrum($id, $options);

            case 'text':
                return $this->_buildText($id, $options);

            case 'multiSourceText':
                return $this->_buildMultiSourceText($id, $options);

            case 'multiSourceTextArea':
                return $this->_buildMultiSourceTextArea($id);

            case 'theme':
                return $this->_buildTheme();

            case 'metaMultiSelect':
                return $this->_buildMetaMultiSelect();

            case 'orderBy':
                return $this->_buildOrderBy();

            case 'fieldProviderFilter':
                return $this->_buildFieldProviderFilter();

            case 'oauth2TokenManagement':
                return $this->_buildOauth2TokenManagement($options);

            case 'oauth2ClientInstructions':
                return $this->_buildOauth2ClientInstructions($options);

            case 'oauth2ClientId':
                return $this->_buildOauth2ClientId($options);

            case 'oauth2ClientSecret':
                return $this->_buildOauth2ClientSecret($options);

            case 'oauth2ClientCredentialsSaving':
                return $this->_buildOauth2ClientCredentialsSaving();

            case 'oauth2TokenDeletion':
                return $this->_buildOauth2TokenDeletion();

            case 'oauth2TokenSelection':
                return $this->_buildOauth2TokenSelection($options);

            case 'textarea':
                return $this->_buildTextArea($id);

            default:
                throw new InvalidArgumentException('Unknown field type: ' . $type);
        }
    }

    public function setMediaProviders(array $providers)
    {
        $this->_mediaProviders = $providers;
    }

    private function _buildFieldProviderFilter()
    {
        return new tubepress_options_ui_impl_fields_templated_multi_FieldProviderFilterField(

            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_optionReference
        );
    }

    private function _buildOrderBy()
    {
        return new tubepress_options_ui_impl_fields_templated_single_OrderByField(

            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_optionReference,
            $this->_acceptableValues,
            $this->_langUtils
        );
    }

    private function _buildMetaMultiSelect()
    {
        return new tubepress_options_ui_impl_fields_templated_multi_MetaMultiSelectField(

            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_optionReference,
            $this->_mediaProviders
        );
    }

    private function _buildTheme()
    {
        return new tubepress_options_ui_impl_fields_templated_single_ThemeField(

            $this->_persistence,
            $this->_requestParams,
            $this->_optionReference,
            $this->_templating,
            $this->_langUtils,
            $this->_themeRegistry,
            $this->_acceptableValues
        );
    }

    private function _buildSpectrum($id, $options)
    {
        $toReturn = new tubepress_options_ui_impl_fields_templated_single_SpectrumColorField(

            $id,
            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_optionReference
        );

        if (isset($options['preferredFormat'])) {

            switch ($options['preferredFormat']) {
                case 'rgb':
                    $toReturn->setPreferredFormatToRgb();
                    break;
                case 'name':
                    $toReturn->setPreferredFormatToName();
                    break;
                case 'hex':
                    $toReturn->setPreferredFormatToHex();
                    break;
                default:
                    break;
            }
        }

        if (isset($options['showAlpha'])) {

            $toReturn->setShowAlpha($options['showAlpha']);
        }

        if (isset($options['showInput'])) {

            $toReturn->setShowInput($options['showInput']);
        }

        if (isset($options['showSelectionPalette'])) {

            $toReturn->setShowSelectionPalette($options['showSelectionPalette']);
        }

        return $toReturn;
    }

    private function _buildText($id, $options)
    {
        $toReturn = new tubepress_options_ui_impl_fields_templated_single_TextField(
            $id,
            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_optionReference
        );

        if (isset($options['size'])) {

            $toReturn->setSize($options['size']);
        }

        return $toReturn;
    }

    private function _buildMultiSourceText($id, $options)
    {
        $toReturn = new tubepress_options_ui_impl_fields_templated_single_MultiSourceTextField(
            $id,
            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_optionReference
        );

        if (isset($options['size'])) {

            $toReturn->setSize($options['size']);
        }

        return $toReturn;
    }

    private function _buildTextArea($id)
    {
        return new tubepress_options_ui_impl_fields_templated_single_SingleOptionField(

            $id,
            'options-ui/fields/textarea',
            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_optionReference
        );
    }

    private function _buildMultiSourceTextArea($id)
    {
        return new tubepress_options_ui_impl_fields_templated_single_MultiSourceSingleOptionField(

            $id,
            'options-ui/fields/textarea',
            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_optionReference
        );
    }

    private function _buildHidden($id)
    {
        return new tubepress_options_ui_impl_fields_templated_single_SingleOptionField(
            $id,
            'options-ui/fields/hidden',
            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_optionReference
        );
    }

    private function _buildDropdown($id)
    {
        return new tubepress_options_ui_impl_fields_templated_single_DropdownField(
            $id,
            $this->_persistence,
            $this->_requestParams,
            $this->_optionReference,
            $this->_templating,
            $this->_langUtils,
            $this->_acceptableValues
        );
    }

    private function _buildMultiSourceDropdown($id)
    {
        return new tubepress_options_ui_impl_fields_templated_single_MultiSourceDropdownField(

            $id,
            $this->_persistence,
            $this->_requestParams,
            $this->_optionReference,
            $this->_templating,
            $this->_langUtils,
            $this->_acceptableValues
        );
    }

    private function _buildMultiSourceBoolean($id)
    {
        return new tubepress_options_ui_impl_fields_templated_single_MultiSourceSingleOptionField(

            $id,
            'options-ui/fields/checkbox',
            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_optionReference
        );
    }

    private function _buildBooleanField($id)
    {
        return new tubepress_options_ui_impl_fields_templated_single_SingleOptionField(
            $id,
            'options-ui/fields/checkbox',
            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_optionReference
        );
    }

    private function _buildGallerySource()
    {
        return new tubepress_options_ui_impl_fields_GallerySourceField(
            $this->_persistence,
            $this->_requestParams
        );
    }

    private function _buildGallerySourceRadio($id, $options)
    {
        $additionalField = isset($options['additionalField']) ? $options['additionalField'] : null;

        return new tubepress_options_ui_impl_fields_templated_GallerySourceRadioField(
            $id,
            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $additionalField
        );
    }

    private function _buildOauth2TokenManagement($options)
    {
        if (!isset($options['provider'])) {

            throw new RuntimeException('Cannot build tubepress_http_oauth2_impl_options_ui_TokenManagementField without provider');
        }

        $provider = $options['provider'];

        if (!($provider instanceof tubepress_spi_http_oauth2_Oauth2ProviderInterface)) {

            throw new RuntimeException('Cannot build tubepress_http_oauth2_impl_options_ui_TokenManagementField with a non-provider');
        }

        return new tubepress_http_oauth2_impl_options_ui_TokenManagementField(
            $provider,
            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_persistenceHelper,
            $this->_oauth2Environment
        );
    }

    private function _buildOauth2ClientInstructions($options)
    {
        if (!isset($options['provider'])) {

            throw new RuntimeException('Cannot build tubepress_http_oauth2_impl_options_ui_ClientInstructionsField without provider');
        }

        $provider = $options['provider'];

        if (!($provider instanceof tubepress_spi_http_oauth2_Oauth2ProviderInterface)) {

            throw new RuntimeException('Cannot build tubepress_http_oauth2_impl_options_ui_ClientInstructionsField with a non-provider');
        }

        return new tubepress_http_oauth2_impl_options_ui_ClientInstructionsField(
            $provider,
            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_persistenceHelper,
            $this->_oauth2Environment,
            $this->_translator
        );
    }

    private function _buildOauth2ClientId($options)
    {
        if (!isset($options['provider'])) {

            throw new RuntimeException('Cannot build tubepress_http_oauth2_impl_options_ui_ClientIdField without provider');
        }

        $provider = $options['provider'];

        if (!($provider instanceof tubepress_spi_http_oauth2_Oauth2ProviderInterface)) {

            throw new RuntimeException('Cannot build tubepress_http_oauth2_impl_options_ui_ClientIdField with a non-provider');
        }

        return new tubepress_http_oauth2_impl_options_ui_ClientIdField(
            $provider,
            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_persistenceHelper,
            $this->_translator
        );
    }

    private function _buildOauth2ClientSecret($options)
    {
        if (!isset($options['provider'])) {

            throw new RuntimeException('Cannot build tubepress_http_oauth2_impl_options_ui_ClientSecretField without provider');
        }

        $provider = $options['provider'];

        if (!($provider instanceof tubepress_spi_http_oauth2_Oauth2ProviderInterface)) {

            throw new RuntimeException('Cannot build tubepress_http_oauth2_impl_options_ui_ClientSecretField with a non-provider');
        }

        return new tubepress_http_oauth2_impl_options_ui_ClientSecretField(
            $provider,
            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_persistenceHelper,
            $this->_translator
        );
    }

    private function _buildOauth2TokenSelection($options)
    {
        if (!isset($options['provider'])) {

            throw new RuntimeException('Cannot build tubepress_http_oauth2_impl_options_ui_TokenSelectionField without provider');
        }

        $provider = $options['provider'];

        if (!($provider instanceof tubepress_spi_http_oauth2_Oauth2ProviderInterface)) {

            throw new RuntimeException('Cannot build tubepress_http_oauth2_impl_options_ui_TokenSelectionField with a non-provider');
        }

        return new tubepress_http_oauth2_impl_options_ui_TokenSelectionField(
            $provider,
            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_persistenceHelper,
            $this->_oauth2Environment
        );
    }

    private function _buildOauth2ClientCredentialsSaving()
    {
        return new tubepress_http_oauth2_impl_options_ui_ClientCredentialsSavingField(
            $this->_persistence,
            $this->_requestParams,
            $this->_persistenceHelper
        );
    }

    private function _buildOauth2TokenDeletion()
    {
        return new tubepress_http_oauth2_impl_options_ui_TokenDeletionField(
            $this->_persistence,
            $this->_requestParams,
            $this->_persistenceHelper
        );
    }
}
