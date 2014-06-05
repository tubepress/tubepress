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
class tubepress_core_embedded_impl_listeners_options_AcceptableValues
{
    /**
     * @var tubepress_core_embedded_api_EmbeddedProviderInterface[]
     */
    private $_embeddedPlayers = array();

    /**
     * @var array
     */
    private $_videoProviders = array();

    public function onAcceptableValues(tubepress_core_event_api_EventInterface $event)
    {
        $current = $event->getSubject();

        if (!is_array($current)) {

            $current = array();
        }

        $event->setSubject(array_merge($current, $this->_getAccepted()));
    }

    public function setEmbeddedProviders(array $embeds)
    {
        $this->_embeddedPlayers = $embeds;
    }

    public function setMediaProviders(array $providers)
    {
        $this->_videoProviders = $providers;
    }

    private function _getAccepted()
    {
        $providerNames = $this->_getValidVideoProviderNames();
        $detected      = array();

        /**
         * @var $embeddedImpl tubepress_core_embedded_api_EmbeddedProviderInterface
         */
        foreach ($this->_embeddedPlayers as $embeddedImpl) {

            /**
             * If the embedded player service's name does not match a registered provider name,
             * it must be non provider based, so let's add it.
             */
            if (! in_array($embeddedImpl->getName(), $providerNames)) {

                $detected[$embeddedImpl->getName()] = $embeddedImpl->getUntranslatedDisplayName();
            }
        }

        asort($detected);

        return array_merge(array(

            tubepress_core_embedded_api_Constants::EMBEDDED_IMPL_PROVIDER_BASED => 'Provider default',  //>(translatable)<

        ), $detected);
    }

    private function _getValidVideoProviderNames()
    {
        $toReturn = array_keys($this->_getValidProviderNamesToFriendlyNames());

        asort($toReturn);

        return $toReturn;
    }

    private function _getValidProviderNamesToFriendlyNames()
    {
        $toReturn = array();

        /**
         * @var $videoProvider tubepress_core_media_provider_api_MediaProviderInterface
         */
        foreach ($this->_videoProviders as $videoProvider) {

            $toReturn[$videoProvider->getName()] = $videoProvider->getDisplayName();
        }

        return $toReturn;
    }
}