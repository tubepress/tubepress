<?php
/**
 * defines.php
 * 
 * Defines lots of constants that we use in the plugin. These are NOT
 * messages that the user will read.
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

/*****************************************************************************/
/*** FIXME *******************************************************************/
/*****************************************************************************/
define("TP_YOUTUBEDEVLINK", "http://www.youtube.com/my_profile_dev");
define("TP_YOUTUBE_RESTURL", "http://www.youtube.com/api2_rest?");
define("TP_PAGE_PARAM", "tubepress_page");
define("TP_VID_PARAM", "tubepress_id");
define("TP_DEBUG_PARAM", "tubepress_debug");
define("TP_OPTION_NAME", "tubepress");
define("TP_DEBUG_ON", "debugging_enabled");

/*****************************************************************************/
/*** SEARCHING MODES *********************************************************/
/*****************************************************************************/
define("TP_SRCH_FAV", "favorites");
define("TP_SRCH_TAG", "tag");
define("TP_SRCH_REL", "related");
define("TP_SRCH_USER", "user");
define("TP_SRCH_PLST", "playlist");
define("TP_SRCH_FEATURED", "featured");
define("TP_SRCH_POPULAR", "popular");
define("TP_SRCH_CATEGORY", "category");

/*****************************************************************************/
/*** SEARCHING VALUES ********************************************************/
/*****************************************************************************/
define("TP_SRCH_TAGVAL", "tagValue");
define("TP_SRCH_RELVAL", "relatedValue");
define("TP_SRCH_USERVAL", "userValue");
define("TP_SRCH_PLSTVAL", "playlistValue");
define("TP_SRCH_POPVAL", "popularValue");
define("TP_SRCH_FAVVAL", "favoritesValue");
define("TP_SRCH_CATVAL", "categoryValue");

/*****************************************************************************/
/*** PLAYER LOCATIONS ********************************************************/
/*****************************************************************************/
define("TP_PLAYIN_NW", "new_window");
define("TP_PLAYIN_YT", "youtube");
define("TP_PLAYIN_NORMAL", "normal");
define("TP_PLAYIN_POPUP", "popup");
define("TP_PLAYIN_THICKBOX", "thickbox");
define("TP_PLAYIN_LWINDOW", "lightwindow");

/*****************************************************************************/
/*** FIX ME ******************************************************************/
/*****************************************************************************/
define("TP_OPT_DEVID", "devID");
define("TP_OPT_KEYWORD", "tubepress");
define("TP_OPT_SEARCHBY", "mode");
define("TP_OPT_THUMBHEIGHT", "thumbHeight");
define("TP_OPT_THUMBWIDTH", "thumbWidth");
define("TP_OPT_USERNAME", "username");
define("TP_OPT_VIDHEIGHT", "mainVidHeight");
define("TP_OPT_VIDWIDTH", "mainVidWidth");
define("TP_OPT_TIMEOUT", "timeout");
define("TP_OPT_PLAYIN", "playerLocation");
define("TP_OPT_VIDSPERPAGE", "resultsPerPage");

/*****************************************************************************/
/*** META INFORMATION ********************************************************/
/*****************************************************************************/
define("TP_VID_AUTHOR", "author");
define("TP_VID_ID", "id");
define("TP_VID_TITLE", "title");
define("TP_VID_LENGTH", "length");
define("TP_VID_RATING_CNT", "ratings");
define("TP_VID_RATING_AVG", "rating");
define("TP_VID_DESC", "description");
define("TP_VID_VIEW", "views");
define("TP_VID_UPLOAD_TIME", "uploaded");
define("TP_VID_COMMENT_CNT", "comments");
define("TP_VID_TAGS", "tags");
define("TP_VID_URL", "url");
define("TP_VID_THUMBURL", "thumburl");
?>
