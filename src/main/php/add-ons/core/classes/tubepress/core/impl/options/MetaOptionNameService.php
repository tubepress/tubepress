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
class tubepress_core_impl_options_MetaOptionNameService
{
    const _ = 'tubepress_core_impl_options_MetaOptionNameService';

    /**
     * @var tubepress_core_api_provider_VideoProviderInterface[]
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

                tubepress_core_api_const_options_Names::AUTHOR,
                tubepress_core_api_const_options_Names::CATEGORY,
                tubepress_core_api_const_options_Names::UPLOADED,
                tubepress_core_api_const_options_Names::DESCRIPTION,
                tubepress_core_api_const_options_Names::ID,
                tubepress_core_api_const_options_Names::KEYWORDS,
                tubepress_core_api_const_options_Names::LENGTH,
                tubepress_core_api_const_options_Names::TITLE,
                tubepress_core_api_const_options_Names::URL,
                tubepress_core_api_const_options_Names::VIEWS,
            );
        }

        if (!isset($this->_cachedProvidedMetaOptionNames)) {

            $this->_cachedProvidedMetaOptionNames = array();

            /**
             * @var $videoProvider tubepress_core_api_provider_VideoProviderInterface
             */
            foreach ($this->_videoProviders as $videoProvider) {

                $this->_cachedProvidedMetaOptionNames[$videoProvider->getFriendlyName()] = $videoProvider->getAdditionalMetaNames();
            }
        }
    }
}
