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
class tubepress_options_ui_impl_listeners_OptionsPageTemplateListener
{
    private static $_TEMPLATE_VAR_CATEGORIES           = 'categories';
    private static $_TEMPLATE_VAR_MAP                  = 'categoryIdToProviderIdToFieldsMap';
    private static $_TEMPLATE_VAR_FIELD_PROVIDERS      = 'fieldProviders';
    private static $_TEMPLATE_VAR_GALLERY_SOURCES      = 'gallerySources';
    private static $_TEMPLATE_VAR_IS_PRO               = 'isPro';
    private static $_TEMPLATE_VAR_BASEURL              = 'baseUrl';
    private static $_TEMPLATE_VAR_MEDIA_PROVIDER_PROPS = 'mediaProviderPropertiesAsJson';

    private static $_categorySortMap = array(
        tubepress_api_options_ui_CategoryNames::GALLERY_SOURCE,
        tubepress_api_options_ui_CategoryNames::THUMBNAILS,
        tubepress_api_options_ui_CategoryNames::EMBEDDED,
        tubepress_api_options_ui_CategoryNames::THEME,
        tubepress_api_options_ui_CategoryNames::META,
        tubepress_api_options_ui_CategoryNames::FEED,
        tubepress_api_options_ui_CategoryNames::CACHE,
        tubepress_api_options_ui_CategoryNames::ADVANCED,
    );

    private static $_providerSortMap = array(
        'field-provider-htmlcache',
        'field-provider-cache-api',
        'field-provider-player',
        'field-provider-embedded-common',
        'field-provider-feed',
        'field-provider-gallery',
        'field-provider-html',
        'field-provider-logger',
        'field-provider-meta',
        'field-provider-options-ui',
        'field-provider-search',
        'field-provider-template',
        'field-provider-theme',
        'field-provider-youtube',
        'field-provider-vimeo',
        'field-provider-jwplayer5',
        'field-provider-wordpress',
    );

    /**
     * @var tubepress_api_environment_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var tubepress_spi_options_ui_FieldProviderInterface[]
     */
    private $_fieldProviders;

    /**
     * @var tubepress_spi_media_MediaProviderInterface[]
     */
    private $_mediaProviders;

    /**
     * @var tubepress_api_translation_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    /**
     * @var array
     */
    private $_fieldIdToProviderInstanceCache;

    public function __construct(tubepress_api_environment_EnvironmentInterface $environment,
                                tubepress_api_translation_TranslatorInterface  $translator,
                                tubepress_api_util_StringUtilsInterface        $stringUtils)
    {
        $this->_environment                    = $environment;
        $this->_translator                     = $translator;
        $this->_stringUtils                    = $stringUtils;
        $this->_fieldIdToProviderInstanceCache = array();
    }

    /**
     * This listener has several very important tasks:
     *
     * 1. Add the template variables:
     *      categories
     *      categoryIdToProviderIdToFieldsMap
     *      fieldProviders
     *      tubePressBaseUrl
     *      isPro
     *
     * 2. Add the multisource template vars (super complicated).
     *
     * 3. Add the field provider properties as JSON
     *
     * 4. Sort the categories.
     *
     * 5. Sort the field providers
     *
     * @param tubepress_api_event_EventInterface $event
     */
    public function onOptionsGuiTemplate(tubepress_api_event_EventInterface $event)
    {
        $templateVariables = $event->getSubject();

        //1
        $this->_addTemplateVariableCategories($templateVariables);
        $this->_addTemplateVariableCategoryIdToProviderIdToFieldsMap($templateVariables);
        $this->_addTemplateVariableFieldProviders($templateVariables);
        $this->_addTemplateVariableTubePressBaseUrl($templateVariables);
        $this->_addTemplateVariableIsPro($templateVariables);

        //2
        $this->_addTemplateVariableGallerySources($templateVariables);

        //3
        $this->_addTemplateVariableMediaProviderProperties($templateVariables);

        //4
        $this->_sortCategories($templateVariables);

        //5
        $this->_sortFieldProviders($templateVariables);

        $event->setSubject($templateVariables);
    }

