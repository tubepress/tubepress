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
abstract class tubepress_app_options_ui_impl_fields_AbstractProviderBasedDropdownField extends tubepress_app_options_ui_impl_fields_AbstractMultiSelectField
{
    /**
     * @var tubepress_app_media_provider_api_MediaProviderInterface[]
     */
    private $_mediaProviders;

    public function __construct($fieldId,
                                tubepress_lib_translation_api_TranslatorInterface   $translator,
                                tubepress_app_options_api_PersistenceInterface      $persistence,
                                tubepress_app_http_api_RequestParametersInterface   $requestParams,
                                tubepress_lib_event_api_EventDispatcherInterface    $eventDispatcher,
                                tubepress_lib_template_api_TemplateFactoryInterface $templateFactory,
                                array                                               $mediaProviders,
                                $untranslatedDisplayName = null,
                                $untranslatedDescription = null)
    {
        parent::__construct(

            $fieldId,
            $translator,
            $persistence,
            $requestParams,
            $eventDispatcher,
            $templateFactory,
            $untranslatedDisplayName,
            $untranslatedDescription
        );

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
        $mapOfChoicesToUntranslatedMediaProviderNames  = array();
        $allChoices                                    = $this->getAllChoices();

        foreach ($allChoices as $choice) {

            if (!isset($mapOfChoicesToUntranslatedMediaProviderNames[$choice])) {

                $mapOfChoicesToUntranslatedMediaProviderNames[$choice] = array();
            }

            foreach ($this->_mediaProviders as $mediaProvider) {

                if ($this->providerRecognizesChoice($mediaProvider, $choice)) {

                    $displayName = $mediaProvider->getDisplayName();

                    if (!in_array($displayName, $mapOfChoicesToUntranslatedMediaProviderNames[$choice])) {

                        $mapOfChoicesToUntranslatedMediaProviderNames[$choice][] = $displayName;
                    }
                }
            }
        }

        $middleMap = array();

        foreach ($mapOfChoicesToUntranslatedMediaProviderNames as $choice => $providerDisplayNames) {

            $finalGroupLabel = implode(' / ', $providerDisplayNames);

            if (!isset($middleMap[$finalGroupLabel])) {

                $middleMap[$finalGroupLabel] = array();
            }

            $optionLabel = $this->getUntranslatedLabelForChoice($choice);
            $optionLabel = $this->translate($optionLabel);

            $middleMap[$finalGroupLabel][$choice] = $optionLabel;
        }

        /**
         * Sort within the groups.
         */
        foreach ($middleMap as $finalGroupLabel => $choices) {

            asort($choices);
            $middleMap[$finalGroupLabel] = $choices;
        }

        $sortedLabels = array_keys($middleMap);
        usort($sortedLabels, array($this, '__sortByMostSlashes'));

        $finalMap = array();
        foreach ($sortedLabels as $finalGroupLabel) {

            $finalMap[$finalGroupLabel] = $middleMap[$finalGroupLabel];
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
     * @return tubepress_app_media_provider_api_MediaProviderInterface[]
     */
    protected function getMediaProviders()
    {
        return $this->_mediaProviders;
    }

    protected abstract function providerRecognizesChoice(tubepress_app_media_provider_api_MediaProviderInterface $provider, $choice);

    protected abstract function getAllChoices();

    protected abstract function getUntranslatedLabelForChoice($choice);
}