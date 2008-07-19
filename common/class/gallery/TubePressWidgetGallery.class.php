<?php
/**
 * Copyright 2006, 2007, 2008 Eric D. Hough (http://ehough.com)
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
 * Widget galleries
 */
class TubePressWidgetGallery extends AbstractTubePressGallery
{
    /**
     * Generates the content of this gallery
     * 
     * @param TubePressOptionsManager $tpom The TubePress options 
     *        manager containing all the user's options
     * 
     * @return The HTML content for this gallery
     */
    public static final function generate(TubePressOptionsManager $tpom)
    {
        return parent::generateThumbs("widget_gallery.tpl.html", $tpom);   
    }
}