    public function setFieldProviders(array $fieldProviders)
    {
        foreach ($fieldProviders as $fieldProvider) {

            if (!($fieldProvider instanceof tubepress_app_api_options_ui_FieldProviderInterface)) {

                throw new InvalidArgumentException('Non tubepress_app_api_options_ui_FieldProviderInterface in call to tubepress_options_ui_impl_listeners_OptionsPageTemplateListener::setFieldProviders');
            }
        }

        $this->_fieldProviders = $fieldProviders;
    }

    public function setMediaProviders(array $mediaProviders)
    {
        foreach ($mediaProviders as $mediaProvider) {

            if (!($mediaProvider instanceof tubepress_spi_media_MediaProviderInterface)) {

                throw new InvalidArgumentException('Non tubepress_spi_media_MediaProviderInterface in call to tubepress_options_ui_impl_listeners_OptionsPageTemplateListener::setMediaProviders');
            }
        }

        $this->_mediaProviders = $mediaProviders;
    }

    private function _addTemplateVariableCategories(array &$templateVariables)
    {
        $templateVariables[self::$_TEMPLATE_VAR_CATEGORIES] = $this->_buildCategoriesArray();
    }

    private function _addTemplateVariableFieldProviders(array &$templateVariables)
    {
        $templateVariables[self::$_TEMPLATE_VAR_FIELD_PROVIDERS] = $this->_buildFieldProviderArray();
    }

    private function _addTemplateVariableCategoryIdToProviderIdToFieldsMap(array &$templateVariables)
    {
        $templateVariables[self::$_TEMPLATE_VAR_MAP] = $this->_buildCategoryIdToProviderIdToFieldsMap($templateVariables);
    }

    private function _addTemplateVariableTubePressBaseUrl(array &$templateVariables)
    {
        $templateVariables[self::$_TEMPLATE_VAR_BASEURL] = $this->_environment->getBaseUrl();
    }

    private function _addTemplateVariableIsPro(array &$templateVariables)
    {
        $templateVariables[self::$_TEMPLATE_VAR_IS_PRO] = $this->_environment->isPro();
    }

    /**
     * This is a fairly complex template variable. Here's the pseudocode:
     *
     * sources is the top-level variable. it's an array of arrays. Each child array looks like the following:
     *
     *    id              => the group id
     *    gallery sources => array of field providers, multisource fields only
     *    feed options    => array of field providers, multisource fields only
     *
     * @param array $templateVariables
     */
    private function _addTemplateVariableGallerySources(array &$templateVariables)
    {
        if (!isset($templateVariables['fields'])) {

            //this should never happen, but just to be safe.
            return;
        }

        $finalFieldsVar = $templateVariables['fields'];

        $multiSourceGroupIdsToFieldsMap = $this->_buildMultiSourceGroupIdsToFieldsMap($templateVariables);
        $toReturn                       = array();

        foreach ($multiSourceGroupIdsToFieldsMap as $groupNumber => $fieldArray) {

            $toReturn[] = $this->_buildTemplateVarForSourceGroup($groupNumber, $fieldArray);
        }

        $templateVariables[self::$_TEMPLATE_VAR_GALLERY_SOURCES] = $toReturn;
        $templateVariables['fields']                             = $finalFieldsVar;
    }

    private function _addTemplateVariableMediaProviderProperties(array &$templateVariables)
    {
        $final = array();
        $toSet = array('miniIconUrl', 'untranslatedModeTemplateMap');

        foreach ($this->_mediaProviders as $mediaProvider) {

            $props = array(
                'displayName' => $mediaProvider->getDisplayName(),
                'sourceNames' => $mediaProvider->getGallerySourceNames(),
            );

            foreach ($toSet as $propertyName) {

                if ($mediaProvider->getProperties()->containsKey($propertyName)) {

                    $propertyValue = $mediaProvider->getProperties()->get($propertyName);

                    if ($toSet === 'untranslatedModeTemplateMap') {

                        $propertyValue = $this->_prepareModeTemplateMap($propertyValue);
                    }

                    $props[$propertyName] = $propertyValue;
                }
            }

            $final[$mediaProvider->getName()] = $props;
        }

        $templateVariables[self::$_TEMPLATE_VAR_MEDIA_PROVIDER_PROPS] = json_encode($final);
    }

