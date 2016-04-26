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
class tubepress_options_ui_impl_fields_templated_multi_MetaMultiSelectField extends tubepress_options_ui_impl_fields_templated_multi_AbstractMultiSelectField implements tubepress_options_ui_impl_fields_templated_multi_MediaProviderFieldInterface
{
    const FIELD_ID = 'meta-dropdown';

    /**
     * @var tubepress_api_options_ReferenceInterface
     */
    private $_optionsReference;

    /**
     * @var tubepress_spi_media_MediaProviderInterface[]
     */
    private $_mediaProviders;

    /**
     * @var array
     */
    private $_cachedMetaOptionNames;

    public function __construct(tubepress_api_options_PersistenceInterface    $persistence,
                                tubepress_api_http_RequestParametersInterface $requestParams,
                                tubepress_api_template_TemplatingInterface    $templating,
                                tubepress_api_options_ReferenceInterface      $optionsReference,
                                array                                         $mediaProviders)
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    protected function getGroupedChoicesArray()
    {
        $this->_primeMetaCache();

        return tubepress_options_ui_impl_fields_templated_multi_MediaProviderFieldHelper::getGroupedChoicesArray($this);
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
     * {@inheritdoc}
     */
    public function isProOnly()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function getUngroupedChoicesArray()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllChoices()
    {
        return $this->_getAllMetaOptionNames();
    }

    /**
     * {@inheritdoc}
     */
    public function getUntranslatedLabelForChoice($choice)
    {
        return $this->_optionsReference->getUntranslatedLabel($choice);
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaProviders()
    {
        return $this->_mediaProviders;
    }

    /**
     * {@inheritdoc}
     */
    public function providerRecognizesChoice(tubepress_spi_media_MediaProviderInterface $mp, $choice)
    {
        $metaNames = $mp->getMapOfMetaOptionNamesToAttributeDisplayNames();

        return array_key_exists($choice, $metaNames);
    }
}
