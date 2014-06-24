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
class tubepress_core_options_ui_impl_fields_MetaMultiSelectField extends tubepress_core_options_ui_impl_fields_AbstractMultiSelectField
{
    const FIELD_ID = 'meta-dropdown';

    /**
     * @var
     */
    private $_optionsReference;

    /**
     * @var tubepress_core_media_provider_api_MediaProviderInterface[]
     */
    private $_mediaProviders;

    public function __construct(tubepress_core_translation_api_TranslatorInterface   $translator,
                                tubepress_core_options_api_PersistenceInterface      $persistence,
                                tubepress_core_http_api_RequestParametersInterface   $requestParams,
                                tubepress_core_event_api_EventDispatcherInterface    $eventDispatcher,
                                tubepress_core_template_api_TemplateFactoryInterface $templateFactory,
                                tubepress_core_options_api_ReferenceInterface        $optionsReference,
                                array                                                $mediaProviders)
    {
        parent::__construct(

            self::FIELD_ID,
            $translator,
            $persistence,
            $requestParams,
            $eventDispatcher,
            $templateFactory,
            'Show each video\'s...'     //>(translatable)<
        );

        $this->_optionsReference = $optionsReference;
        $this->_mediaProviders   = $mediaProviders;
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

    /**
     * @return string[] An array of currently selected values, which may be empty.
     */
    protected function getCurrentlySelectedValues()
    {
        $metaNames = $this->_getAllMetaOptionNames();
        $toReturn  = array();

        foreach ($metaNames as $metaName) {

            if ($this->getOptionPersistence()->fetch($metaName)) {

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
        return array();
    }

    /**
     * @return array An associative array of translated group names to associative array of
     *               value => translated display names
     */
    protected function getGroupedTranslatedChoicesArray()
    {
        $tempMap  = array();
        $allNames = $this->_getAllMetaOptionNames();
        foreach ($allNames as $metaOptionName) {

            if (!isset($tempMap[$metaOptionName])) {

                $tempMap[$metaOptionName] = array();
            }

            foreach ($this->_mediaProviders as $mediaProvider) {

                if (in_array($metaOptionName, array_keys($mediaProvider->getMapOfMetaOptionNamesToAttributeDisplayNames()))) {

                    $displayName = $mediaProvider->getDisplayName();
                    if (!in_array($displayName, $tempMap[$metaOptionName])) {

                        $tempMap[$metaOptionName][] = $displayName;
                    }
                }
            }
        }

        $middleMap = array();
        foreach ($tempMap as $metaOptionName => $providerDisplayNames) {

            $label = implode(' / ', $providerDisplayNames);
            if (!isset($middleMap[$label])) {
                $middleMap[$label] = array();
            }
            $optionLabel = $this->_optionsReference->getUntranslatedLabel($metaOptionName);
            $optionLabel = $this->translate($optionLabel);
            $middleMap[$label][$metaOptionName] = $optionLabel;
        }

        /**
         * Sort within the groups.
         */
        foreach ($middleMap as $label => $choices) {

            asort($choices);
            $middleMap[$label] = $choices;
        }

        $sortedLabels = array_keys($middleMap);
        usort($sortedLabels, array($this, '__sortByMostSlashes'));

        $finalMap = array();
        foreach ($sortedLabels as $label) {

            $finalMap[$label] = $middleMap[$label];
        }

        return $finalMap;
    }

    public function __sortByMostSlashes($a, $b)
    {
        $aCount = substr_count($a, '/');
        $bCount = substr_count($b, '/');

        if ($aCount > $bCount) {

            return -1;
        }

        if ($aCount === $bCount) {

            return 0;
        }

        return 1;
    }

    /**
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    protected function onSubmitAllMissing()
    {
        $optionNames = $this->_getAllMetaOptionNames();

        //they unchecked everything
        foreach ($optionNames as $optionName) {

            $message = $this->getOptionPersistence()->queueForSave($optionName, false);

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
        $optionNames = $this->_getAllMetaOptionNames();

        foreach ($optionNames as $optionName) {

            $message = $this->getOptionPersistence()->queueForSave($optionName, in_array($optionName, $values));

            if ($message !== null) {

                return $message;
            }
        }

        return null;
    }

    private function _getAllMetaOptionNames()
    {
        $toReturn = array();

        foreach ($this->_mediaProviders as $mediaProvider) {

            $toReturn = array_merge($toReturn, array_keys($mediaProvider->getMapOfMetaOptionNamesToAttributeDisplayNames()));
        }

        return array_unique($toReturn);
    }
}