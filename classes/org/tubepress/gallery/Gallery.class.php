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

/**
 * TubePress gallery. This class gets one or more videos from a provider and applies them to the template.
 */
interface org_tubepress_gallery_Gallery
{
    const FAVORITES        = 'favorites';
    const FEATURED         = 'recently_featured';
    const MOBILE           = 'mobile';
    const MOST_DISCUSSED   = 'most_discussed';
    const MOST_LINKED      = 'most_linked';
    const MOST_RECENT      = 'most_recent';
    const MOST_RESPONDED   = 'most_responded';
    const PLAYLIST         = 'playlist';
    const POPULAR          = 'most_viewed';
    const TAG              = 'tag';
    const TOP_RATED        = 'top_rated';
    const USER             = 'user';
    const VIMEO_UPLOADEDBY = 'vimeoUploadedBy';
    const VIMEO_LIKES      = 'vimeoLikes';
    const VIMEO_APPEARS_IN = 'vimeoAppearsIn';
    const VIMEO_SEARCH     = 'vimeoSearch';
    const VIMEO_CREDITED   = 'vimeoCreditedTo';
    const VIMEO_CHANNEL    = 'vimeoChannel';
    const VIMEO_ALBUM      = 'vimeoAlbum';
    const VIMEO_GROUP      = 'vimeoGroup';

    /**
     * Generates the HTML for TubePress. Could be a gallery or single video.
     *
     * @param string $shortCodeContent The optional shortcode content
     *
     * @return The HTML for TubePress.
     */
    public function getHtml($shortCodeContent = '');
}
