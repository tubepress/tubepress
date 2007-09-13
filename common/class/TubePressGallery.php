<?php
/**
 * TubePressGallery.php
 * 
 * The gallery generation class
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

class_exists("TubePressOptionsPackage")
    || require("options/TubePressOptionsPackage.php");
class_exists("HTML_Template_IT")
    || require(dirname(__FILE__) . 
        "/../../lib/PEAR/HTML/HTML_Template_IT/IT.php");
class_exists("TubePressXML") || require("util/TubePressXML.php");
class_exists("TubePressVideo") || require("TubePressVideo.php");
class_exists("TubePressStatic") || require("util/TubePressStatic.php");
class_exists("Net_URL")
    || require(dirname(__FILE__) . 
        "/../../lib/PEAR/Networking/Net_URL/URL.php");

function_exists("diggstyle_getPaginationString")
    || require(dirname(__FILE__) . "/../../lib/diggstyle_function.php");

/**
 * Handles fetching and printing out a YouTube gallery. This is meant
 * to be a "static" class, but PHP 4 doesn't support them :(
 */
class TubePressGallery
{
    
    /**
     * Don't let anyone instantiate this class
     */
    function TubePressGallery()
    {
        die("This is a static utility class");
    }

    /*
     * This is the main function that we expose, so we have to
     * do some ugly error checking first.
     */
    function generate($stored = "PHP4isLAMO")
    {
        if ($stored == "PHP4isLAMO") {
            $stored = new TubePressStorageBox();
        } else {
            if ($stored == NULL) {
                return TubePressStatic::bail(
                    PEAR::raiseError("Null storage"));
            }
            if (!is_a($stored, "TubePressStorageBox")) {
                return TubePressStatic::bail(
                    PEAR::raiseError("Wrong data type"));
            }
            $result = $stored->checkValidity();
            if (PEAR::isError($result)) {
                return TubePressStatic::bail($result);
            }
        }
        
        $result = TubePressGallery::_generate($stored);
        if (PEAR::isError($result)) {
            return TubePressStatic::bail($result);
        } else {
            return $result;    
        }
    }
    
    /**
     * This is the main method. At this point we must assume that our options
     * are valid.
     */
    function _generate($stored)
    {   

        /* printing a single video only? */
        $playerLocation = $stored->options->get(TP_OPT_PLAYIN);
        if ($playerLocation->getValue() == TP_PLAYIN_NW
            && isset($_GET[TP_PARAM_VID])) {
            return TubePressGallery::printHTMLSingleVideo();
        }
        
        /* load up the gallery template */
        $tpl = new HTML_Template_IT(dirname(__FILE__) . "/../templates");
        $tpl->loadTemplatefile("gallery.tpl.html", true, true);
        if (PEAR::isError($tpl)) {
        	return $tpl;
        }

        /* are we paging? */
        $paging = TubePressStatic::areWePaging($stored->options);
            
        /* Grab the XML from YouTube's API */
        $request = TubePressXML::generateRequest($stored);
        if (PEAR::isError($request)) {
            return $request;
        }
        $youtube_xml = TubePressXML::fetchRawXML($stored->options,
            $request);

        /* Any HTTP errors? */
        if (PEAR::isError($youtube_xml)) {
            return $youtube_xml;
        }

        /* put the XML into a nice, friendly array */
        $videoArray = TubePressXML::parseRawXML($youtube_xml);

print_r($videoArray);

        /* Any parsing errors? */
        if (PEAR::isError($videoArray)) {
            return $videoArray;
        }
        
        /* keeps track of how many videos we've actually printed */
        $videosPrintedCnt = 0;
        
        /* how many videos we actually got from YouTube */
        //$videosReturnedCnt = is_array($videoArray['video'][0]) ?
        //    count($videoArray['video']) :
        //    1;
        $videosReturnedCnt = count($videoArray);
        
        
        /* Next two lines figure out how many videos we're going to show */
        $vidsPerPage = $stored->options->get(TP_OPT_VIDSPERPAGE);
        $vidLimit = ($paging ?
            $vidsPerPage->getValue() : 
            $videosReturnedCnt);
        if ($videosReturnedCnt < $vidLimit) {
            $vidLimit = $videosReturnedCnt;
        }

        /* If we're paging, spit out the top/bottom pagination */
        if ($paging) {
            $tpl->setVariable('PAGINATION', 
                TubePressGallery::_printHTML_pagination(
                    $videoArray['total'], 
                    $stored->options));
            $tpl->parse('topPagination');
            $tpl->parse('bottomPagination');
        }
        
        for ($x = 0; $x < $vidLimit; $x++) {
            /* Create a TubePressVideo object from the XML (if we can) */
            $video = new TubePressVideo($videoArray[$x]);
        
            /* Top of the gallery is special */
            if ($videosPrintedCnt++ == 0) {
                TubePressGallery::printHTML_bigvid($video, 
                    $stored->options, $tpl);
            }
            
            /* Here's where each thumbnail gets printed */
            TubePressGallery::_printHTML_smallvid($video,
                 $stored, $tpl);
        }

        return $tpl->get();
    }
    
