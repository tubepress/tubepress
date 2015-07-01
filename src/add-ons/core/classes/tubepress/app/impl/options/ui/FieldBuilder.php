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
class tubepress_app_impl_options_ui_FieldBuilder implements tubepress_app_api_options_ui_FieldBuilderInterface
{
    /**
     * @var tubepress_app_api_options_PersistenceInterface
     */
    private $_persistence;

    /**
     * @var tubepress_lib_api_http_RequestParametersInterface
     */
    private $_requestParams;

    /**
     * @var tubepress_lib_api_template_TemplatingInterface
     */
    private $_templating;

    /**
     * @var tubepress_app_api_options_ReferenceInterface
     */
    private $_optionReference;

    /**
     * @var tubepress_platform_api_util_LangUtilsInterface
     */
    private $_langUtils;

    /**
     * @var tubepress_app_api_options_AcceptableValuesInterface
     */
    private $_acceptableValues;

    /**
     * @var tubepress_platform_api_contrib_RegistryInterface
     */
    private $_themeRegistry;

    /**
     * @var tubepress_app_api_media_MediaProviderInterface[]
     */
    private $_mediaProviders = array();

    public function __construct(tubepress_app_api_options_PersistenceInterface      $persistence,
                                tubepress_lib_api_http_RequestParametersInterface   $requestParams,
                                tubepress_lib_api_template_TemplatingInterface      $templating,
                                tubepress_app_api_options_ReferenceInterface        $optionReference,
                                tubepress_platform_api_util_LangUtilsInterface      $langUtils,
                                tubepress_app_api_options_AcceptableValuesInterface $acceptableValues,
                                tubepress_platform_api_contrib_RegistryInterface    $themeRegistry)
    {
        $this->_persistence      = $persistence;
        $this->_requestParams    = $requestParams;
        $this->_templating       = $templating;
        $this->_optionReference  = $optionReference;
        $this->_langUtils        = $langUtils;
        $this->_acceptableValues = $acceptableValues;
        $this->_themeRegistry    = $themeRegistry;
    }

    /**
     * Builds a new field.
     *
     * @param string $id      The unique ID of this field.
     * @param string $type    The type of the field. (e.g. text, radio, dropdown, multi, etc)
     * @param array  $options An optional array of options to contruct the field.
     *
     * @return tubepress_app_api_options_ui_FieldInterface A new instance of the field.
     *
     * @throws InvalidArgumentException If unable to build the given type.
     *
     * @api
     * @since 4.0.0
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
                return $this->_buildBooleanField($id, $options);

            case 'multiSourceBool':
            case 'multiSourceBoolean':
                return $this->_buildMultiSourceBoolean($id, $options);
            
            case 'dropdown':
                return $this->_buildDropdown($id, $options);

            case 'multiSourceDropdown':
                return $this->_buildMultiSourceDropdown($id, $options);
            
            case 'hidden':
                return $this->_buildHidden($id, $options);
            
            case 'spectrum':
                return $this->_buildSpectrum($id, $options);
            
            case 'text':
                return $this->_buildText($id, $options);

            case 'multiSourceText':
                return $this->_buildMultiSourceText($id, $options);

            case 'multiSourceTextArea':
                return $this->_buildMultiSourceTextArea($id, $options);

            case 'theme':
                return $this->_buildTheme();

            case 'metaMultiSelect':
                return $this->_buildMetaMultiSelect();

            case 'orderBy':
                return $this->_buildOrderBy();

            case 'fieldProviderFilter':
                return $this->_buildFieldProviderFilter();

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
        return new tubepress_app_impl_options_ui_fields_templated_multi_FieldProviderFilterField(

            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_optionReference
        );
    }

    private function _buildOrderBy()
    {
        return new tubepress_app_impl_options_ui_fields_templated_single_OrderByField(

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
        return new tubepress_app_impl_options_ui_fields_templated_multi_MetaMultiSelectField(

            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $this->_optionReference,
            $this->_mediaProviders
        );
    }

    private function _buildTheme()
    {
        return new tubepress_app_impl_options_ui_fields_templated_single_ThemeField(

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
        $toReturn = new tubepress_app_impl_options_ui_fields_templated_single_SpectrumColorField(

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
        $toReturn = new tubepress_app_impl_options_ui_fields_templated_single_TextField(
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
        $toReturn = new tubepress_app_impl_options_ui_fields_templated_single_MultiSourceTextField(
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

    private function _buildMultiSourceTextArea($id, $options)
    {
        return new tubepress_app_impl_options_ui_fields_templated_single_MultiSourceSingleOptionField(

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
        return new tubepress_app_impl_options_ui_fields_templated_single_SingleOptionField(
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
        return new tubepress_app_impl_options_ui_fields_templated_single_DropdownField(
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
        return new tubepress_app_impl_options_ui_fields_templated_single_MultiSourceDropdownField(

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
        return new tubepress_app_impl_options_ui_fields_templated_single_MultiSourceSingleOptionField(

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
        return new tubepress_app_impl_options_ui_fields_templated_single_SingleOptionField(
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
        return new tubepress_app_impl_options_ui_fields_GallerySourceField(
            $this->_persistence,
            $this->_requestParams
        );
    }

    private function _buildGallerySourceRadio($id, $options)
    {
        $additionalField = isset($options['additionalField']) ? $options['additionalField'] : null;

        return new tubepress_app_impl_options_ui_fields_templated_GallerySourceRadioField(
            $id,
            $this->_persistence,
            $this->_requestParams,
            $this->_templating,
            $additionalField
        );
    }
}