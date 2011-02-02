<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
interface org_tubepress_api_template_Template
{
    const AUTHOR_URL_PREFIX             = 'authorUrlPrefix';
    const EMBEDDED_AUTOSTART            = 'autostart';
    const EMBEDDED_COLOR_HIGHLIGHT      = 'embeddedColorHightlight';
    const EMBEDDED_COLOR_PRIMARY        = 'embeddedColorPrimary';
    const EMBEDDED_DATA_URL             = 'homeURL';
    const EMBEDDED_FULLSCREEN           = 'embeddedFullscreen';
    const EMBEDDED_HEIGHT               = 'embeddedHeight';
    const EMBEDDED_IMPL_NAME            = 'embeddedImplementationName';
    const EMBEDDED_SOURCE               = 'embeddedSource';
    const EMBEDDED_WIDTH                = 'embeddedWidth';
    const GALLERY_ID                    = 'galleryId';
    const META_LABELS                   = 'metaLabels';
    const META_SHOULD_SHOW              = 'shouldShow';
    const OPTIONS_PAGE_CATEGORIES       = 'optionsPageCategories';
    const OPTIONS_PAGE_CATEGORY_OPTIONS = 'optionsPageCategoryOptions';
    const OPTIONS_PAGE_CATEGORY_TITLE   = 'optionsPageCategoryTitle';
    const OPTIONS_PAGE_DONATION         = 'optionsPageDonation';
    const OPTIONS_PAGE_INTRO            = 'optionsPageIntro';
    const OPTIONS_PAGE_OPTIONS_DESC     = 'optionsPageOptionsDesc';
    const OPTIONS_PAGE_OPTIONS_PRO_ONLY = 'optionsPageProOnly';
    const OPTIONS_PAGE_OPTIONS_TITLE    = 'optionsPageOptionTitle';
    const OPTIONS_PAGE_OPTIONS_WIDGET   = 'optionsPageOptionsWidget';
    const OPTIONS_PAGE_SAVE             = 'optionsPageSave';
    const OPTIONS_PAGE_TITLE            = 'optionsPageTitle';
    const OPTIONS_PAGE_VIMEO_OPTION     = 'optionsPageVimeoOption';
    const OPTIONS_PAGE_YOUTUBE_OPTION   = 'optionsPageYouTubeOption';
    const PAGINATION_BOTTOM             = 'bottomPagination';
    const PAGINATION_TOP                = 'topPagination';
    const PLAYER_HTML                   = 'preGallery';
    const PLAYER_NAME                   = 'playerName';
    const PRE_GALLERY                   = self::PLAYER_HTML;
    const SHORTCODE                     = 'shortcode';
    const THEME_CSS                     = 'themeCssUrl';
    const THUMBNAIL_HEIGHT              = 'thumbHeight';
    const THUMBNAIL_WIDTH               = 'thumbWidth';
    const TUBEPRESS_BASE_URL            = 'tubepressBaseUrl';
    const VIDEO_ARRAY                   = 'videoArray';
    const VIDEO_SEARCH_PREFIX           = 'videoSearchPrefix';
    const VIDEO                         = 'video';
    const WIDGET_CONTROL_SHORTCODE      = 'widgetControlShortcode';
    const WIDGET_CONTROL_TITLE          = 'widgetControlTitle';
    const WIDGET_SHORTCODE              = 'widgetShortcode';
    const WIDGET_TITLE                  = 'widgetTitle';

    /**
     * Converts this template to a string
     *
     * @return string The string representation of this template.
     */
    function toString();

    /**
     * Sets the path of this template.
     *
     * @param string $path The absolute path on the filesystem of this template.
     *
     * @return void
     */
    function setPath($path);

    /**
     * Set a template variable.
     *
     * @param string  $name  The name of the template variable to set.
     * @param unknown $value The value of the template variable.
     *
     * @return void
     */
    function setVariable($name, $value);

    /**
     * Resets this template for use. Clears out any set variables.
     *
     * @return void
     */
    function reset();
}