     /**
     * Prints out an embedded video at the top of the gallery.
     * Used in "normal" video playing mode only.
     * 
     * @param vid A TubePressVideo object of the video we're going to play
     * @param options A TubePressTag object holding all of our options
     * @param tpl Our template object
     */
    function printHTML_bigvid($vid, $options, &$tpl)
    {    
        /* we only do this stuff if we're operating in "normal" play mode */
        $playerLocation = $options->get(TP_OPT_PLAYIN);
        if ($playerLocation->getValue() != TP_PLAYIN_NORMAL) {
            return;
        }
        
        $width = $options->get(TP_OPT_VIDWIDTH);
        $height = $options->get(TP_OPT_VIDHEIGHT);

        $tpl->setVariable('WIDTH', $width->getValue());
        $tpl->setVariable('TITLE', $vid->getTitle());
        $tpl->setVariable('HEIGHT', $height->getValue());
        $tpl->setVariable('ID', $vid->getId());
        $tpl->parse('bigVideo');
    }
    
    /**
     * Used in "single video" mode to print out a single video and a 
     * "back to gallery" link
     *
     * @param options A TubePressTag object holding all of our options
     */
    function printHTML_singleVideo($options)
    {
        $width = $options->get(TP_OPT_VIDWIDTH);
        $height = $options->get(TP_OPT_VIDHEIGHT);
        
        $url = new Net_URL(TubePressStatic::fullURL());
        $url->removeQueryString(TP_PARAM_VID);

    	$tpl =& new TubePressSavant();
    	$tpl->assign('width', $width->getValue());
    	$tpl->assign('height', $height->getValue());
    	$tpl->assign('id', $id);
    	$tpl->assign('url', $url->getURL());
    	$tpl->assign('linktext', _tpMsg("BACK2GALLERY"));
        
        return $tpl->fetch('single_video.tpl.php');
    }
    
    /*************************************************************************/
    /************************* "PRIVATE" FUNCTIONS ***************************/
    /*************************************************************************/
    
