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
 * Displays a multi-select drop-down input for video meta.
 */
class tubepress_addons_core_impl_options_ui_fields_MetaMultiSelectField extends tubepress_impl_options_ui_fields_AbstractMultiSelectField
{
    const FIELD_ID = 'meta-dropdown';

    /**
     * @var tubepress_spi_provider_PluggableVideoProviderService[]
     */
    private $_videoProviders;

    /**
     * @var string[]
     */
    private $_cachedCoreMetaOptionNames;

    /**
     * @var array
     */
    private $_cachedProvidedMetaOptionNames;

    public function __construct()
    {
        parent::__construct(self::FIELD_ID, 'Show each video\'s...');   //>(translatable)<
    }

    /**
     * Gets whether or not this field is TubePress Pro only.
     *
     * @return boolean True if this field is TubePress Pro only. False otherwise.
     */
    public function isProOnly()
    {
        return false;
    }

    public function setVideoProviders(array $providers)
    {
        $this->_videoProviders = $providers;
    }

    /**
     * @return string[] An array of currently selected values, which may be empty.
     */
    protected function getCurrentlySelectedValues()
    {
        $metaNames      = $this->_getAllMetaOptionNames();
        $storageManager = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();
        $toReturn       = array();

        foreach ($metaNames as $metaName) {

            if ($storageManager->fetch($metaName)) {

                $toReturn[] = $metaName;
            }
        }

        return $toReturn;
    }

    /**
     * @return array An associative array of value => translated display names
     */
    protected function getUngroupedTranslatedChoicesArray()
    {
        //prime cache
        $this->_getAllMetaOptionNames();

        $messageService = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();
        $optionProvider = tubepress_impl_patterns_sl_ServiceLocator::getOptionProvider();
        $toReturn       = array();

        foreach ($this->_cachedCoreMetaOptionNames as $metaOptionName) {

            $toReturn[$metaOptionName] = $messageService->_($optionProvider->getLabel($metaOptionName));
        }

        asort($toReturn);

        return $toReturn;
    }

    /**
     * @return array An associative array of translated group names to associative array of
     *               value => translated display names
     */
    protected function getGroupedTranslatedChoicesArray()
    {
        $toReturn       = array();
        $messageService = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();
        $optionProvider = tubepress_impl_patterns_sl_ServiceLocator::getOptionProvider();

        //prime cache
        $this->_getAllMetaOptionNames();

        foreach ($this->_cachedProvidedMetaOptionNames as $friendlyName => $metaOptionNames) {

            $values = array();

            foreach ($metaOptionNames as $metaOptionName) {

                $values[$metaOptionName] = $messageService->_($optionProvider->getLabel($metaOptionName));
            }

            asort($values);

            $toReturn[$friendlyName] = $values;
        }

        ksort($toReturn);

        return $toReturn;
    }

    private function _getAllMetaOptionNames()
    {
        if (!isset($this->_cachedCoreMetaOptionNames)) {

            $this->_cachedCoreMetaOptionNames = array(

                tubepress_api_const_options_names_Meta::AUTHOR,
                tubepress_api_const_options_names_Meta::CATEGORY,
                tubepress_api_const_options_names_Meta::UPLOADED,
                tubepress_api_const_options_names_Meta::DESCRIPTION,
                tubepress_api_const_options_names_Meta::ID,
                tubepress_api_const_options_names_Meta::KEYWORDS,
                tubepress_api_const_options_names_Meta::LENGTH,
                tubepress_api_const_options_names_Meta::TITLE,
                tubepress_api_const_options_names_Meta::URL,
                tubepress_api_const_options_names_Meta::VIEWS,
            );
        }

        if (!isset($this->_cachedProvidedMetaOptionNames)) {

            $this->_cachedProvidedMetaOptionNames = array();

            /**
             * @var $videoProvider tubepress_spi_provider_PluggableVideoProviderService
             */
            foreach ($this->_videoProviders as $videoProvider) {

                $this->_cachedProvidedMetaOptionNames[$videoProvider->getFriendlyName()] = $videoProvider->getAdditionalMetaNames();
            }
        }

        $toReturn = $this->_cachedCoreMetaOptionNames;

        foreach ($this->_cachedProvidedMetaOptionNames as $friendlyName => $metaOptionNames) {

            $toReturn = array_merge($toReturn, $metaOptionNames);
        }

        return $toReturn;
    }

    /**
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    protected function onSubmitAllMissing()
    {
        $storage     = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();
        $optionNames = $this->_getAllMetaOptionNames();

        //they unchecked everything
        foreach ($optionNames as $optionName) {

            $message = $storage->queueForSave($optionName, false);

            if ($message !== null) {

                return $message;
            }
        }

        return null;
    }

    /**
     * @param array $values The incoming values for this field.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    protected function onSubmitMixed(array $values)
    {
        $storage     = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();
        $optionNames = $this->_getAllMetaOptionNames();

        foreach ($optionNames as $optionName) {

            $message = $storage->queueForSave($optionName, in_array($optionName, $values));

            if ($message !== null) {

                return $message;
            }
        }

        return null;
    }
}