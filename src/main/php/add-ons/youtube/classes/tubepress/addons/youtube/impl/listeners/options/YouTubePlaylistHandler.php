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
class tubepress_addons_youtube_impl_listeners_options_YouTubePlaylistHandler
{
    public function onPreValidationOptionSet(tubepress_api_event_EventInterface $event)
    {
        $name = $event->getArgument('optionName');

        /** We only care about playlistValue. */
        if ($name !== tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE) {

            return;
        }

        $filteredValue = $this->_maybeGetListValueFromUrl($event->getSubject());
        $filteredValue = $this->_maybeRemoveLeadingPL($filteredValue);

        $event->setSubject($filteredValue);
    }

    private function _maybeRemoveLeadingPL($originalValue)
    {
        if (!tubepress_impl_util_StringUtils::startsWith($originalValue, 'PL')) {

            return $originalValue;
        }

        return tubepress_impl_util_StringUtils::replaceFirst('PL', '', $originalValue);
    }

    private function _maybeGetListValueFromUrl($originalValue)
    {
        $url = null;

        try {

            $url = new ehough_curly_Url($originalValue);

        } catch (Exception $e) {

            return $originalValue;
        }

        $host = $url->getHost();

        if (!tubepress_impl_util_StringUtils::endsWith($host, 'youtube.com')) {

            return $originalValue;
        }

        $params = $url->getQueryVariables();

        if (!array_key_exists('list', $params)) {

            return $originalValue;
        }

        return $params['list'];
    }
}