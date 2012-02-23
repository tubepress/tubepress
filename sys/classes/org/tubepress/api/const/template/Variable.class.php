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
 * Public TubePress template variables. These variables are considered
 * safe for use by anyone, so don't mess with this list without serious
 * consideration!
 */
class org_tubepress_api_const_template_Variable
{
    const EMBEDDED_AUTOSTART       = 'autostart';
    const EMBEDDED_COLOR_HIGHLIGHT = 'embeddedColorHightlight';
    const EMBEDDED_COLOR_PRIMARY   = 'embeddedColorPrimary';
    const EMBEDDED_DATA_URL        = 'homeURL';
    const EMBEDDED_FULLSCREEN      = 'embeddedFullscreen';
    const EMBEDDED_HEIGHT          = 'embeddedHeight';
    const EMBEDDED_IMPL_NAME       = 'embeddedImplementationName';
    const EMBEDDED_SOURCE          = 'embeddedSource';
    const EMBEDDED_WIDTH           = 'embeddedWidth';
    const GALLERY_ID               = 'galleryId';
    const META_LABELS              = 'metaLabels';
    const META_SHOULD_SHOW         = 'shouldShow';
    const PAGINATION_BOTTOM        = 'bottomPagination';
    const PAGINATION_TOP           = 'topPagination';
    const PLAYER_HTML              = 'preGallery';
    const PLAYER_NAME              = 'playerName';
    const SEARCH_HANDLER_URL       = 'searchHandlerUrl';
    const SEARCH_HIDDEN_INPUTS     = 'searchHiddenInputs';
    const SEARCH_BUTTON            = 'searchButton';
    const SEARCH_TARGET_DOM_ID     = 'searchTargetDomId';
    const SEARCH_TERMS             = 'searchTerms';
    const SHORTCODE                = 'shortcode';
    const THUMBNAIL_HEIGHT         = 'thumbHeight';
    const THUMBNAIL_WIDTH          = 'thumbWidth';
    const TUBEPRESS_BASE_URL       = 'tubepressBaseUrl';
    const VIDEO_ARRAY              = 'videoArray';
    const VIDEO                    = 'video';
    const VIDEO_ID                 = 'videoId';
}

class org_tubepress_template_Template extends org_tubepress_api_const_template_Variable {
	//the only purpose of this class is for backwards compatibility with user HTML templates :(
}
