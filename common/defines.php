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
/*** MISC ********************************************************************/
/*****************************************************************************/

/* DO NOT CHANGE THIS DEFINE */
define("TP_OPTION_NAME", "tubepress");

/*****************************************************************************/
/*** QUERY STRING PARAMS *****************************************************/
/*****************************************************************************/
define("TP_PARAM_PAGE", "tubepress_page");
define("TP_PARAM_VID", "tubepress_id");
define("TP_PARAM_DEBUG", "tubepress_debug");

/*****************************************************************************/
/*** MODE NAMES **************************************************************/
/*****************************************************************************/
define("TP_MODE_FAV", "favorites");
define("TP_MODE_SEARCH", "tag");
define("TP_MODE_TAG", "tag");
define("TP_MODE_REL", "related");
define("TP_MODE_USER", "user");
define("TP_MODE_PLST", "playlist");
define("TP_MODE_FEATURED", "featured");
define("TP_MODE_POPULAR", "popular");
define("TP_MODE_CATEGORY", "category");
define("TP_MODE_TOPRATED", "top_rated");
define("TP_MODE_MOBILE", "mobile");

/*****************************************************************************/
/*** MODE  *************************************************************/
/*****************************************************************************/
define("TP_OPT_MODE", "mode");

/*****************************************************************************/
/*** PLAYER LOCATIONS ********************************************************/
/*****************************************************************************/
define("TP_OPT_PLAYIN", "playerLocation");
define("TP_PLAYIN_NW", "new_window");
define("TP_PLAYIN_YT", "youtube");
define("TP_PLAYIN_NORMAL", "normal");
define("TP_PLAYIN_POPUP", "popup");
define("TP_PLAYIN_GREYBOX", "greybox");
define("TP_PLAYIN_LWINDOW", "lightwindow");

/*****************************************************************************/
/*** DISPLAY OPTIONS *********************************************************/
/*****************************************************************************/
define("TP_OPT_THUMBHEIGHT", "thumbHeight");
define("TP_OPT_THUMBWIDTH", "thumbWidth");
define("TP_OPT_VIDHEIGHT", "mainVidHeight");
define("TP_OPT_VIDWIDTH", "mainVidWidth");
define("TP_OPT_VIDSPERPAGE", "resultsPerPage");
define("TP_OPT_GREYBOXON", "greyBoxEnabled");
define("TP_OPT_LWON", "lightWindowEnabled");
define("TP_OPT_ORDERBY", "orderBy");

/*****************************************************************************/
/*** ADVANCED OPTIONS ********************************************************/
/*****************************************************************************/
define("TP_OPT_DEBUG", "debugging_enabled");
define("TP_OPT_KEYWORD", "keyword");
define("TP_OPT_TIMEOUT", "timeout");
define("TP_OPT_RANDOM_THUMBS", "randomize_thumbnails");
define("TP_OPT_FILTERADULT", "filter_racy");

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
define("TP_VID_CATEGORY", "category");

define("TP_CSS_SUCCESS", "updated fade");
define("TP_CSS_FAILURE", "error fade");
?>