    /**
     * @param string                                               $groupNumber
     * @param tubepress_api_options_ui_MultiSourceFieldInterface[] $fieldsInGroup
     *
     * @return array
     */
    private function _buildTemplateVarForSourceGroup($groupNumber, array $fieldsInGroup)
    {
        return array(

            'id'                          => $groupNumber,
            'gallerySourceFieldProviders' => $this->_buildWrappedFieldProviders($groupNumber, $fieldsInGroup, true),
            'feedOptionFieldProviders'    => $this->_buildWrappedFieldProviders($groupNumber, $fieldsInGroup, false),
        );
    }

    /**
     * @param $groupNumber
     * @param tubepress_api_options_ui_MultiSourceFieldInterface[] $fieldsInGroup
     * @param $gallerySources
     *
     * @return array
     */
    private function _buildWrappedFieldProviders($groupNumber, $fieldsInGroup, $gallerySources)
    {
        $fieldProviderIdToFieldsMap   = array();
        $fieldProviderIdToInstanceMap = array();

        foreach ($fieldsInGroup as $field) {

            $currentFieldId                                       = $field->getId();
            $originalFieldId                                      = str_replace("tubepress-multisource-$groupNumber-", '', $currentFieldId);
            $actualFieldProvider                                  = $this->_findFieldProviderForFieldId($originalFieldId);
            $actualCategory                                       = $this->_getCategoryIdOfFieldId($originalFieldId, $actualFieldProvider);
            $actualFieldProviderId                                = $actualFieldProvider->getId();
            $fieldProviderIdToInstanceMap[$actualFieldProviderId] = $actualFieldProvider;

            if ($gallerySources && $actualCategory !== tubepress_api_options_ui_CategoryNames::GALLERY_SOURCE) {

                //we only want gallery sources
                continue;
            }

            if (!$gallerySources && $actualCategory === tubepress_api_options_ui_CategoryNames::GALLERY_SOURCE) {

                //we've already done gallery sources
                continue;
            }

            if (!isset($fieldProviderIdToFieldsMap[$actualFieldProviderId])) {

                $wrappedFieldProviders[$actualFieldProviderId] = array();
            }

            $fieldProviderIdToFieldsMap[$actualFieldProviderId][] = $field;
        }

        $wrappedFieldProviders = array();

        foreach ($fieldProviderIdToFieldsMap as $fieldProviderId => $fields) {

            $actualFieldProvider     = $fieldProviderIdToInstanceMap[$fieldProviderId];
            $wrappedFieldProviders[] = new tubepress_options_ui_impl_MultiSourceFieldProviderWrapper($actualFieldProvider, $fields);
        }

        return $wrappedFieldProviders;
    }

    private function _getCategoryIdOfFieldId($fieldId, tubepress_spi_options_ui_FieldProviderInterface $fieldProvider)
    {
        $map = $fieldProvider->getCategoryIdsToFieldIdsMap();

        foreach ($map as $categoryId => $fieldIds) {

            if (in_array($fieldId, $fieldIds)) {

                return $categoryId;
            }
        }

        throw new RuntimeException(sprintf('Unable to find original category for field %s', $fieldId));
    }

    /**
     * @param $fieldId
     *
     * @return tubepress_spi_options_ui_FieldProviderInterface
     */
    private function _findFieldProviderForFieldId($fieldId)
    {
        if (!isset($this->_fieldIdToProviderInstanceCache[$fieldId])) {

            foreach ($this->_fieldProviders as $fieldProvider) {

                $map = $fieldProvider->getCategoryIdsToFieldIdsMap();

                foreach ($map as $categoryId => $fieldIds) {

                    if (in_array($fieldId, $fieldIds)) {

                        $this->_fieldIdToProviderInstanceCache[$fieldId] = $fieldProvider;
                        break;
                    }
                }

                if (isset($this->_fieldIdToProviderInstanceCache[$fieldId])) {

                    break;
                }
            }

            if (!isset($this->_fieldIdToProviderInstanceCache[$fieldId])) {

                throw new RuntimeException(sprintf('Could not find field provider for field %s', $fieldId));
            }
        }

        return $this->_fieldIdToProviderInstanceCache[$fieldId];
    }

