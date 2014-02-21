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
 * Performs validation on option values
 */
class tubepress_addons_core_impl_options_MetaOptionNameService
{
    const _ = 'tubepress_addons_core_impl_options_MetaOptionNameService';

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

    public function setVideoProviders(array $providers)
    {
        $this->_videoProviders = $providers;
    }

    public function getAllMetaOptionNames()
    {
        $this->_primeCache();

        $toReturn = $this->_cachedCoreMetaOptionNames;

        foreach ($this->_cachedProvidedMetaOptionNames as $friendlyName => $metaOptionNames) {

            $toReturn = array_merge($toReturn, $metaOptionNames);
        }

        return $toReturn;
    }

    public function getCoreMetaOptionNames()
    {
        $this->_primeCache();

        return $this->_cachedCoreMetaOptionNames;
    }

    public function getMapOfFriendlyProviderNameToMetaOptionNames()
    {
        $this->_primeCache();

        return $this->_cachedProvidedMetaOptionNames;
    }

    private function _primeCache()
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
    }
}
