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
class tubepress_core_impl_options_ui_fields_MetaMultiSelectField extends tubepress_core_impl_options_ui_fields_AbstractMultiSelectField
{
    const FIELD_ID = 'meta-dropdown';

    /**
     * @var
     */
    private $_optionProvider;

    /**
     * @var tubepress_core_impl_options_MetaOptionNameService
     */
    private $_metaOptionNameService;

    public function __construct(tubepress_core_api_translation_TranslatorInterface   $translator,
                                tubepress_core_api_options_PersistenceInterface      $persistence,
                                tubepress_core_api_http_RequestParametersInterface   $requestParams,
                                tubepress_core_api_event_EventDispatcherInterface    $eventDispatcher,
                                tubepress_core_api_template_TemplateFactoryInterface $templateFactory,
                                tubepress_core_api_options_ProviderInterface         $optionProvider,
                                tubepress_core_impl_options_MetaOptionNameService    $metaOptionNameService)
    {
        parent::__construct(

            self::FIELD_ID,
            $translator,
            $persistence,
            $requestParams,
            $eventDispatcher,
            $templateFactory,
            'Show each video\'s...'
        );

        $this->_metaOptionNameService = $metaOptionNameService;
        $this->_optionProvider        = $optionProvider;
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
        $coreMetaOptionNames = $this->_metaOptionNameService->getCoreMetaOptionNames();

        return $this->_labelAndAssociate($coreMetaOptionNames);
    }

    /**
     * @return array An associative array of translated group names to associative array of
     *               value => translated display names
     */
    protected function getGroupedTranslatedChoicesArray()
    {
        $toReturn = array();
        $map      = $this->_metaOptionNameService->getMapOfFriendlyProviderNameToMetaOptionNames();

        foreach ($map as $friendlyName => $metaOptionNames) {

            $values                  = $this->_labelAndAssociate($metaOptionNames);
            $toReturn[$friendlyName] = $values;
        }

        ksort($toReturn);

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
        return $this->_metaOptionNameService->getAllMetaOptionNames();
    }

    private function _labelAndAssociate($metaOptionNames)
    {
        foreach ($metaOptionNames as $metaOptionName) {

            $label                   = $this->_optionProvider->getLabel($metaOptionName);
            $values[$metaOptionName] = $this->translate($label);
        }

        asort($values);

        return $values;
    }
}