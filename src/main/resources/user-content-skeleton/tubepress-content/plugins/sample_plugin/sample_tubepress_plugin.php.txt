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
 * This file demonstrates a trivial TubePress plugin. Uncomment the last line in the file to actually
 * activate the plugin.
 */

/**
 * Any PHP object can be registered as a TubePress plugin. The object defines its interaction with the core by
 * 
 *  1. Registering itself via TubePress::registerFilter()
 *  2. Implementing specially named methods on the object.
 *
 */
class SampleTubePressPlugin
{
    /**
     * Since we're registering this object with the 'galleryHtml' filter point,
     * it needs to implement the function alter_galleryHtml($html, $galleryId);

     * @param string $rawHtml   The gallery HTML to filter.
     * @param int    $galleryId The unique gallery ID.
     * 
     * @return string The (possibly modified) HTML.
     */
	public function alter_galleryHtml($rawHtml, $galleryId)
	{
		return "Hello, TubePress! $rawHtml";
	}
}

/**
 * Finally, we register an instance of this class as a filter for the 'galleryHtml' filter point,
 * which allows us to modify TubePress's HTML for a thumbnail galery.
 */
//TubePress::registerFilter('galleryHtml', new SampleTubePressPlugin());
