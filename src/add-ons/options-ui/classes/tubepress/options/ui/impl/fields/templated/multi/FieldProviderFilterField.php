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

/**
 * Displays a multi-select drop-down input for video meta.
 */
class tubepress_options_ui_impl_fields_templated_multi_FieldProviderFilterField extends tubepress_options_ui_impl_fields_templated_multi_AbstractMultiSelectField
{
    const FIELD_ID = 'provider_filter_field';

    /**
     * @var tubepress_spi_options_ui_FieldProviderInterface[]
     */
    private $_fieldProviders = array();

    public function __construct(tubepress_api_options_PersistenceInterface    $persistence,
                                tubepress_api_http_RequestParametersInterface $requestParams,
                                tubepress_api_template_TemplatingInterface    $templating,
                                tubepress_api_options_ReferenceInterface      $optionsReference)
    {
        $optionName = tubepress_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS;

        parent::__construct(

            self::FIELD_ID,
            $persistence,
            $requestParams,
            $templating,
            $optionsReference->getUntranslatedLabel($optionName),
            $optionsReference->getUntranslatedDescription($optionName)
        );
    }

    /**
     * @param tubepress_spi_options_ui_FieldProviderInterface[] $providers
     */
    public function setFieldProviders(array $providers)
    {
        $this->_fieldProviders = $providers;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCurrentlySelectedValues()
    {
        $optionName      = tubepress_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS;
        $currentHides    = explode(';', $this->getOptionPersistence()->fetch($optionName));
        $providerNameMap = $this->_getFieldProvidersIdToDisplayNameMap();
        $currentShows    = array();

        foreach ($providerNameMap as $fieldProviderName => $fieldProviderDisplayName) {

            if (!in_array($fieldProviderName, $currentHides)) {

                $currentShows[] = $fieldProviderName;
            }
        }

        return $currentShows;
    }

    /**
     * {@inheritdoc}
     */
    protected function getUngroupedChoicesArray()
    {
        return $this->_getFieldProvidersIdToDisplayNameMap();
    }

    /**
     * {@inheritdoc}
     */
    protected function onSubmitAllMissing()
    {
        $providerIds = array_keys($this->_getFieldProvidersIdToDisplayNameMap());
        $newValue    = implode(';', $providerIds);

        return $this->getOptionPersistence()->queueForSave(tubepress_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS, $newValue);
    }

    /**
     * {@inheritdoc}
     */
    protected function onSubmitMixed(array $values)
    {
        $optionName            = tubepress_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS;
        $allFieldProviderNames = array_keys($this->_getFieldProvidersIdToDisplayNameMap());

        $toHide = array();

        foreach ($allFieldProviderNames as $fieldProviderName) {

            /*
             * They checked the box, which means they want to show it.
             */
            if (in_array($fieldProviderName, $values)) {

                continue;
            }

            /*
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

            $toReturn[$fieldProvider->getId()] = $fieldProvider->getUntranslatedDisplayName();
        }

        return $toReturn;
    }

    /**
     * {@inheritdoc}
     */
    public function isProOnly()
    {
        return false;
    }
}
