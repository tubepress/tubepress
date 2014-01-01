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
     * @var $metas tubepress_spi_options_OptionDescriptor[]
     */
    private $_cachedCoreOptionDescriptors;

    /**
     * @var tubepress_spi_options_OptionDescriptor[]
     */
    private $_cachedProvidedOptionDescriptors;

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
        $optionDescriptors = $this->_getOptionDescriptors();
        $storageManager    = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();
        $toReturn          = array();

        foreach ($optionDescriptors as $metaOptionDescriptor) {

            if ($storageManager->fetch($metaOptionDescriptor->getName())) {

                $toReturn[] = $metaOptionDescriptor->getName();
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
        $this->_getOptionDescriptors();

        $messageService = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();
        $toReturn       = array();

        foreach ($this->_cachedCoreOptionDescriptors as $metaOptionDescriptor) {

            $toReturn[$metaOptionDescriptor->getName()] = $messageService->_($metaOptionDescriptor->getLabel());
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

        //prime cache
        $this->_getOptionDescriptors();

        foreach ($this->_cachedProvidedOptionDescriptors as $friendlyName => $optionDescriptorArray) {

            $values = array();

            /**
             * @var $optionDescriptorArray tubepress_spi_options_OptionDescriptor[]
             */
            foreach ($optionDescriptorArray as $optionDescriptor) {

                $values[$optionDescriptor->getName()] = $messageService->_($optionDescriptor->getLabel());
            }

            asort($values);

            $toReturn[$friendlyName] = $values;
        }

        ksort($toReturn);

        return $toReturn;
    }

    private function _getOptionDescriptors()
    {
        if (!isset($this->_cachedCoreOptionDescriptors)) {

            $metaNames = array(

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

            $this->_cachedCoreOptionDescriptors = $this->_lookupOptionDescriptors($metaNames);
        }

        if (!isset($this->_cachedProvidedOptionDescriptors)) {

            $this->_cachedProvidedOptionDescriptors = array();

            /**
             * @var $videoProvider tubepress_spi_provider_PluggableVideoProviderService
             */
            foreach ($this->_videoProviders as $videoProvider) {

                $providedOptionDescriptors = $this->_lookupOptionDescriptors($videoProvider->getAdditionalMetaNames());

                $this->_cachedProvidedOptionDescriptors[$videoProvider->getFriendlyName()] = $providedOptionDescriptors;
            }
        }

        $toReturn = $this->_cachedCoreOptionDescriptors;

        foreach ($this->_cachedProvidedOptionDescriptors as $friendlyName => $optionDescriptorArray) {

            $toReturn = array_merge($toReturn, $optionDescriptorArray);
        }

        return $toReturn;
    }

    /**
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    protected function onSubmitAllMissing()
    {
        $storage     = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();
        $optionNames = array_keys($this->_getOptionDescriptors());

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
        $optionNames = array_keys($this->_getOptionDescriptors());

        foreach ($optionNames as $optionName) {

            $message = $storage->queueForSave($optionName, in_array($optionName, $values));

            if ($message !== null) {

                return $message;
            }
        }

        return null;
    }

    /**
     * @param string[] $names
     *
     * @return tubepress_spi_options_OptionDescriptor[]
     */
    private function _lookupOptionDescriptors(array $names)
    {
        /**
         * @var $metas tubepress_spi_options_OptionDescriptor[]
         */
        $toReturn  = array();
        $reference = tubepress_impl_patterns_sl_ServiceLocator::getOptionDescriptorReference();

        foreach ($names as $metaName) {

            $toReturn[$metaName] = $reference->findOneByName($metaName);
        }

        return $toReturn;
    }
}