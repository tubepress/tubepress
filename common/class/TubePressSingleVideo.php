<?php
/**
 * TubePressSingleVideo.php
 * 
 * The gallery generation class
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

class_exists("TubePressOptionsPackage")
    || require("options/TubePressOptionsPackage.php");
class_exists("HTML_Template_IT")
    || require(dirname(__FILE__) . 
        "/../../lib/PEAR/HTML/HTML_Template_IT/IT.php");

class TubePressSingleVideo
{
    function generateHTML($options)
    {
        /* load up the single video template */
        $tpl = new HTML_Template_IT(dirname(__FILE__) . "/../templates");
        $tpl->loadTemplatefile("single_video.tpl.html", true, true);
        if (PEAR::isError($tpl)) {
            return $tpl;
        }
        $video = new TubePressVideo($_GET[TP_PARAM_VID], $options);
        
        $tpl->setVariable('TITLE', $video->getTitle());
        $tpl->parse('bigVideo');
        echo $tpl->get();
    }

}

?>