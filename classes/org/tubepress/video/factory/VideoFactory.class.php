<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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
 * Takes a DOMDocument from YouTube and spits back a org_tubepress_video_Video
 */
interface org_tubepress_video_factory_VideoFactory
{
    /**
     * Main method
     *
     * @param DOMElement $rss   The raw XML of what we got from YouTube
     * @param int        $limit The max number of videos to return
     * 
     * @return org_tubepress_video_Video A org_tubepress_video_Video representing this video
     */
    public function dom2TubePressVideoArray($feed, $limit);
}
