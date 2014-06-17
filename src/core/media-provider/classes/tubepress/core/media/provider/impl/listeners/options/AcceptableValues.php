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
class tubepress_core_media_provider_impl_listeners_options_AcceptableValues
{
    /**
     * @var tubepress_core_media_provider_api_MediaProviderInterface[]
     */
    private $_mediaProviders;

    public function onMode(tubepress_core_event_api_EventInterface $event)
    {
        $this->_handle($event, 'getGallerySourceNames');
    }

    public function onOrderBy(tubepress_core_event_api_EventInterface $event)
    {
        $this->_handle($event, 'getMapOfFeedSortNamesToUntranslatedLabels');
    }

    public function onPerPageSort(tubepress_core_event_api_EventInterface $event)
    {
        $this->_handle($event, 'getMapOfPerPageSortNamesToUntranslatedLabels');
    }

    private function _handle(tubepress_core_event_api_EventInterface $event, $methodName)
    {
        $current = $event->getSubject();

        if (!is_array($current)) {

            $current = array();
        }

        foreach ($this->_mediaProviders as $provider) {

            $current = array_merge($current, $provider->$methodName());
        }

        $event->setSubject($current);
    }

    public function setMediaProviders(array $providers)
    {
        $this->_mediaProviders = $providers;
    }
}