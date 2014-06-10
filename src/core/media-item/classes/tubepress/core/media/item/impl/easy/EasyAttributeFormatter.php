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
 */
class tubepress_core_media_item_impl_easy_EasyAttributeFormatter
{
    /**
     * @var tubepress_core_util_api_TimeUtilsInterface
     */
    private $_timeUtils;

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    private $_sourceToDestinationMap = array();

    private $_numbersPrecisionMap = array();

    private $_truncateStringMap = array();

    private $_datesFromUnixTimes = array();

    private $_durationsFromSeconds = array();

    private $_providerName;

    public function __construct(tubepress_core_util_api_TimeUtilsInterface  $timeUtils,
                                tubepress_core_options_api_ContextInterface $context)
    {
        $this->_timeUtils = $timeUtils;
        $this->_context   = $context;
    }

    public function formatNumber($source, $destination, $precision)
    {
        $this->_sourceToDestinationMap[$source] = $destination;
        $this->_numbersPrecisionMap[$source]    = $precision;
    }

    public function truncateString($source, $destination, $optionName)
    {
        $this->_sourceToDestinationMap[$source] = $destination;
        $this->_truncateStringMap[$source]      = $optionName;
    }

    public function formatDateFromUnixTime($source, $destination)
    {
        $this->_sourceToDestinationMap[$source] = $destination;
        $this->_datesFromUnixTimes[]            = $source;
    }

    public function formatDurationFromSeconds($source, $destination)
    {
        $this->_sourceToDestinationMap[$source] = $destination;
        $this->_durationsFromSeconds[]          = $source;
    }

    public function setProviderName($name)
    {
        $this->_providerName = $name;
    }

    public function onNewMediaItem(tubepress_core_event_api_EventInterface $event)
    {
        /**
         * @var $mediaItem tubepress_core_media_item_api_MediaItem
         */
        $mediaItem = $event->getSubject();

        $mediaProvider = $mediaItem->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_PROVIDER);

        if (isset($this->_providerName) && $mediaProvider->getName() !== $this->_providerName) {

            return;
        }

        $this->_handleDurations($mediaItem);
        $this->_handleNumbers($mediaItem);
        $this->_handleStringTruncations($mediaItem);
        $this->_handleDates($mediaItem);
    }

    private function _handleDurations(tubepress_core_media_item_api_MediaItem $mediaItem)
    {
        foreach ($this->_durationsFromSeconds as $secondsAttributeName) {

            if (!$mediaItem->hasAttribute($secondsAttributeName)) {

                continue;
            }

            $seconds   = $mediaItem->getAttribute($secondsAttributeName);
            $formatted = $this->_timeUtils->secondsToHumanTime($seconds);
            $mediaItem->setAttribute($this->_sourceToDestinationMap[$secondsAttributeName], $formatted);
        }
    }

    private function _handleDates(tubepress_core_media_item_api_MediaItem $mediaItem)
    {
        $dateFormat = $this->_context->get(tubepress_core_media_item_api_Constants::OPTION_DATEFORMAT);
        $relative   = $this->_context->get(tubepress_core_media_item_api_Constants::OPTION_RELATIVE_DATES);

        foreach ($this->_datesFromUnixTimes as $unixTimeAttributeName) {

            if (!$mediaItem->hasAttribute($unixTimeAttributeName)) {

                continue;
            }

            $unixTime = $mediaItem->getAttribute($unixTimeAttributeName);
            $formatted = $this->_timeUtils->unixTimeToHumanReadable($unixTime, $dateFormat, $relative);
            $mediaItem->setAttribute($this->_sourceToDestinationMap[$unixTimeAttributeName], $formatted);
        }
    }

    private function _handleNumbers(tubepress_core_media_item_api_MediaItem $mediaItem)
    {
        foreach ($this->_numbersPrecisionMap as $attributeName => $precision) {

            if (!$mediaItem->hasAttribute($attributeName)) {

                continue;
            }

            $formatted = number_format((float) $mediaItem->getAttribute($attributeName), $precision);
            $mediaItem->setAttribute($this->_sourceToDestinationMap[$attributeName], $formatted);
        }
    }

    private function _handleStringTruncations(tubepress_core_media_item_api_MediaItem $mediaItem)
    {
        foreach ($this->_truncateStringMap as $attributeName => $optionName) {

            if (!$mediaItem->hasAttribute($attributeName)) {

                continue;
            }

            $limit = intval($this->_context->get($optionName));

            if ($limit === 0) {

                continue;
            }

            $currentValue = $mediaItem->getAttribute($attributeName);

            if (strlen($currentValue) <= $limit) {

                continue;
            }

            $truncated = substr("$currentValue", 0, $limit) . '...';
            $mediaItem->setAttribute($this->_sourceToDestinationMap[$attributeName], $truncated);
        }
    }
}