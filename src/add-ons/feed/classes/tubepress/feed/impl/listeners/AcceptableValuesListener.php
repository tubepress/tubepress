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

class tubepress_feed_impl_listeners_AcceptableValuesListener
{
    /**
     * @var tubepress_spi_media_MediaProviderInterface[]
     */
    private $_mediaProviders;

    public function onMode(tubepress_api_event_EventInterface $event)
    {
        $this->_handle($event, 'getGallerySourceNames');
    }

    public function onOrderBy(tubepress_api_event_EventInterface $event)
    {
        $this->_handle($event, 'getMapOfFeedSortNamesToUntranslatedLabels');
    }

    public function onPerPageSort(tubepress_api_event_EventInterface $event)
    {
        $current = $event->getSubject();

        if (!is_array($current)) {

            $current = array();
        }

        $result = array(
            tubepress_api_options_AcceptableValues::PER_PAGE_SORT_NONE          => 'none',                            //>(translatable)<
            tubepress_api_options_AcceptableValues::PER_PAGE_SORT_COMMENT_COUNT => 'comment count',                   //>(translatable)<
            tubepress_api_options_AcceptableValues::PER_PAGE_SORT_NEWEST        => 'date published (newest first)',   //>(translatable)<
            tubepress_api_options_AcceptableValues::PER_PAGE_SORT_OLDEST        => 'date published (oldest first)',   //>(translatable)<
            tubepress_api_options_AcceptableValues::PER_PAGE_SORT_DURATION      => 'length',                          //>(translatable)<
            tubepress_api_options_AcceptableValues::PER_PAGE_SORT_RANDOM        => 'random',                          //>(translatable)<
            tubepress_api_options_AcceptableValues::PER_PAGE_SORT_TITLE         => 'title',                           //>(translatable)<
            tubepress_api_options_AcceptableValues::PER_PAGE_SORT_VIEW_COUNT    => 'view count',                      //>(translatable)<
        );

        $toSet = array_merge($current, $result);

        $event->setSubject($toSet);
    }

    private function _handle(tubepress_api_event_EventInterface $event, $methodName)
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
