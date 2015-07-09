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
 *
 */
class tubepress_app_impl_listeners_options_values_FeedOptions
{
    /**
     * @var tubepress_app_api_media_MediaProviderInterface[]
     */
    private $_mediaProviders;

    public function onMode(tubepress_lib_api_event_EventInterface $event)
    {
        $this->_handle($event, 'getGallerySourceNames');
    }

    public function onOrderBy(tubepress_lib_api_event_EventInterface $event)
    {
        $this->_handle($event, 'getMapOfFeedSortNamesToUntranslatedLabels');
    }

    private function _handle(tubepress_lib_api_event_EventInterface $event, $methodName)
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