    private function _buildMultiSourceGroupIdsToFieldsMap(array $templateVariables)
    {
        $toReturn = array();
        $fields   = $templateVariables['fields'];

        foreach ($fields as $fieldId => $field) {

            if (!($field instanceof tubepress_api_options_ui_MultiSourceFieldInterface)) {

                continue;
            }

            if (preg_match_all('/^tubepress-multisource-([0-9]+)-.+$/', $fieldId, $matches) !== 1) {

                continue;
            }

            if (!is_array($matches) || count($matches) !== 2 || !is_array($matches[1]) || count($matches[1]) !== 1) {

                continue;
            }

            $groupNumbers = $matches[1][0];

            if (!isset($toReturn["$groupNumbers"])) {

                $toReturn["$groupNumbers"] = array();
            }

            $toReturn["$groupNumbers"][] = $field;
        }

        return $toReturn;
    }

    private function _buildCategoryIdToProviderIdToFieldsMap(array $templateVariables)
    {
        $toReturn   = array();
        $categories = $templateVariables[self::$_TEMPLATE_VAR_CATEGORIES];

        /*
         * @var tubepress_api_options_ui_ElementInterface
         */
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

    private function _buildCategoriesArray()
    {
        $toReturn     = array();
        $alreadyAdded = array();

        foreach ($this->_fieldProviders as $fieldProvider) {

            $categoriesFromProvider = $fieldProvider->getCategories();

            foreach ($categoriesFromProvider as $category) {

                if (!in_array($category->getId(), $alreadyAdded)) {

                    $toReturn[] = $category;
                }
            }
        }

        return $toReturn;
    }

    public function _sortFieldProviders(array &$templateVariables)
    {
        if (!isset($templateVariables[self::$_TEMPLATE_VAR_MAP])) {

            return;
        }

        $map            = &$templateVariables[self::$_TEMPLATE_VAR_MAP];
        $gallerySources = &$templateVariables[self::$_TEMPLATE_VAR_GALLERY_SOURCES];

        foreach ($map as $categoryId => $providerMap) {

            $map[$categoryId] = $this->_sortProviderMap($providerMap);
        }

        $internalTemplateVars = array(
            'gallerySourceFieldProviders',
            'feedOptionFieldProviders',
        );

        foreach ($gallerySources as &$gallerySource) {

            foreach ($internalTemplateVars as $internalTemplateVar) {

                if (!isset($gallerySource[$internalTemplateVar])) {

                    continue;
                }

                $fieldProviderArray                  = $gallerySource[$internalTemplateVar];
                $sorted                              = $this->_sortMultisourceFieldProviders($fieldProviderArray);
                $gallerySource[$internalTemplateVar] = $sorted;
            }
        }
    }

    /**
     * @param tubepress_spi_options_ui_FieldProviderInterface[] $multiSourceFieldProviders
     *
     * @return array
     */
    private function _sortMultisourceFieldProviders(array $multiSourceFieldProviders)
    {
        $toReturn = array();
        $addedIds = array();

        foreach (self::$_providerSortMap as $providerId) {

            foreach ($multiSourceFieldProviders as $multiSourceFieldProvider) {

                $msFieldProviderId = $multiSourceFieldProvider->getId();

                if (!$this->_stringUtils->startsWith($msFieldProviderId, $providerId)) {

                    continue;
                }

                $toReturn[] = $multiSourceFieldProvider;
                $addedIds[] = $msFieldProviderId;
            }
        }

        foreach ($multiSourceFieldProviders as $multiSourceFieldProvider) {

            $msFieldProviderId = $multiSourceFieldProvider->getId();

            if (!in_array($msFieldProviderId, $addedIds)) {

                $toReturn[] = $multiSourceFieldProvider;
            }
        }

        return $toReturn;
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
        if (!isset($templateVariables[self::$_TEMPLATE_VAR_CATEGORIES])) {

            return;
        }

        /*
         * @var tubepress_api_options_ui_ElementInterface[]
         */
        $newCategories = array();

        /*
         * @var tubepress_api_options_ui_ElementInterface[]
         */
        $existingCategories = $templateVariables[self::$_TEMPLATE_VAR_CATEGORIES];

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

        $templateVariables[self::$_TEMPLATE_VAR_CATEGORIES] = $newCategories;
    }

    private function _prepareModeTemplateMap($map)
    {
        if (!is_array($map)) {

            //this should never happen
            return $map;
        }

        foreach ($map as $key => $untranslated) {

            $map[$key] = $this->_translator->trans($untranslated);
        }

        return $map;
    }
}
