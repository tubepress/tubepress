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
          "REFUSED" => "Unable to connect to YouTube - ",
          "NOCOUNT" => "YouTube didn't return a total video count",
          "NOVALS" => "No valid values defined for this option",
          "VALTYPE" => "Must set valid option values with TubePressEnum class",
          "VIDNOARR" => "Attempted to make video out of non-array",
          "VIDEMTARR" => "Attempted to make video out of empty array",
          "MISSATT" => "%s is missing from the video's XML",
          "PARSERR" => "There was a problem parsing the TubePress tag in this page",
          
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
          "VIDUPLOAD" => "Uploaded date",
          "VIDURL" => "YouTube URL",
          "VIDVIEWS" => "Views",
          "VIDCAT" => "Category",
          
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
          "KEYWORD_TITLE" => "Trigger keyword",
          "KEYWORD_DESC" => "The word you insert (in plaintext, between " .
                  "square brackets) into your posts to display your YouTube " .
                  "gallery.",
          "TIMEOUT_TITLE" => "How long to wait (in seconds) for YouTube to " .
                  "respond",
          "TIMEOUT_DESC" => "Default is 6 seconds",
          
          /*******************************************************************/
          /*** MODES *********************************************************/
          /*******************************************************************/
          "MODE_TITLE" => "Mode",
          "MODE_FAV_TITLE" => "This YouTube user's \"favorites\"",
          "MODE_FAV_DESC" => "YouTube limits this mode to the latest 500 favorites",
          "MODE_FEAT_TITLE" => "The latest \"featured\" videos " .
                  "on YouTube's homepage",
          "MODE_HEADER" => "Which videos?",
          "MODE_PLST_TITLE" => "This playlist",
          "MODE_PLST_DESC" => "Limited to 200 videos per playlist. Will usually look something like this:" .
                  " D2B04665B213AE35. Copy the playlist id from the end of the " .
                  "URL in your browser's address bar (while looking at a YouTube " .
                  "playlist). It comes right after the 'p='. For instance: " .
                  "http://youtube.com/my_playlists?p=D2B04665B213AE35",
          "MODE_POPULAR_TITLE" => "Most-viewed videos from...",
          "MODE_TAG_TITLE" => "YouTube search for",
          "MODE_USER_TITLE" => "Videos from this YouTube user",
          
          /*******************************************************************/
          /*** DISPLAY OPTIONS ***********************************************/
          /*******************************************************************/
          "THUMBHEIGHT_DESC" => "Default (and maximum) is 90",
          "THUMBHEIGHT_TITLE" => "Height (px) of thumbs",
          "THUMBWIDTH_DESC" => "Default (and maximum) is 120",
          "THUMBWIDTH_TITLE" => "Width (px) of thumbs",
          "VIDDISP" => "Video display",
          "VIDHEIGHT_TITLE" => "Max height (px) of main video",
          "VIDHEIGHT_DESC" => "Default is 336",
          "VIDWIDTH_TITLE" => "Max width (px) of main video",
          "VIDWIDTH_DESC" => "Default is 424",
          "VIDSPERPAGE_TITLE" => "Videos per page",
          "VIDSPERPAGE_DESC" => "Default is 20.",
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
