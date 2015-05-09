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

class tubepress_app_impl_listeners_template_pre_OptionsPageTemplateListener
{
    private static $_TEMPLATE_VAR_MAP    = 'categoryIdToProviderIdToFieldsMap';
    private static $_TEMPLATE_VAR_FIELDS = 'fields';

    private static $_categorySortMap = array(
        tubepress_app_api_options_ui_CategoryNames::GALLERY_SOURCE,
        tubepress_app_api_options_ui_CategoryNames::THUMBNAILS,
        tubepress_app_api_options_ui_CategoryNames::EMBEDDED,
        tubepress_app_api_options_ui_CategoryNames::THEME,
        tubepress_app_api_options_ui_CategoryNames::META,
        tubepress_app_api_options_ui_CategoryNames::FEED,
        tubepress_app_api_options_ui_CategoryNames::CACHE,
        tubepress_app_api_options_ui_CategoryNames::ADVANCED,
    );

    private static $_providerSortMap = array(

        'field-provider-core',
        'field-provider-youtube',
        'field-provider-vimeo',
        'field-provider-jwplayer5',
        'field-provider-wordpress',
    );

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

    public function __construct(tubepress_app_api_options_PersistenceInterface    $persistence,
                                tubepress_lib_api_http_RequestParametersInterface $requestParams,
                                tubepress_lib_api_template_TemplatingInterface    $templating)
    {
        $this->_persistence   = $persistence;
        $this->_requestParams = $requestParams;
        $this->_templating    = $templating;
    }

    public function onOptionsGuiTemplate(tubepress_lib_api_event_EventInterface $event)
    {
        $templateVariables = $event->getSubject();

        $this->_sortCategories($templateVariables);
        $this->_sortProviders($templateVariables);
        $this->_applyMultiSource($templateVariables);

        $event->setSubject($templateVariables);
    }

    /**
     * Remove any multisource fields from the "fields" array
     */
    public function _applyMultiSource(array &$templateVariables)
    {
        if (!isset($templateVariables[self::$_TEMPLATE_VAR_MAP])) {

            //this should never happen, but let's be defensive
            return;
        }

        if (!isset($templateVariables[self::$_TEMPLATE_VAR_FIELDS])) {

            //this should never happen, but let's be defensive
            return;
        }

        $map                  = $templateVariables[self::$_TEMPLATE_VAR_MAP];
        $fields               = $templateVariables[self::$_TEMPLATE_VAR_FIELDS];
        $feedFields           = array();
        $originalSourceFields = array();

        /**
         * Collect any feed options that are multisource. Remove them from the fields
         * array and save them for the sources field instead.
         */
        if (isset($map[tubepress_app_api_options_ui_CategoryNames::FEED])) {

            foreach ($map[tubepress_app_api_options_ui_CategoryNames::FEED] as $providerId => $fieldIds) {

                foreach ($fieldIds as $fieldId) {

                    if ($this->_isFieldMultiSource($fields, $fieldId)) {

                        if (!isset($feedFields[$providerId])) {

                            $feedFields[$providerId] = array();
                        }

                        $feedFields[$providerId][] = $fields[$fieldId];
                        unset($fields[$fieldId]);
                    }
                }
            }
        }

        /**
         * Collect all the fields for the gallery source tab. Remove them from the fields array
         * and save them for the sources field instead.
         */
        if (isset($map[tubepress_app_api_options_ui_CategoryNames::GALLERY_SOURCE])) {

            foreach ($map[tubepress_app_api_options_ui_CategoryNames::GALLERY_SOURCE] as $providerId => $fieldIds) {

                foreach ($fieldIds as $fieldId) {

                    if (!isset($fields[$fieldId])) {

                        continue;
                    }

                    $field = $fields[$fieldId];

                    if (!isset($originalSourceFields[$providerId])) {

                        $originalSourceFields[$providerId] = array();
                    }

                    $originalSourceFields[$providerId][] = $field;
                    unset($fields[$fieldId]);
                }
            }
        }

        /**
         * Create the sources field and send it to the gallery sources tab.
         */
        if (isset($templateVariables['fieldProviders'])) {

            $sourcesField = new tubepress_app_impl_options_ui_fields_templated_single_SourcesField(
                $this->_persistence,
                $this->_requestParams,
                $this->_templating,
                $originalSourceFields,
                $feedFields,
                $templateVariables['fieldProviders']
            );

            $fields[tubepress_app_api_options_Names::SOURCES] = $sourcesField;
            $map[tubepress_app_api_options_ui_CategoryNames::GALLERY_SOURCE] = array(
                'field-provider-core' => array(
                    tubepress_app_api_options_Names::SOURCES
                )
            );
        }

        $templateVariables[self::$_TEMPLATE_VAR_FIELDS] = $fields;
        $templateVariables[self::$_TEMPLATE_VAR_MAP]    = $map;
    }

    private function _isFieldMultiSource(array $fields, $fieldId)
    {
        if (!array_key_exists($fieldId, $fields)) {

            return false;
        }

        /**
         * @var $field tubepress_app_api_options_ui_FieldInterface
         */
        $field           = $fields[$fieldId];
        $fieldProperties = $field->getProperties();
        $containsKey     = $fieldProperties->containsKey(tubepress_app_api_options_ui_FieldInterface::PROPERTY_APPLIES_TO_MULTISOURCE);

        if (!$containsKey) {

            return false;
        }

        $isMultiSource = $fieldProperties->get(tubepress_app_api_options_ui_FieldInterface::PROPERTY_APPLIES_TO_MULTISOURCE);

        return $isMultiSource === true;
    }

    public function _sortProviders(array &$templateVariables)
    {
        if (!isset($templateVariables[self::$_TEMPLATE_VAR_MAP])) {

            return;
        }

        $map = &$templateVariables[self::$_TEMPLATE_VAR_MAP];

        foreach ($map as $categoryId => $providerMap) {

            $map[$categoryId] = $this->_sortProviderMap($providerMap);
        }
    }

    private function _sortProviderMap(array &$providerMap)
    {
        $toReturn = array();

        foreach (self::$_providerSortMap as $providerId) {

            if (isset($providerMap[$providerId])) {

                $toReturn[$providerId] = $providerMap[$providerId];
            }
        }

        foreach ($providerMap as $id => $fields) {

            if (!isset($toReturn[$id])) {

                $toReturn[$id] = $fields;
            }
        }

        return $toReturn;
    }

    public function _sortCategories(array &$templateVariables)
    {
        if (!isset($templateVariables['categories'])) {

            return;
        }

        /**
         * @var $newCategories tubepress_app_api_options_ui_ElementInterface[]
         */
        $newCategories = array();

        /**
         * @var $existingCategories tubepress_app_api_options_ui_ElementInterface[]
         */
        $existingCategories = $templateVariables['categories'];

        foreach (self::$_categorySortMap as $categoryId) {

            foreach ($existingCategories as $category) {

                if ($category->getId() === $categoryId) {

                    $newCategories[] = $category;
                    break;
                }
            }
        }

        foreach ($existingCategories as $category) {

            $alreadyAdded = false;

            foreach ($newCategories as $newCategory) {

                if ($newCategory->getId() === $category->getId()) {

                    $alreadyAdded = true;
                    break;
                }
            }

            if (!$alreadyAdded) {

                $newCategories[] = $category;
            }
        }

        $templateVariables['categories'] = $newCategories;
    }
}