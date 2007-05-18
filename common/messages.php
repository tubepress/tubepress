<?php
/**
 * messages.php
 * 
 * Constant strings that the user will read.
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

$tubepressMessages = 
    array(
          "BACK2GALLERY" => "Back to gallery",
          
          /*******************************************************************/
          /*** ERRORS ********************************************************/
          /*******************************************************************/          
          "TIMEOUT" => "Timed out while trying to contact YouTube",
          "BADHTTP" => "YouTube did not respond with an HTTP OK (%s)",
          "NOSTATUS" => "Valid XML from YouTube, but status is missing",
          "REFUSED" => "Unable to connect to YouTube",
          "XMLUNSERR" => "XML unserialization error",
          "YTERROR" => "YouTube responded with an error message: %s",
          "NOCOUNT" => "YouTube didn't return a total video count",
          "OKNOVIDS" => "YouTube responded with OK, but no video_list returned",
          "BADMODE" => "Invalid mode specified (%s)",
          "BADTYPE" => "Type mismatch while setting the value for '%s.' " .
    		    "Expected a %s but instead got a %s with value '%s'",
    	  "BADVAL" => "This value is not in the valid values list.",
    	  "NOVALS" => "No valid values defined for this option",
    	  "ARRSET" => "Can only set valid options with an array",
    	  "VALTYPE" => "Must set valid option values with TubePressEnum class",
    	  "SETOPT" => "%s is not a valid option",
    	  "NODB" => "Database options are completely missing.",
    	  "BADDB" => "Database options are not coming back as an array",
    	  "VIDNOARR" => "Attempted to make video out of non-array",
    	  "VIDEMTARR" => "Attempted to make video out of empty array",
    	  "MISSATT" => "%s is missing from the video's XML",
    	  "PARSERR" => "There was a problem parsing the TubePress tag in this page",
    	  "GALERR" => "There was a problem generating the gallery",
    	  "DBMISS" => "Database is missing the '%s' option. You have %s out of %s options stored. Perhaps you need to initialize your database?",
          "OLDDB" => "You have options that are not current TubePressOptions",
          
          /*******************************************************************/
          /*** META INFO *****************************************************/
          /*******************************************************************/
          "VIDAUTHOR" => "Author",
          "VIDCOMMENTS" => "Comments",
          "VIDDESC" => "Description",
          "VIDID" => "Video ID",
          "VIDLEN" => "Length",
          "VIDTITLE" => "Title",
          "VIDRATING" => "Rating",
          "VIDRATINGS" => "Ratings",
          "VIDTAGS" => "Tags",
          "VIDTHUMBURL" => "Thumbnail URL",
          "VIDUPLOAD" => "Uploaded date",
          "VIDURL" => "YouTube URL",
          "VIDVIEWS" => "Views",
          








          "META" => "Video meta display",
          
          /*******************************************************************/
          /*** PAGE WIDE MESSAGES ********************************************/
          /*******************************************************************/
          "OPTPANELTITLE" => "TubePress Options",
          "OPTPANELMENU" => "TubePress",
          "OPTSUCCESS" => "Options updated",
          "SAVE" => "Save",
          
          /*******************************************************************/
          /*** ADVANCED OPTIONS **********************************************/
          /*******************************************************************/
          "ADV_GRP_TITLE" => "Advanced",
          "DEVID_TITLE" => "Alternate YouTube developer ID",
          "DEVID_DESC" => "Default is \"qh7CQ9xJIIc\". I can't think of a " .
          		"reason why you'd want/need to change the default developer " .
          		"id to you own, but the option is here for completeness." .
          		" Available from",
          "KEYWORD_TITLE" => "Trigger keyword",
          "KEYWORD_DESC" => "The word you insert (in plaintext, between " .
          		"square brackets) into your posts to display your YouTube " .
          		"gallery.",
          "TIMEOUT_TITLE" => "How long to wait (in seconds) for YouTube to respond",
          "TIMEOUT_DESC" => "Default is 6 seconds",
          "USERNAME_TITLE" => "Alternate YouTube username",
          "USERNAME_DESC" => "Default is \"3hough\". Again, no reason to change " .
          		"this value unless you know something I don't :p",
          "DEBUGDESC" => "Enable debugging", "If set to 'true', " .
                         "anyone will be able to view your debugging" .
                         "information. This is a very small privacy" .
                         "risk. If you're not having problems with" .
                         "TubePress, or you're worried about revealing" .
                         "any details of your TubePress pages, feel free to disable debugging here.",
          
          /*******************************************************************/
          /*** MODES *********************************************************/
          /*******************************************************************/
          "WHICHVIDS" => "Which videos?",
          "SRCH_CATEGORY_TITLE" => "this category",
          "SRCH_FAV_TITLE" => "this user's \"favorites\"",
          "SRCH_FEATURED_TITLE" => "The latest 25 \"featured\" videos " .
          		"on YouTube's homepage.",
          "SRCH_PLST_TITLE" => "this playlist",
          "SRCH_PLST_DESC" => "Will usually look something like this:" .
          		" D2B04665B213AE35. Copy the playlist id from the end of the URL" .
          		" in your browser's address bar (while looking at a YouTube playlist)." .
          		" It comes right after the 'p='. For instance: " .
          		"http://youtube.com/my_playlists?p=D2B04665B213AE35.",
          "SRCH_POPULAR_TITLE" => "Top 25 most-viewed videos from the past...",
          "SRCH_REL_TITLE" => "<i>any</i> of these tags",
          "SRCH_TAG_TITLE" => "<i>all</i> of these tags",
          "SRCH_USER_TITLE" => "this user's videos",
          "SRCH_TAGREL_DESC" => "Space-separated tags with no special characters " .
          		"or punctuation.",
          
          /*******************************************************************/
          /*** DISPLAY OPTIONS ***********************************************/
          /*******************************************************************/
          "VIDDISP" => "Video display",
          "THUMBHEIGHT_DESC" => "Default is 90",
          "THUMBHEIGHT_TITLE" => "Height (px) of thumbs",
          "THUMBWIDTH_DESC" => "Default is 120",
          "THUMBWIDTH_TITLE" => "Width (px) of thumbs",
          "VIDHEIGHT_TITLE" => "Max height (px) of main video",
          "VIDHEIGHT_DESC" => "Default is 350",
          "VIDWIDTH_TITLE" => "Max width (px) of main video",
          "VIDWIDTH_DESC" => "Default is 425",
          "VIDSPERPAGE_TITLE" => "Videos per page",
          "VIDSPERPAGE_DESC" => "Default is 20, maximum is 100. The only modes that " .
          		"support pagination are the tag modes, and videos from some user. " .
          		"Playlists are supposed to page but it appears to be broken on YouTube's" .
          		" side :(",
          
          /*******************************************************************/
          /*** VIDEO PLAYER LOCATIONS ****************************************/
          /*******************************************************************/
          "PLAYIN_TITLE" => "Play each video...",
          "PLAYIN_NW_TITLE" => "in a new window by itself",
          "PLAYIN_YT_TITLE" => "from the original YouTube page",
          "PLAYIN_NORMAL_TITLE" => "normally (at the top of your gallery)",
          "PLAYIN_POPUP_TITLE" => "in a popup window",
          "PLAYIN_LB_TITLE" => "using Thickbox (experimental)",
          "PLAYIN_LB_TITLE" => "with ThickBox (experimental)",
          "PLAYIN_LW_TITLE" => "with LightWindow (experimental)");

function _tpMsg() {
	global $tubepressMessages;
	if (func_num_args() > 1) {
		$format = func_get_arg(0);
		$args = func_get_arg(1);
		return vsprintf($tubepressMessages[$format], $args);
	} else {
		return $tubepressMessages[func_get_arg(0)];
	}
	
}
?>
