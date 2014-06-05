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
 * Shuffles videos on request.
 */
class tubepress_core_media_provider_impl_listeners_page_PerPageSorter
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_media_provider_api_MediaProviderInterface
     */
    private $_mostCommonProvider;

    /**
     * @var string
     */
    private $_perPageSortOrder;

    public function __construct(tubepress_api_log_LoggerInterface  $logger,
                                tubepress_core_options_api_ContextInterface $context)
    {
        $this->_logger  = $logger;
        $this->_context = $context;
    }

    public function onVideoGalleryPage(tubepress_core_event_api_EventInterface $event)
    {
        $this->_perPageSortOrder = $this->_context->get(tubepress_core_media_provider_api_Constants::OPTION_PER_PAGE_SORT);
        $shouldLog               = $this->_logger->isEnabled();

        /** No sort requested? */
        if ($this->_perPageSortOrder === tubepress_core_media_provider_api_Constants::PER_PAGE_SORT_NONE) {

            if ($shouldLog) {
                
                $this->_logger->debug('Requested per-page sort order is "none". Not applying per-page sorting.');
            }

            return;
        }

        /** Grab a handle to the videos. */
        $videos = $event->getSubject()->getItems();

        if ($this->_perPageSortOrder === tubepress_core_media_provider_api_Constants::PER_PAGE_SORT_RANDOM) {

            if ($shouldLog) {

                $this->_logger->debug('Shuffling videos');
            }

            shuffle($videos);

        } else {

            $videos = $this->_performComplexSort($videos, $shouldLog);
        }

        $videos = array_values($videos);

        /** Modify the feed result. */
        $event->getSubject()->setItems($videos);
    }

    private function _performComplexSort(array $items, $shouldLog)
    {
        $provider = $this->_findMostCommonProviderThatSupportsSort($items);

        if (!$provider) {

            if ($shouldLog) {

                $this->_logger->debug(sprintf('Could not find a provider to sort videos by %s',
                    $this->_perPageSortOrder));
            }

            return $items;
        }

        $this->_mostCommonProvider = $provider;

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Provider "%s" chosen to sort videos by %s',
                $this->_mostCommonProvider->getDisplayName(), $this->_perPageSortOrder));
        }

        @usort($items, array($this, '__providerSort'));

        return $items;
    }

    /**
     * @param array $items
     *
     * @return tubepress_core_media_provider_api_MediaProviderInterface
     */
    private function _findMostCommonProviderThatSupportsSort(array $items)
    {
        $hits                      = array();
        $providerNameToInstanceMap = array();

        /**
         * @var $item tubepress_core_media_item_api_MediaItem
         */
        foreach ($items as $item) {

            /**
             * @var $provider tubepress_core_media_provider_api_MediaProviderInterface
             */
            $provider         = $item->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER);
            $perPageSortNames = array_keys($provider->getMapOfPerPageSortNamesToUntranslatedLabels());
            $providerName     = $provider->getName();

            if (!isset($providerNameToInstanceMap[$providerName])) {

                $providerNameToInstanceMap[$providerName] = $provider;
            }

            if (!in_array($this->_perPageSortOrder, $perPageSortNames)) {

                continue;
            }

            if (!isset($hits[$providerName])) {

                $hits[$providerName] = 0;
            }

            $hits[$providerName] = ($hits[$providerName] + 1);
        }

        if (count($hits) === 0) {

            return null;
        }

        arsort($hits);
        $keys = array_keys($hits);

        $name = $keys[0];

        return $providerNameToInstanceMap[$name];
    }

    public function __providerSort($itemA, $itemB)
    {
        return $this->_mostCommonProvider->compareForPerPageSort($itemA, $itemB, $this->_perPageSortOrder);
    }
}