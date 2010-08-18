<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array(
    'org_tubepress_log_Log',
    'org_tubepress_options_manager_OptionsManager',
    'org_tubepress_util_FilesystemUtils',
    'org_tubepress_uploads_thumbnail_ThumbnailManager',
    'org_tubepress_uploads_UploadsUtils'));

/**
 * Handles uploaded thumbnails.
 *
 */
class org_tubepress_uploads_thumbnail_SimpleThumbnailManager implements org_tubepress_uploads_thumbnail_ThumbnailManager
{
    const LOG_PREFIX = 'Local video thumb factory';
    
    public function getExistingThumbnails($videoId)
    {
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Looking up video thumbnails for %s', $videoId);
        
        $thumbname  = basename(substr($videoId, 0, strlen($videoId) - 4));
        $thumbname  = preg_replace('/[^a-zA-Z0-9]/', '', $thumbname);
        $baseDir    = org_tubepress_uploads_UploadsUtils::getBaseVideoDirectory();
        $galleryDir = org_tubepress_uploads_UploadsUtils::getGalleryNameFromVideoId($videoId);
        
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Thumbnail names will look something like <tt>%s</tt>', $thumbname);
        
        $thumbAbsPaths = array();

        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Looking for existing thumbnails at <tt>%s</tt>', "$baseDir/$galleryDir/generated_thumbnails/");
        
        $files = org_tubepress_util_FilesystemUtils::getFilenamesInDirectory("$baseDir/$galleryDir/generated_thumbnails/",
            self::LOG_PREFIX);

        $toReturn = array();
        foreach ($files as $file) {
            if (strpos($file, $postfix) !== false) {
                org_tubepress_log_Log::log(self::LOG_PREFIX, 'Found a thumbnail we can use at <tt>%s</tt>', realpath($file));
                array_push($toReturn, basename($file));
            }
        }

        return $toReturn;
    }
}
