<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Removes "PL" from the start of playlist values.
 */
class tubepress_plugins_core_impl_filters_prevalidationoptionset_YouTubePlaylistPlPrefixRemover
{
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('YouTube Playlist PL Prefix Remover');
    }


    public final function onPreValidationOptionSet(tubepress_api_event_TubePressEvent $event)
    {
        $name = $event->getArgument('optionName');

        /** We only care about playlistValue. */
        if ($name !== tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE) {

            return;
        }

        $value = $event->getSubject();

        if (! is_string($value)) {

            return;
        }

        if (tubepress_impl_util_StringUtils::startsWith($value, 'PL')) {

            if ($this->_logger->isDebugEnabled()) {

                $this->_logger->debug(sprintf('Removing \'PL\' prefix from playlist value of %s', $value));
            }

            $newValue = tubepress_impl_util_StringUtils::replaceFirst('PL', '', $value);

            $event->setSubject($newValue);
        }

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Playlist value %s does not beging with \'PL\'', $value));
        }
    }
}
