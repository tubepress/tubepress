<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
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
class tubepress_core_options_ui_impl_FieldBuilder implements tubepress_core_options_ui_api_FieldBuilderInterface
{
    /**
     * @var tubepress_core_translation_api_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_core_options_api_PersistenceInterface
     */
    private $_persistence;

    /**
     * @var tubepress_core_http_api_RequestParametersInterface
     */
    private $_requestParams;

    /**
     * @var tubepress_core_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_core_template_api_TemplateFactoryInterface
     */
    private $_templateFactory;

    /**
     * @var tubepress_core_options_api_ReferenceInterface
     */
    private $_optionReference;

    /**
     * @var tubepress_api_util_LangUtilsInterface
     */
    private $_langUtils;

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_options_api_AcceptableValuesInterface
     */
    private $_acceptableValues;

    /**
     * @var tubepress_core_theme_api_ThemeLibraryInterface
     */
    private $_themeLibrary;

    /**
     * @var tubepress_api_contrib_RegistryInterface
     */
    private $_themeRegistry;

    /**
     * @var tubepress_core_media_provider_api_MediaProviderInterface[]
     */
    private $_mediaProviders;

    public function __construct(tubepress_core_translation_api_TranslatorInterface   $translator,
                                tubepress_core_options_api_PersistenceInterface      $persistence,
                                tubepress_core_http_api_RequestParametersInterface   $requestParams,
                                tubepress_core_event_api_EventDispatcherInterface    $eventDispatcher,
                                tubepress_core_template_api_TemplateFactoryInterface $templateFactory,
                                tubepress_core_options_api_ReferenceInterface        $optionProvider,
                                tubepress_api_util_LangUtilsInterface                $langUtils,
                                tubepress_core_options_api_ContextInterface          $context,
                                tubepress_core_options_api_AcceptableValuesInterface $acceptableValues,
                                tubepress_core_theme_api_ThemeLibraryInterface       $themeLibrary)
    {
        $this->_translator       = $translator;
        $this->_persistence      = $persistence;
        $this->_requestParams    = $requestParams;
        $this->_eventDispatcher  = $eventDispatcher;
        $this->_templateFactory  = $templateFactory;
        $this->_optionReference  = $optionProvider;
        $this->_langUtils        = $langUtils;
        $this->_context          = $context;
        $this->_acceptableValues = $acceptableValues;
        $this->_themeLibrary     = $themeLibrary;
    }

    /**
     * Builds a new field.
     *
     * @param string $id      The unique ID of this field.
     * @param string $type    The type of the field. (e.g. text, radio, dropdown, multi, etc)
     * @param array  $options An optional array of options to contruct the field.
     *
     * @return tubepress_core_options_ui_api_FieldInterface A new instance of the field.
     *
     * @throws InvalidArgumentException If unable to build the given type.
     *
     * @api
     * @since 4.0.0
     */
    public function newInstance($id, $type, array $options = array())
    {
        switch ($type) {

            case 'gallerySourceRadio':
                return $this->_buildGallerySourceRadio($id, $options);
            
            case 'bool':
            case 'boolean':
                return $this->_buildBooleanField($id, $options);
            
            case 'dropdown':
                return $this->_buildDropdown($id, $options);
            
            case 'hidden':
                return $this->_buildHidden($id, $options);
            
            case 'spectrum':
                return $this->_buildSpectrum($id, $options);
            
            case 'text':
                return $this->_buildText($id, $options);

            case 'theme':
                return $this->_buildTheme();

            case 'metaMultiSelect':
                return $this->_buildMetaMultiSelect();

            default:
                throw new InvalidArgumentException('Unknown field type');
        }
    }

    public function setThemeRegistry(tubepress_api_contrib_RegistryInterface $themeRegistry)
    {
        $this->_themeRegistry = $themeRegistry;
    }

    public function setMediaProviders(array $providers)
    {
        $this->_mediaProviders = $providers;
    }

    private function _buildMetaMultiSelect()
    {
        return new tubepress_core_options_ui_impl_fields_MetaMultiSelectField(
            $this->_translator,
            $this->_persistence,
            $this->_requestParams,
            $this->_eventDispatcher,
            $this->_templateFactory,
            $this->_optionReference,
            $this->_mediaProviders
        );
    }

    private function _buildTheme()
    {
        return new tubepress_core_options_ui_impl_fields_provided_ThemeField(

            $this->_translator,
            $this->_persistence,
            $this->_requestParams,
            $this->_eventDispatcher,
            $this->_optionReference,
            $this->_templateFactory,
            $this->_langUtils,
            $this->_themeRegistry,
            $this->_themeLibrary,
            $this->_acceptableValues
        );
    }

    private function _buildSpectrum($id, $options)
    {
        $toReturn = new tubepress_core_options_ui_impl_fields_provided_SpectrumColorField(
            $id,
            $this->_translator,
            $this->_persistence,
            $this->_requestParams,
            $this->_eventDispatcher,
            $this->_templateFactory,
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
        $toReturn = new tubepress_core_options_ui_impl_fields_provided_TextField(
            $id,
            $this->_translator,
            $this->_persistence,
            $this->_requestParams,
            $this->_eventDispatcher,
            $this->_templateFactory,
            $this->_optionReference
        );

        if (isset($options['size'])) {

            $toReturn->setSize($options['size']);
        }

        return $toReturn;
    }

    private function _buildHidden($id, $options)
    {
        return new tubepress_core_options_ui_impl_fields_provided_HiddenField(
            $id,
            $this->_translator,
            $this->_persistence,
            $this->_requestParams,
            $this->_eventDispatcher,
            $this->_templateFactory,
            $this->_optionReference
        );
    }

    private function _buildDropdown($id, $options)
    {
        return new tubepress_core_options_ui_impl_fields_provided_DropdownField(
            $id,
            $this->_translator,
            $this->_persistence,
            $this->_requestParams,
            $this->_eventDispatcher,
            $this->_optionReference,
            $this->_templateFactory,
            $this->_langUtils,
            $this->_acceptableValues
        );
    }

    private function _buildBooleanField($id, $options)
    {
        return new tubepress_core_options_ui_impl_fields_provided_BooleanField(
            $id,
            $this->_translator,
            $this->_persistence,
            $this->_requestParams,
            $this->_eventDispatcher,
            $this->_templateFactory,
            $this->_optionReference
        );
    }

    private function _buildGallerySourceRadio($id, $options)
    {
        $additionalField = isset($options['additionalField']) ? $options['additionalField'] : null;

        return new tubepress_core_options_ui_impl_fields_GallerySourceRadioField(
            $id,
            $this->_translator,
            $this->_persistence,
            $this->_requestParams,
            $this->_eventDispatcher,
            $this->_templateFactory,
            $this->_context,
            $additionalField
        );
    }
}