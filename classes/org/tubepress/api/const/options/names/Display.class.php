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
 * Display option names for the plugin
 *
 */
class org_tubepress_api_const_options_names_Display
{
    //The order in which these constants are declared dictates the
    //order in which they'll be presented on the options page
    const THEME               = 'theme';
    const AJAX_PAGINATION     = 'ajaxPagination';
    const CURRENT_PLAYER_NAME = 'playerLocation';
    const RESULTS_PER_PAGE    = 'resultsPerPage';
    const HQ_THUMBS           = 'hqThumbs';
    const THUMB_HEIGHT        = 'thumbHeight';
    const THUMB_WIDTH         = 'thumbWidth';
    const ORDER_BY            = 'orderBy';
    const PAGINATE_ABOVE      = 'paginationAbove';
    const PAGINATE_BELOW      = 'paginationBelow';
    const RANDOM_THUMBS       = 'randomize_thumbnails';
    const RELATIVE_DATES      = 'relativeDates';
    const DESC_LIMIT          = 'descriptionLimit';
}
