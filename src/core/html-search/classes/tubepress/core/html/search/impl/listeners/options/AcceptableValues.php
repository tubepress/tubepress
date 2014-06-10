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
class tubepress_core_html_search_impl_listeners_options_AcceptableValues
{
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

    public function setMediaProviders(array $providers)
    {
        $this->_videoProviders = $providers;
    }

    private function _getAccepted()
    {
        return $this->_getValidProviderNamesToDisplayNames();
    }

    private function _getValidProviderNamesToDisplayNames()
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