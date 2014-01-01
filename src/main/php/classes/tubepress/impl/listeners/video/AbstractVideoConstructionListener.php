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
 * Base class providing support to video construction filters.
 */
abstract class tubepress_impl_listeners_video_AbstractVideoConstructionListener
{
    public function onVideoConstruction(tubepress_api_event_EventInterface $event)
    {
        $video = $event->getSubject();

        /*
         * Short circuit for videos belonging to someone else.
         */
        if ($video->getAttribute(tubepress_api_video_Video::ATTRIBUTE_PROVIDER_NAME) !== $this->getHandledProviderName()) {

            return;
        }

        $attributeMap = $this->buildAttributeMap($event);

        foreach ($attributeMap as $attributeName => $attributeValue) {

            $video->setAttribute($attributeName, $attributeValue);
        }
    }

    /**
     * Build a map of attribute names => attribute values for the video construction event.
     *
     * @param tubepress_api_event_EventInterface $event The video construction event.
     *
     * @return array An associative array of attribute names => attribute values
     */
    protected abstract function buildAttributeMap(tubepress_api_event_EventInterface $event);

    /**
     * @return string The name of the provider that this filter handles.
     */
    protected abstract function getHandledProviderName();

    /**
     * Optionally trims the description.
     *
     * @param string $description The incoming description.
     *
     * @return string The optionally trimmed description.
     */
    protected final function trimDescription($description)
    {
        $context = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $limit   = $context->get(tubepress_api_const_options_names_Meta::DESC_LIMIT);

        if ($limit > 0 && strlen($description) > $limit) {

            $description = substr($description, 0, $limit) . '...';
        }

        return $description;
    }

    /**
     * Given a unix time, return a human-readable version.
     *
     * @param mixed $unixTime The given unix time.
     *
     * @return string A human readable time.
     */
    protected final function unixTimeToHumanReadable($unixTime)
    {
        if ($unixTime == '') {

            return '';
        }

        $context = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();

        if ($context->get(tubepress_api_const_options_names_Meta::RELATIVE_DATES)) {

            return tubepress_impl_util_TimeUtils::getRelativeTime($unixTime);
        }

        return @date($context->get(tubepress_api_const_options_names_Meta::DATEFORMAT), $unixTime);
    }

    /**
     * Choose a thumbnail URL for the video.
     *
     * @param array $urls An array of URLs from which to choose.
     *
     * @return string A single thumbnail URL.
     */
    protected final function pickThumbnailUrl($urls)
    {
        if (! is_array($urls) || sizeof($urls) == 0) {

            return '';
        }

        $context = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $random  = $context->get(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS);

        if ($random) {

            return $urls[array_rand($urls)];

        } else {

            return $urls[0];
        }
    }

    /**
     * Builds a "fancy" number for the given number.
     *
     * @param mixed $num The candidate.
     *
     * @return string A formatted number, or "N/A" if non-numeric.
     */
    protected final function fancyNumber($num)
    {
        if (! is_numeric($num)) {

            return 'N/A';
        }

        return number_format($num);
    }
}