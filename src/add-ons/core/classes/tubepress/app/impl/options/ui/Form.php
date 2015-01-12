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
 * Base class for options pages.
 */
class tubepress_app_impl_options_ui_Form implements tubepress_app_api_options_ui_FormInterface
{
    /**
     * @var tubepress_app_api_environment_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var tubepress_app_api_options_PersistenceInterface
     */
    private $_persistence;

    /**
     * @var tubepress_app_api_options_ui_FieldProviderInterface[] Categories.
     */
    private $_fieldProviders;

    /**
     * @var tubepress_lib_api_template_TemplatingInterface
     */
    private $_templating;

    /**
     * @var tubepress_platform_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    /**
     * @var tubepress_app_impl_html_CssAndJsGenerationHelper
     */
    private $_cssAndJsGenerationHelper;

    public function __construct(tubepress_lib_api_template_TemplatingInterface     $templating,
                                tubepress_app_api_environment_EnvironmentInterface $environment,
                                tubepress_app_api_options_PersistenceInterface     $persistence,
                                tubepress_platform_api_util_StringUtilsInterface   $stringUtils,
                                tubepress_app_impl_html_CssAndJsGenerationHelper   $cssAndJsGenerationHelper)
    {
        $this->_templating               = $templating;
        $this->_environment              = $environment;
        $this->_persistence              = $persistence;
        $this->_stringUtils              = $stringUtils;
        $this->_cssAndJsGenerationHelper = $cssAndJsGenerationHelper;
    }

    /**
     * @param array   $errors        An associative array, which may be empty, of field IDs to error messages.
     * @param boolean $justSubmitted True if the form was just submitted, false otherwise.
     *
     * @return string The HTML for the options page.
     */
    public function getHTML(array $errors = array(), $justSubmitted = false)
    {
        $fields                            = $this->_buildFieldsArray();
        $categories                        = $this->_buildCategoriesArray();
        $categoryIdToProviderIdToFieldsMap = $this->_buildCategoryIdToProviderIdToFieldsMap($categories);
        $fieldProviders                    = $this->_buildFieldProviderArray();

        $templateVariables = array(

            'categories'                           => $categories,
            'categoryIdToProviderIdToFieldsMap'    => $categoryIdToProviderIdToFieldsMap,
            'errors'                               => $errors,
            'fields'                               => $fields,
            'isPro'                                => $this->_environment->isPro(),
            'justSubmitted'                        => $justSubmitted,
            'fieldProviders'                       => $fieldProviders,
            'tubePressBaseUrl'                     => $this->_environment->getBaseUrl()->toString(),
        );

        return $this->_templating->renderTemplate('options-ui/form', $templateVariables);
    }

    /**
     * Invoked when the page is submitted by the user.
     *
     * @return array An associative array, which may be empty, of field IDs to error messages.
     */
    public function onSubmit()
    {
        /**
         * @var tubepress_app_api_options_ui_FieldInterface[] $fields
         */
        $fields = $this->_buildFieldsArray();
        $errors = array();

        foreach ($fields as $field) {

            $fieldError = $field->onSubmit();

            if ($fieldError) {

                $errors[$field->getId()] = $fieldError;
            }
        }

        /**
         * Let's save!
         */
        $this->_persistence->flushSaveQueue();

        return $errors;
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface[]
     *
     * @api
     * @since 4.0.0
     */
    public function getUrlsCSS()
    {
        return $this->_cssAndJsGenerationHelper->getUrlsCSS();
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface[]
     *
     * @api
     * @since 4.0.0
     */
    public function getUrlsJS()
    {
        return $this->_cssAndJsGenerationHelper->getUrlsJS();
    }

    /**
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    public function getCSS()
    {
        return $this->_cssAndJsGenerationHelper->getCSS();
    }

    /**
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    public function getJS()
    {
        return $this->_cssAndJsGenerationHelper->getJS();
    }

    /**
     * This function is called by the IOC container.
     *
     * @param tubepress_app_api_options_ui_FieldProviderInterface[] $providers
     */
    public function setFieldProviders(array $providers)
    {
        $this->_fieldProviders = $providers;
    }

    private function _buildCategoriesArray()
    {
        $toReturn = array();

        foreach ($this->_fieldProviders as $fieldProvider) {

            $toReturn = array_merge($toReturn, $fieldProvider->getCategories());
        }

        return $toReturn;
    }

    private function _buildFieldsArray()
    {
        $fields = array();

        foreach ($this->_fieldProviders as $fieldProvider) {

            $fields = array_merge($fields, $fieldProvider->getFields());
        }

        $toReturn = array();

        /**
         * @var $fields tubepress_app_api_options_ui_FieldInterface[]
         */
        foreach ($fields as $field) {

            $toReturn[$field->getId()] = $field;
        }

        if (isset($toReturn[tubepress_app_impl_options_ui_fields_templated_multi_FieldProviderFilterField::FIELD_ID])) {

            /**
             * @var $filterField tubepress_app_impl_options_ui_fields_templated_multi_FieldProviderFilterField
             */
            $filterField = $toReturn[tubepress_app_impl_options_ui_fields_templated_multi_FieldProviderFilterField::FIELD_ID];

            $filterField->setFieldProviders($this->_fieldProviders);
        }

        return $toReturn;
    }

    /**
     * @param tubepress_app_api_options_ui_ElementInterface[] $categories
     *
     * @return array
     */
    private function _buildCategoryIdToProviderIdToFieldsMap(array $categories)
    {
        $toReturn = array();

        foreach ($categories as $category) {

            $categoryId = $category->getId();

            if (!isset($toReturn[$categoryId])) {

                $toReturn[$categoryId] = array();
            }

            foreach ($this->_fieldProviders as $fieldProvider) {

                $map = $fieldProvider->getCategoryIdsToFieldIdsMap();

                if (!isset($map[$categoryId])) {

                    continue;
                }

                $toReturn[$categoryId][$fieldProvider->getId()] = $map[$categoryId];
            }
        }

        return $toReturn;
    }

    private function _buildFieldProviderArray()
    {
        $toReturn = array();

        foreach ($this->_fieldProviders as $fieldProvider) {

            $toReturn[$fieldProvider->getId()] = $fieldProvider;
        }

        return $toReturn;
    }
}