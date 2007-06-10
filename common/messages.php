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
          /*******************************************************************/
          /*** ERRORS ********************************************************/
          /*******************************************************************/          
          "TIMEOUT" => "Timed out while trying to contact YouTube after %s seconds",
          "BADHTTP" => "YouTube did not respond with an HTTP OK",
          "NOSTATUS" => "Valid XML from YouTube, but status is missing",
          "REFUSED" => "Unable to connect to YouTube - ",
          "XMLUNSERR" => "XML unserialization error",
          "NOCOUNT" => "YouTube didn't return a total video count",
          "OKNOVIDS" => "YouTube responded with OK, but no video_list returned",
          "BADMODE" => "Invalid mode specified (%s)",
          "BADTYPE" => "Type mismatch while setting the value for '%s'. " .
                "Expected any %s but instead got '%s' of type '%s'",
          "BADVAL" => "\"%s\" not a valid value for \"%s\". Must be one of the following: '%s'",
          "NOVALS" => "No valid values defined for this option",
          "VALTYPE" => "Must set valid option values with TubePressEnum class",
          "NOSUCHOPT" => "%s is not a valid option",
          "NODB" => "Database options are completely missing.",
          "BADDB" => "Database options appear to be of type '%s' instead of an array.",
          "VIDNOARR" => "Attempted to make video out of non-array",
          "VIDEMTARR" => "Attempted to make video out of empty array",
          "MISSATT" => "%s is missing from the video's XML",
          "PARSERR" => "There was a problem parsing the TubePress tag in this page",
          "DBMISS" => "Database is missing the '%s' option. You have %s out of " .
                  "%s options stored. Perhaps you need to initialize your database?",
          "OLDDB" => "You have options that are not current TubePressOptions",
          "MAXMIN" => "%s must be between 1 and %s. You supplied %s.",
          
          /*******************************************************************/
          /*** META INFO *****************************************************/
          /*******************************************************************/
          "META" => "Video meta display",
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
          
          /*******************************************************************/
          /*** PAGE WIDE MESSAGES ********************************************/
          /*******************************************************************/
          "OPTPANELTITLE" => "TubePress Options",
          "OPTPANELMENU" => "TubePress",
          "OPTSUCCESS" => "Options updated",
          "SAVE" => "Save",
          "OPTPAGEDESC" => "Set default options for the plugin. 
            Each option here can be overridden 
            on any page that has your TubePress trigger tag.",
          
          /*******************************************************************/
          /*** ADVANCED OPTIONS **********************************************/
          /*******************************************************************/
          "ADV_GRP_TITLE" => "Advanced",
          "DEBUGTITLE" => "Enable debugging",
          "DEBUGDESC" => "If checked, " .
                         "anyone will be able to view your debugging " .
                         "information. This is a rather small privacy " .
                         "risk. If you're not having problems with " .
                         "TubePress, or you're worried about revealing " .
                         "any details of your TubePress pages, feel free to " .
                         "disable the feature.",
          "DEVID_TITLE" => "Alternate YouTube developer ID",
          "DEVID_DESC" => "Default is \"qh7CQ9xJIIc\". I can't think of a " .
                  "reason why you'd want/need to change the default developer " .
                  "id to you own, but the option is here for completeness." .
                  " Available from <a href=\"http://www.youtube.com/my_profile_dev\"" .
                  ">http://www.youtube.com/my_profile_dev</a>",
          "KEYWORD_TITLE" => "Trigger keyword",
          "KEYWORD_DESC" => "The word you insert (in plaintext, between " .
                  "square brackets) into your posts to display your YouTube " .
                  "gallery.",
          "TIMEOUT_TITLE" => "How long to wait (in seconds) for YouTube to " .
                  "respond",
          "TIMEOUT_DESC" => "Default is 6 seconds",
          "USERNAME_TITLE" => "Alternate YouTube username",
          "USERNAME_DESC" => "Default is \"3hough\". Again, no reason to " .
                  "change this value unless you know something I don't :p",
          
          /*******************************************************************/
          /*** MODES *********************************************************/
          /*******************************************************************/
          "MODE_TITLE" => "Mode",
          "MODE_FAV_TITLE" => "this YouTube user's \"favorites\"",
          "MODE_FAV_DESC" => "YouTube limits this mode to the latest 10 favorites",
          "MODE_FEAT_TITLE" => "The latest 25 \"featured\" videos " .
                  "on YouTube's homepage",
          "MODE_HEADER" => "Which videos?",
          "MODE_PLST_TITLE" => "<sup>*</sup>this playlist",
          "MODE_PLST_DESC" => "Will usually look something like this:" .
                  " D2B04665B213AE35. Copy the playlist id from the end of the " .
                  "URL in your browser's address bar (while looking at a YouTube " .
                  "playlist). It comes right after the 'p='. For instance: " .
                  "http://youtube.com/my_playlists?p=D2B04665B213AE35",
          "MODE_POPULAR_TITLE" => "Most-viewed videos from the past...",
          "MODE_TAG_TITLE" => "<sup>*</sup>YouTube search for",
          "MODE_USER_TITLE" => "<sup>*</sup>videos from this YouTube user",
          
          /*******************************************************************/
          /*** DISPLAY OPTIONS ***********************************************/
          /*******************************************************************/
          "THUMBHEIGHT_DESC" => "Default (and maximum) is 90",
          "THUMBHEIGHT_TITLE" => "Height (px) of thumbs",
          "THUMBWIDTH_DESC" => "Default (and maximum) is 120",
          "THUMBWIDTH_TITLE" => "Width (px) of thumbs",
          "VIDDISP" => "Video display",
          "VIDHEIGHT_TITLE" => "Max height (px) of main video",
          "VIDHEIGHT_DESC" => "Default (and maximum) is 336",
          "VIDWIDTH_TITLE" => "Max width (px) of main video",
          "VIDWIDTH_DESC" => "Default (and maximum) is 424",
          "VIDSPERPAGE_TITLE" => "Videos per page",
          "VIDSPERPAGE_DESC" => "Default is 20, maximum is 100. Only some modes " .
                  "support pagination (see above).",
          "TP_OPT_GREYBOXON_TITLE" => "Enable GreyBox",
          "TP_OPT_LWON_TITLE" => "Enable lightWindow",
          "TP_OPT_GREYBOXON_DESC" => "Checking this box will load the GreyBox JS libraries" .
              " in your blog. This <i>may</i> interfere with your theme and/or other plugins," .
              " so it's good practice to leave this disabled if you're not using GreyBox.",
          "TP_OPT_LWON_DESC" => "Checking this box will load the lightWindow JS libraries" .
              " in your blog. This <i>may</i> interfere with your theme and/or other plugins," .
              " so it's good practice to leave this disabled if you're not using lightWindow.",
          
          /*******************************************************************/
          /*** VIDEO PLAYER LOCATIONS ****************************************/
          /*******************************************************************/
          "PLAYIN_LW_TITLE" => "with lightWindow (experimental... enable it above)",
          "PLAYIN_NORMAL_TITLE" => "normally (at the top of your gallery)",
          "PLAYIN_NW_TITLE" => "in a new window by itself",
          "PLAYIN_POPUP_TITLE" => "in a popup window",
          "PLAYIN_TB_TITLE" => "with GreyBox (experimental... enable it above)",
          "PLAYIN_TITLE" => "Play each video...",
          "PLAYIN_YT_TITLE" => "from the original YouTube page",
          
          /*******************************************************************/
          /*** MISC **********************************************************/
          /*******************************************************************/
          "BACK2GALLERY" => "&laquo; back to gallery",
          "PREV" => "&laquo; prev",
          "NEXT" => "next &raquo;");

/**
 * Our main messaging function
 */
function _tpMsg()
{
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
