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

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_gallery_AbstractGallery'));

/**
 * Regular TubePress gallery
 */
class org_tubepress_gallery_Gallery extends org_tubepress_gallery_AbstractGallery
{
    const FAVORITES       = "favorites";
    const FEATURED        = "recently_featured";
    const MOBILE          = "mobile";
    const MOST_DISCUSSESD = "most_discussed";
    const MOST_LINKED     = "most_linked";
    const MOST_RECENT     = "most_recent";
    const MOST_RESPONDED  = "most_responded";
    const PLAYLIST        = "playlist";
    const POPULAR         = "most_viewed";
    const TAG             = "tag";
    const TOP_RATED       = "top_rated";
    const USER            = "user";
    
    /**
     * Generates the content of this gallery
     * 
     * @return The HTML content for this gallery
     */
    public final function generate($galleryId)
    {
        try {
            $this->setTemplateDirectory(dirname(__FILE__) . "/../../../../ui/gallery/html_templates");
             return $this->generateThumbs($galleryId);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