    /**
     * Prints out video meta information below a video thumbnail. This
     * function needs a makeover at some point.
     * 
     * @param vid A TubePressVideo object of the video in question
     * @param options A TubePressTag object holding all of our options
     * @param link The attributes of the anchor for the title text
     * @param tpl
     */
    function _printHTML_metaInfo($vid, $options, $link, &$tpl)
    {
        /* the video's title */
        $title = $options->get(TP_VID_TITLE);
        if ($title->getValue()) {
        	$tpl->setVariable('PLAYLINK', $link);
        	$tpl->setVariable('TITLE', $vid->getTitle());
        	$tpl->parse('title');
        }
        
        /* the video's runtime */
        $length = $options->get(TP_VID_LENGTH);
        if ($length->getValue()) {
        	$tpl->setVariable('RUNTIME', $vid->getRuntime());
            $tpl->parse('runtime');
        }

        /* the video's description */
        $desc = $options->get(TP_VID_DESC);
        if ($desc->getValue()) {
            $tpl->setVariable('DESCRIPTION', $vid->getDescription());
            $tpl->parse('description');
        }
        
        /* the video's author */
        $author = $options->get(TP_VID_AUTHOR);
        if ($author->getValue()) {
            $opt = $options->get(TP_VID_AUTHOR);
            $tpl->setVariable('METANAME', $opt->getTitle());
            $tpl->setVariable('AUTHOR', $vid->getAuthor());
            $tpl->parse('author');
        }
        
        /* the video's comment count */
        $comment = $options->get(TP_VID_COMMENT_CNT);
        if ($comment->getValue()) {
            $opt = $options->get(TP_VID_COMMENT_CNT);
            $tpl->setVariable('METANAME', $opt->getTitle());
            $tpl->setVariable('COUNT', $vid->getCommentCount());
            $tpl->setVariable('ID', $vid->getId());
            $tpl->parse('comments');
        }
        
        /* the tags, space separated */
        $tags = $options->get(TP_VID_TAGS);
        if ($tags->getValue()) {
            $tags = explode(" ", $vid->getTags());
            $tags = implode("%20", $tags);
            $opt = $options->get(TP_VID_TAGS);
            $tpl->setVariable('METANAME', $opt->getTitle());
            $tpl->setVariable('SEARCHSTRING', $tags);
            $tpl->setVariable('TAGS', $vid->getTags());
            $tpl->parse('tags');
        }
        
        /* the thumbnail URL */
        $thumb = $options->get(TP_VID_THUMBURL);
        if ($thumb->getValue()) {
            $opt = $options->get(TP_VID_THUMBURL);
        	$tpl->setVariable('LINKVALUE', $vid->getThumbURL());
        	$tpl->setVariable('LINKTEXT', $opt->getTitle());
            $tpl->parse('url');
        }
        
        /* the video URL */
        $url = $options->get(TP_VID_URL);
        if ($url->getValue()) {
            $opt = $options->get(TP_VID_URL);
        	$tpl->setVariable('LINKVALUE', $vid->getURL());
        	$tpl->setVariable('LINKTEXT', $opt->getTitle());
            $tpl->parse('url');
        }
        
        /* 
         * the rest of these meta values don't require any special
         * treatment
         */
        $left = array(TP_VID_VIEW, TP_VID_ID, TP_VID_RATING_AVG,
            TP_VID_RATING_CNT, TP_VID_UPLOAD_TIME, TP_VID_CATEGORY);
        
        foreach ($left as $leftover) {
            $opt = $options->get($leftover);
            
            if ($opt->getValue() == true) {
                
       	        $tpl->setVariable('METANAME', $opt->getTitle());
       	        
       	        switch ($leftover) {
       	            case TP_VID_VIEW:
       	                $tpl->setVariable('METAVALUE', $vid->getViewCount());
       	                break;
       	            case TP_VID_ID:
       	                $tpl->setVariable('METAVALUE', $vid->getId());
       	                break;
       	            case TP_VID_RATING_AVG:
       	                $tpl->setVariable('METAVALUE', $vid->getRatingAverage());
       	                break;
       	            case TP_VID_RATING_CNT:
       	                $tpl->setVariable('METAVALUE', $vid->getRatingCount());
       	                break;
       	            case TP_VID_UPLOAD_TIME:
       	                $tpl->setVariable('METAVALUE', $vid->getUploadTime());
                        break;
                    case TP_VID_CATEGORY:
                        $tpl->setVariable('METAVALUE', $vid->getCategory());
       	        }
                $tpl->parse('meta');
            }
        }
    }

    /**
     * Handles the logic and printing of pagination links ("next" and "prev")
     * 
     * @param vidCount The grand total video count
     * @param options Current options
     */
    function _printHTML_pagination($vidCount, $options)
    {
        $currentPage = TubePressStatic::getPageNum();
        $vidsPerPage = $options->get(TP_OPT_VIDSPERPAGE);
    
        $newurl = new Net_URL(TubePressStatic::fullURL());
        $newurl->removeQueryString(TP_PARAM_PAGE);
 
        $currentMode = $options->get(TP_OPT_MODE);
        if ($currentMode->getValue() == TP_MODE_TAG) {
            $vidCount = min($vidCount, 400);
        }
 
        return diggstyle_getPaginationString($currentPage, $vidCount,
            $vidsPerPage->getValue(), 1, $newurl->getURL(), TP_PARAM_PAGE);
    }
    
    /**
     * The main wrapper method for printing out a single video 
     * thumbnail and the meta information for it.
     * 
     * @param vid 
     * @param options
     * @param tpl
     */
    function _printHTML_smallvid($vid, $stored, &$tpl)
    {
        $playerOpt = $stored->options->get(TP_OPT_PLAYIN);
        $playerObj = $stored->players->get($playerOpt->getValue());
        $playLink = $playerObj->getPlayLink($vid, $stored->options);
        
        TubePressGallery::_printHTML_metaInfo($vid, $stored->options,$playLink, $tpl);
        
        $height = $stored->options->get(TP_OPT_THUMBHEIGHT);
        $width = $stored->options->get(TP_OPT_THUMBWIDTH);
        
        $tpl->setVariable('PLAYLINK', $playLink);
        $tpl->setVariable('TITLE', $vid->getTitle());
        $tpl->setVariable('THUMBURL', $vid->getThumbURL());
        $tpl->setVariable('THUMBWIDTH', $width->getValue());
        $tpl->setVariable('THUMBHEIGHT', $height->getValue());
        
		$tpl->parse('thumb');
    }
}
?>