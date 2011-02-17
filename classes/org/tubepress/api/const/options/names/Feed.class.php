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
 * YouTube feed options
 */
class org_tubepress_api_const_options_names_Feed
{
    /* The order in which these constants are declared dictates the
       order in which they'll be presented on the options page */
    const CACHE_ENABLED    = 'cacheEnabled';
    const EMBEDDABLE_ONLY  = 'embeddableOnly';
    const FILTER           = 'filter_racy';
    const DEV_KEY          = 'developerKey';
    const RESULT_COUNT_CAP = 'resultCountCap';
    const SEARCH_ONLY_USER = 'searchResultsRestrictedToUser';
    const VIMEO_KEY        = 'vimeoKey';
    const VIMEO_SECRET     = 'vimeoSecret';
}
