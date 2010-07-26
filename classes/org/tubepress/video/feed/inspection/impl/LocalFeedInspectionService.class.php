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
    || require dirname(__FILE__) . '/../../../../../tubepress_classloader.php';
tubepress_load_classes(array(
    'org_tubepress_video_feed_inspection_FeedInspectionService',
    'org_tubepress_util_FilesystemUtils',
    'org_tubepress_util_LocalVideoUtils'
));

/**
 * Examines the feed a directory
 *
 */
class org_tubepress_video_feed_inspection_impl_LocalFeedInspectionService implements org_tubepress_video_feed_inspection_FeedInspectionService
{
    private $_logPrefix;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_logPrefix = 'Local Feed Inspection';
    }

    /**
     * Determine the total number of videos in this gallery.
     *
     * @param unknown $dir The raw video feed (varies depending on provider)
     *
     * @return integer The total number of videos in this gallery.
     */
    public function getTotalResultCount($dir)
    {
        $baseDir = org_tubepress_util_LocalVideoUtils::getBaseVideoDirectory();
        return sizeof(org_tubepress_util_LocalVideoUtils::findVideos($baseDir . '/' . $dir, $this->_logPrefix));
    }

    /**
     * Determine the number of videos in this gallery page.
     *
     * @param unknown $dir The raw video feed (varies depending on provider)
     *
     * @return integer The number of videos in this gallery page.
     */
    public function getQueryResultCount($dir)
    {
        return $this->getTotalResultCount($dir);
    }
}
