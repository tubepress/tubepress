<?php
/**
 * TubePressCSS.php
 * 
 * Serves as a constant object to hold CSS info
 * 
 * Copyright (C) 2007 Eric D. Hough (http://ehough.com)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class TubePressCSS
{
    var $container, $mainVid_id, $mainVid_class, $meta_class,
        $thumb_container_class, $thumb_class, $thumbImg_class,
        $runtime_class, $title_class, $success_class, $meta_group,
        $pagination, $nextlink, $prevlink;
         
    /**
     * Simple constructor. Nice and easy.
     */
    function TubePressCSS()
    {
        $this->container =             "tubepress_container";
        $this->mainVid_id =            "tubepress_mainvideo";
        $this->mainVid_class =         "tubepress_mainvideo";
        $this->meta_class =            "tubepress_meta";
        $this->thumb_container_class = "tubepress_video_thumbs";
        $this->thumb_class =           "tubepress_thumb";
        $this->thumbImg_class =        "tubepress_video_thumb_img";
        $this->runtime_class =         "tubepress_runtime";
        $this->title_class =           "tubepress_title";
        $this->success_class =         "updated fade";
        $this->meta_group =            "tubepress_meta_group";
        $this->pagination =            "tubepress_pagination";
        $this->nextlink =              "tubepress_next";
        $this->prevlink =              "tubepress_prev";
        $this->failure_class =         "error fade";
        $this->pages =                 "tubepress_pages";
    }
}
?>
