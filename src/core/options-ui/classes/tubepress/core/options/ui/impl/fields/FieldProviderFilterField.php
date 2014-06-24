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
class tubepress_core_options_ui_impl_fields_FieldProviderFilterField extends tubepress_core_options_ui_impl_fields_AbstractMultiSelectField
{
    const FIELD_ID = 'field-provider-filter-field';

    /**
     * @var tubepress_core_options_ui_api_FieldProviderInterface[]
     */
    private $_fieldProviders = array();

    public function __construct(tubepress_core_translation_api_TranslatorInterface   $translator,
                                tubepress_core_options_api_PersistenceInterface      $persistence,
                                tubepress_core_http_api_RequestParametersInterface   $requestParams,
                                tubepress_core_event_api_EventDispatcherInterface    $eventDispatcher,
                                tubepress_core_template_api_TemplateFactoryInterface $templateFactory,
                                tubepress_core_options_api_ReferenceInterface        $optionsReference)
    {
        $optionName = tubepress_core_options_ui_api_Constants::OPTION_DISABLED_FIELD_PROVIDERS;

        parent::__construct(

            self::FIELD_ID,
            $translator,
            $persistence,
            $requestParams,
            $eventDispatcher,
            $templateFactory,
            $optionsReference->getUntranslatedLabel($optionName),
            $optionsReference->getUntranslatedDescription($optionName)
        );
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
     * @param tubepress_core_options_ui_api_FieldProviderInterface[] $providers
     */
    public function setFieldProviders(array $providers)
    {
        $this->_fieldProviders = $providers;
    }

    /**
     * @return string[] An array of currently selected values, which may be empty.
     */
    protected function getCurrentlySelectedValues()
    {
        $optionName          = tubepress_core_options_ui_api_Constants::OPTION_DISABLED_FIELD_PROVIDERS;
        $currentHides        = explode(';', $this->getOptionPersistence()->fetch($optionName));
        $providerNameMap      = $this->_getFieldProvidersIdToDisplayNameMap();
        $currentShows        = array();

        foreach ($providerNameMap as $fieldProviderName => $fieldProviderDisplayName) {

            if (! in_array($fieldProviderName, $currentHides)) {

                $currentShows[] = $fieldProviderName;
            }
        }

        return $currentShows;
    }

    /**
     * @return array An associative array of value => translated display names
     */
    protected function getUngroupedTranslatedChoicesArray()
    {
        return $this->_getFieldProvidersIdToDisplayNameMap();
    }

    /**
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    protected function onSubmitAllMissing()
    {
        $providerIds = array_keys($this->_getFieldProvidersIdToDisplayNameMap());
        $newValue       = implode(';', $providerIds);

        return $this->getOptionPersistence()->queueForSave(tubepress_core_options_ui_api_Constants::OPTION_DISABLED_FIELD_PROVIDERS, $newValue);
    }

    /**
     * @param array $values The incoming values for this field.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    protected function onSubmitMixed(array $values)
    {
        $optionName          = tubepress_core_options_ui_api_Constants::OPTION_DISABLED_FIELD_PROVIDERS;
        $allFieldProviderNames = array_keys($this->_getFieldProvidersIdToDisplayNameMap());

        $toHide = array();

        foreach ($allFieldProviderNames as $fieldProviderName) {

            /*
             * They checked the box, which means they want to show it.
             */
            if (in_array($fieldProviderName, $values)) {

                continue;
            }

            /**
             * They don't want to show this provider, so hide it.
             */
            $toHide[] = $fieldProviderName;
        }

        return $this->getOptionPersistence()->queueForSave($optionName, implode(';', $toHide));
    }

    private function _getFieldProvidersIdToDisplayNameMap()
    {
        $toReturn = array();

        foreach ($this->_fieldProviders as $fieldProvider) {

            if (!$fieldProvider->isAbleToBeFilteredFromGui()) {

                continue;
            }

            $toReturn[$fieldProvider->getId()] = $fieldProvider->getTranslatedDisplayName();
        }

        return $toReturn;
    }
}