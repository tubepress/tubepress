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
 * The types of options TubePress can handle
 *
 */
class org_tubepress_options_Type
{
    /* Yes/No options */
    const BOOL     = "boolean";

    /* An HTML color (6 hex digits) */
    const COLOR    = "color";	
	
    /* A number */
	const INTEGRAL = "integral";
	
	/* A gallery mode */
	const MODE = "mode";
	
	/* Video sort order */
	const ORDER     = "order";

	/* Shadowbox, popup, etc */
	const PLAYER    = "player";
	
    /* A YouTube playlist ID */
	const PLAYLIST = "playlist";
	
	/* Video quality */
	const QUALITY = "quality";

    /* a SafeSearch level */
    const SAFE_SEARCH = "safeSearch";

    /* Free form text */	
	const TEXT     = "text";

	/* Today, last week, etc */
	const TIME_FRAME = "timeFrame";
	
    /* A YouTube username */
	const YT_USER  = "youtubeUser";
}
