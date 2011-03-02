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
 * The types of options TubePress can handle
 *
 */
class org_tubepress_api_const_options_Type
{
    const BOOL        = 'boolean';              // Yes/No options
    const COLOR       = 'color';                // An HTML color (6 hex digits)
    const INTEGRAL    = 'integral';             // A number
    const MODE        = 'mode';                 // A gallery mode
    const ORDER       = 'order';                // Video sort order
    const OUTPUT      = 'output';               // Output mode
    const PLAYER      = 'player';               // Shadowbox, popup, etc
    const PLAYER_IMPL = 'playerImplementation'; // YouTube, Longtail, etc
    const PLAYLIST    = 'playlist';             // A YouTube playlist ID
    const SAFE_SEARCH = 'safeSearch';           // a SafeSearch level
    const TEXT        = 'text';                 // Free form text
    const THEME       = 'theme';                // TubePress theme to use
    const TIME_FRAME  = 'timeFrame';            // Today, last week, etc
    const YT_USER     = 'youtubeUser';          // A YouTube username
}
