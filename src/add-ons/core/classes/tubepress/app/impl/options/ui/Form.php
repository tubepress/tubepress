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
     * @var tubepress_app_impl_options_Persistence
     */
    private $_persistence;

    /**
     * @var tubepress_app_api_options_ui_FieldProviderInterface[]
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

    /**
     * @var tubepress_lib_api_http_RequestParametersInterface
     */
    private $_httpRequestParams;

    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    /**
     * @var tubepress_app_api_options_PersistenceInterface[]
     */
    private $_multiSourcePersistenceServices;

    /**
     * @var tubepress_app_impl_options_MultiSourcePersistenceBackend[]
     */
    private $_multiSourcePersistenceBackends;

    /**
     * @var tubepress_app_api_options_ui_MultiSourceFieldInterface[]
     */
    private $_cachedGroupIds;

    public function __construct(tubepress_platform_api_log_LoggerInterface        $logger,
                                tubepress_lib_api_template_TemplatingInterface    $templating,
                                tubepress_app_impl_options_Persistence            $persistence,
                                tubepress_platform_api_util_StringUtilsInterface  $stringUtils,
                                tubepress_app_impl_html_CssAndJsGenerationHelper  $cssAndJsGenerationHelper,
                                tubepress_lib_api_http_RequestParametersInterface $httpRequestParams)
    {
        $this->_logger                   = $logger;
        $this->_templating               = $templating;
        $this->_persistence              = $persistence;
        $this->_stringUtils              = $stringUtils;
        $this->_cssAndJsGenerationHelper = $cssAndJsGenerationHelper;
        $this->_httpRequestParams        = $httpRequestParams;
        $this->_shouldLog                = $logger->isEnabled();
    }

    /**
     * @param array   $errors        An associative array, which may be empty, of field IDs to error messages.
     * @param boolean $justSubmitted True if the form was just submitted, false otherwise.
     *
     * @return string The HTML for the options page.
     */
    public function getHTML(array $errors = array(), $justSubmitted = false)
    {
        $hasErrors         = count($errors) > 0;
        $fields            = $this->_buildFieldsArray(false, $hasErrors);
        $templateVariables = array(

            'errors'        => $errors,
            'fields'        => $fields,
            'justSubmitted' => $justSubmitted,
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
        $fields = $this->_buildFieldsArray(true);
        $errors = array();

        foreach ($fields as $field) {

            $fieldError = $field->onSubmit();

            if ($fieldError) {

                $errors[$field->getId()] = $fieldError;
            }
        }

        $this->_flushPersistence();

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
     * @param tubepress_app_api_options_ui_FieldProviderInterface[] $providers
     */
    public function setFieldProviders(array $providers)
    {
        $this->_fieldProviders = $providers;
    }

    private function _buildFieldsArray($fromSubmission, $hasErrors = false)
    {
        $fields = array();

        foreach ($this->_fieldProviders as $fieldProvider) {

            $fieldsFromProvider = $fieldProvider->getFields();

            foreach ($fieldsFromProvider as $fieldFromProvider) {

                if (!($fieldFromProvider instanceof tubepress_app_api_options_ui_MultiSourceFieldInterface)) {

                    $fields[] = $fieldFromProvider;
                }
            }
        }

        $multiSourceFields = $this->_buildMultiSourceFieldsArray($fromSubmission, $hasErrors);
        $fields            = array_merge($fields, $multiSourceFields);
        $toReturn          = array();

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

    private function _buildMultiSourceFieldsArray($fromSubmit, $hasErrors)
    {
        if ($fromSubmit) {

            return $this->_buildMultiSourceFieldsFromSubmission();
        }

        return $this->_buildMultiSourceFieldsFromStorage($hasErrors);
    }

    private function _buildMultiSourceFieldsFromSubmission()
    {
        $this->_collectGroupIdsFromSubmission();

        $fieldClones                           = array();
        $this->_multiSourcePersistenceBackends = array();
        $this->_multiSourcePersistenceServices = array();
        $persistedOptions                      = $this->_persistence->fetchAll();
        $multiSourceFields                     = $this->_getMultiSourceFieldsFromProviders();

        foreach ($this->_cachedGroupIds as $sourceGroupId) {

            $backend                                 = new tubepress_app_impl_options_MultiSourcePersistenceBackend($persistedOptions);
            $multiSourcePersistence                  = $this->_persistence->getCloneWithCustomBackend($backend);
            $this->_multiSourcePersistenceBackends[] = $backend;
            $this->_multiSourcePersistenceServices[] = $multiSourcePersistence;

            /**
             * @var $multiSourceFields tubepress_app_api_options_ui_MultiSourceFieldInterface[]
             */
            foreach ($multiSourceFields as $multiSourceField) {

                $fieldClones[] = $multiSourceField->cloneForMultiSource("tubepress-multisource-$sourceGroupId-", $multiSourcePersistence);
            }
        }

        $toReturn = array();

        /**
         * @var $fieldClones tubepress_app_api_options_ui_FieldInterface[]
         */
        foreach ($fieldClones as $collectedField) {

            $toReturn[$collectedField->getId()] = $collectedField;
        }

        return $toReturn;
    }

    private function _collectGroupIdsFromSubmission()
    {
        $allParams             = $this->_httpRequestParams->getAllParams();
        $this->_cachedGroupIds = array();

        foreach ($allParams as $key => $value) {

            if (preg_match_all('/^tubepress-multisource-([0-9]+)-.+$/', $key, $matches) !== 1) {

                continue;
            }

            if (!is_array($matches) || count($matches) !== 2 || !is_array($matches[1]) || count($matches[1]) !== 1) {

                continue;
            }

            $this->_cachedGroupIds[] = $matches[1][0];
        }

        $this->_cachedGroupIds = array_values(array_unique($this->_cachedGroupIds));

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Found %d multisource group(s): %s', count($this->_cachedGroupIds), json_encode($this->_cachedGroupIds)));
        }
    }

    private function _buildMultiSourceFieldsFromStorage($hasErrors)
    {
        $allPersistedOptions = $this->_persistence->fetchAll();

        $toReturn = $this->_buildMultiSourceFieldsFromStoredSources($allPersistedOptions, $hasErrors);

        if (!$toReturn) {

            $toReturn = $this->_buildMultiSourceFieldsFromLegacyStorage($allPersistedOptions, $hasErrors);
        }

        $this->_cachedGroupIds = array();

        return $toReturn;
    }

    private function _buildMultiSourceFieldsFromStoredSources(array $allPersistedOptions, $hasErrors)
    {
        if (!isset($allPersistedOptions[tubepress_app_api_options_Names::SOURCES])) {

            return null;
        }

        $storedSources  = $allPersistedOptions[tubepress_app_api_options_Names::SOURCES];
        $decodedSources = json_decode($storedSources, true);

        if (!$decodedSources) {

            return null;
        }

        $toReturn = array();

        for ($index = 0; $index < count($decodedSources); $index++) {

            $decodedSource = $decodedSources[$index];
            $mergedOptions = array_merge($allPersistedOptions, $decodedSource);
            $fields        = $this->_buildFieldArrayForSingleSource($mergedOptions, $hasErrors, $index);
            $toReturn      = array_merge($toReturn, $fields);
        }

        return $toReturn;
    }

    private function _buildMultiSourceFieldsFromLegacyStorage(array $allPersistedOptions, $hasErrors)
    {
        return $this->_buildFieldArrayForSingleSource($allPersistedOptions, $hasErrors, 0);
    }

    private function _buildFieldArrayForSingleSource(array $options, $hasErrors, $index)
    {
        if ($hasErrors && isset($this->_cachedGroupIds[$index])) {

            $groupId = $this->_cachedGroupIds[$index];

        } else {

            $groupId = mt_rand(100000, 1000000);
        }

        $prefix                        = "tubepress-multisource-$groupId-";
        $multiSourcePersistenceBackend = new tubepress_app_impl_options_MultiSourcePersistenceBackend($options);
        $readOnlyPersistence           = $this->_persistence->getCloneWithCustomBackend($multiSourcePersistenceBackend);
        $toReturn                      = array();
        $multiSourceFields             = $this->_getMultiSourceFieldsFromProviders();

        foreach ($multiSourceFields as $multiSourceField) {

            $toReturn[] = $multiSourceField->cloneForMultiSource($prefix, $readOnlyPersistence);
        }

        return $toReturn;
    }

    /**
     * @return tubepress_app_api_options_ui_MultiSourceFieldInterface[]
     */
    private function _getMultiSourceFieldsFromProviders()
    {
        $toReturn = array();

        foreach ($this->_fieldProviders as $fieldProvider) {

            $fieldsFromProvider = $fieldProvider->getFields();

            foreach ($fieldsFromProvider as $fieldFromProvider) {

                if ($fieldFromProvider instanceof tubepress_app_api_options_ui_MultiSourceFieldInterface) {

                    $toReturn[] = $fieldFromProvider;
                }
            }
        }

        return $toReturn;
    }

    private function _flushPersistence()
    {
        foreach ($this->_multiSourcePersistenceServices as $multiSourcePersistenceService) {

            $multiSourcePersistenceService->flushSaveQueue();
        }

        $sources = array();

        foreach ($this->_multiSourcePersistenceBackends as $multiSourcePersistenceBackend) {

            $sources[] = $multiSourcePersistenceBackend->getPersistenceQueue();
        }

        $sources = json_encode($sources);

        $this->_persistence->queueForSave(tubepress_app_api_options_Names::SOURCES, $sources);

        $this->_persistence->flushSaveQueue();
    }
}