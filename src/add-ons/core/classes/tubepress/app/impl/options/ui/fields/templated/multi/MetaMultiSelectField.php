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

/**
 * Displays a multi-select drop-down input for video meta.
 */
class tubepress_app_impl_options_ui_fields_templated_multi_MetaMultiSelectField extends tubepress_app_impl_options_ui_fields_templated_multi_AbstractMultiSelectField implements tubepress_app_impl_options_ui_fields_templated_multi_MediaProviderFieldInterface
{
    const FIELD_ID = 'meta-dropdown';

    /**
     * @var tubepress_app_api_options_ReferenceInterface
     */
    private $_optionsReference;

    /**
     * @var tubepress_app_api_media_MediaProviderInterface[]
     */
    private $_mediaProviders;

    /**
     * @var array
     */
    private $_cachedMetaOptionNames;

    public function __construct(tubepress_app_api_options_PersistenceInterface    $persistence,
                                tubepress_lib_api_http_RequestParametersInterface $requestParams,
                                tubepress_lib_api_template_TemplatingInterface    $templating,
                                tubepress_app_api_options_ReferenceInterface      $optionsReference,
                                array                                             $mediaProviders)
    {
        parent::__construct(

            self::FIELD_ID,
            $persistence,
            $requestParams,
            $templating,
            'Show each video\'s...'     //>(translatable)<
        );

        $this->_mediaProviders   = $mediaProviders;
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

    /**
     * @return array An associative array of translated group names to associative array of
     *               value => untranslated display names
     */
    protected function getGroupedChoicesArray()
    {
        $this->_primeMetaCache();

        return tubepress_app_impl_options_ui_fields_templated_multi_MediaProviderFieldHelper::getGroupedChoicesArray($this);
    }

    private function _getAllMetaOptionNames()
    {
        $this->_primeMetaCache();

        return $this->_cachedMetaOptionNames;
    }

    private function _primeMetaCache()
    {
        if (!isset($this->_cachedMetaOptionNames)) {

            $this->_cachedMetaOptionNames = array();

            foreach ($this->_mediaProviders as $mediaProvider) {

                $this->_cachedMetaOptionNames = array_merge($this->_cachedMetaOptionNames, $mediaProvider->getMapOfMetaOptionNamesToAttributeDisplayNames());
            }

            $this->_cachedMetaOptionNames = array_unique($this->_cachedMetaOptionNames);
            $this->_cachedMetaOptionNames = array_keys($this->_cachedMetaOptionNames);
        }
    }

    /**
     * Gets whether or not this field is TubePress Pro only.
     *
     * @return boolean True if this field is TubePress Pro only. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isProOnly()
    {
        return false;
    }

    /**
     * @return array An associative array of value => untranslated display names
     */
    protected function getUngroupedChoicesArray()
    {
        return array();
    }

    public function getAllChoices()
    {
        return $this->_getAllMetaOptionNames();
    }

    public function getUntranslatedLabelForChoice($choice)
    {
        return $this->_optionsReference->getUntranslatedLabel($choice);
    }

    public function getMediaProviders()
    {
        return $this->_mediaProviders;
    }

    public function providerRecognizesChoice(tubepress_app_api_media_MediaProviderInterface $mp, $choice)
    {
        $metaNames = $mp->getMapOfMetaOptionNamesToAttributeDisplayNames();

        return array_key_exists($choice, $metaNames);
    }
}