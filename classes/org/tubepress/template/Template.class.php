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
 * TubePress template
 */
interface org_tubepress_template_Template
{
    const AUTOSTART = 'autostart';
    const GALLERY_ID = 'galleryId';
    const EMBEDDED_IMPL_NAME = 'embeddedImplementationName';
    const EMBEDDED_SOURCE = 'embeddedSource';
    const EMBEDDED_WIDTH = 'embeddedWidth';
    const EMBEDDED_HEIGHT = 'embeddedHeight';
    const HOME_URL = 'homeURL';
    const META_SHOULD_SHOW = 'shouldShow';
    const META_LABELS = 'metaLabels';
    const OPTIONS_PAGE_TITLE = 'optionsPageTitle';
    const OPTIONS_PAGE_INTRO = 'optionsPageIntro';
    const OPTIONS_PAGE_DONATION = 'optionsPageDonation';
    const OPTIONS_PAGE_SAVE = 'optionsPageSave';
    const OPTIONS_PAGE_CATEGORIES = 'optionsPageCategories';
    const PRE_GALLERY = 'preGallery';
    const PAGINATION_BOTTOM = 'bottomPagination';
    const PAGINATION_TOP = 'topPagination';
    const PLAYER_NAME = 'playerName';
    const SHORTCODE = 'shortcode';
    const THUMBNAIL_WIDTH = 'thumbWidth';
    const THUMBNAIL_HEIGHT = 'thumbHeight';
    const TUBEPRESS_BASE_URL = 'tubepressBaseUrl';
    const VIDEO = 'video';
    const VIDEO_ARRAY = 'videoArray';
    const WIDGET_CONTROL_TITLE = 'widgetControlTitle';
    const WIDGET_TITLE = 'widgetTitle';
    const WIDGET_CONTROL_SHORTCODE = 'widgetControlShortcode';
    const WIDGET_SHORTCODE = 'widgetShortcode';
    
    public function toString();
    
    public function setPath($path);
    
    public function setVariable($name, $value);
}
