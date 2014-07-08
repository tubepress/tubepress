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
class tubepress_app_options_ui_impl_fields_MetaMultiSelectField extends tubepress_app_options_ui_impl_fields_AbstractProviderBasedDropdownField
{
    const FIELD_ID = 'meta-dropdown';

    /**
     * @var
     */
    private $_optionsReference;


    public function __construct(tubepress_lib_translation_api_TranslatorInterface   $translator,
                                tubepress_app_options_api_PersistenceInterface      $persistence,
                                tubepress_app_http_api_RequestParametersInterface   $requestParams,
                                tubepress_lib_event_api_EventDispatcherInterface    $eventDispatcher,
                                tubepress_lib_template_api_TemplateFactoryInterface $templateFactory,
                                tubepress_app_options_api_ReferenceInterface        $optionsReference,
                                array                                               $mediaProviders)
    {
        parent::__construct(

            self::FIELD_ID,
            $translator,
            $persistence,
            $requestParams,
            $eventDispatcher,
            $templateFactory,
            $mediaProviders,
            'Show each video\'s...'     //>(translatable)<
        );

        $this->_optionsReference = $optionsReference;
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

        foreach ($this->getMediaProviders() as $mediaProvider) {

            $toReturn = array_merge($toReturn, array_keys($mediaProvider->getMapOfMetaOptionNamesToAttributeDisplayNames()));
        }

        return array_unique($toReturn);
    }

    protected function getAllChoices()
    {
        return $this->_getAllMetaOptionNames();
    }

    protected function providerRecognizesChoice(tubepress_app_media_provider_api_MediaProviderInterface $provider, $choice)
    {
        return in_array($choice, array_keys($provider->getMapOfMetaOptionNamesToAttributeDisplayNames()));
    }

    protected function getUntranslatedLabelForChoice($choice)
    {
        return $this->_optionsReference->getUntranslatedLabel($choice);
    }
}