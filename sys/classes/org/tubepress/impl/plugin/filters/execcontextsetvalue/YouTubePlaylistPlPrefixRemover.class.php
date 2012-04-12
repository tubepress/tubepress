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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_GallerySource',
    'org_tubepress_impl_util_StringUtils',
    'org_tubepress_impl_log_Log'
));

/**
 * Removes "PL" from the start of playlist values.
 */
class org_tubepress_impl_plugin_filters_execcontextsetvalue_YouTubePlaylistPlPrefixRemover
{
    private static $_logPrefix = 'YouTube Playlist PL Prefix Remover';

    /**
     * Filters the HTML for the gallery.
     *
     * @param string $html      The gallery HTML.
     * @param string $galleryId The current gallery ID
     *
     * @return string The modified HTML
     */
    public function alter_preValidationOptionSet($name, $value)
    {
        /** We only care about playlistValue. */
        if ($name !== org_tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE) {

            return;
        }

        if (org_tubepress_impl_util_StringUtils::startsWith($value, 'PL')) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Removing \'PL\' prefix from playlist value of %s', $value);

            return org_tubepress_impl_util_StringUtils::replaceFirst('PL', '', $value);
        }

        org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Playlist value %s does not beging with \'PL\'', $value);

        return $value;
    }
}
