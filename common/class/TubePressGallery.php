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
    || require("TubePressOptionsPackage.php");
class_exists("HTML_Template_IT")
    || require(dirname(__FILE__) . 
        "/../../lib/PEAR/HTML/HTML_Template_IT/IT.php");
class_exists("TubePressXML") || require("TubePressXML.php");
class_exists("TubePressVideo") || require("TubePressVideo.php");
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

    function generate($options = "PHP4isLAMO")
    {
        if ($options == "PHP4isLAMO") {
            $options = new TubePressOptionsPackage();
        }
        
        $result = TubePressGallery::_generate($options);
        if (PEAR::isError($result)) {
            return TubePressStatic::bail($result);
        } else {
            return $result;    
        }
    }
    
    /**
     * This is the main method
     */
    function _generate($options)
    {   

        /* printing a single video only? */
        if ($options->getValue(TP_OPT_PLAYIN) == TP_PLAYIN_NW
            && isset($_GET[TP_PARAM_VID])) {
            return TubePressGallery::printHTMLSingleVideo();
        }
        
        $tpl = new HTML_Template_IT(dirname(__FILE__) . "/../templates");
        $tpl->loadTemplatefile("gallery.tpl.html", true, true);
        if (PEAR::isError($tpl)) {
        	return $tpl;
        }

        /* are we paging? */
        $paging = TubePressStatic::areWePaging($options);
            
        /* Grab the XML from YouTube's API */
        $youtube_xml = TubePressXML::fetchRawXML($options);

        /* Any HTTP errors? */
        if (PEAR::isError($youtube_xml)) {
            return $youtube_xml;
        }

        $videoArray = TubePressXML::parseRawXML($youtube_xml);

        /* Any parsing errors? Or errors from YouTube? */
        if (PEAR::isError($videoArray)) {
            return $videoArray;
        }
        
        /* keeps track of how many videos we've actually printed */
        $videosPrintedCnt = 0;
        
        /* how many videos we actually got from YouTube */
        $videosReturnedCnt = is_array($videoArray['video'][0]) ?
            count($videoArray['video']) :
            1;
        
        /* Next two lines figure out how many videos we're going to show */
        $vidLimit = ($paging ?
            $options->getValue(TP_OPT_VIDSPERPAGE) : 
            $videosReturnedCnt);
        if ($videosReturnedCnt < $vidLimit) {
            $vidLimit = $videosReturnedCnt;
        }

        if ($paging) {
            $pagination = TubePressGallery::_printHTML_pagination(
                $videoArray['total'], 
                $options);
            $tpl->setCurrentBlock('topPagination');
            $tpl->setVariable('PAGINATION', $pagination);
            $tpl->parseCurrentBlock();
            $tpl->setCurrentBlock('bottomPagination');
            $tpl->setVariable('PAGINATION', $pagination);
            $tpl->parseCurrentBlock();
        }
        
        for ($x = 0; $x < $vidLimit; $x++) {
            /* Create a TubePressVideo object from the XML (if we can) */
            if ($videosReturnedCnt == 1) {
                $video = new TubePressVideo($videoArray['video']);
            } else {
                $video = new TubePressVideo($videoArray['video'][$x]);
            }
        
            /* Top of the gallery is special */
            if ($videosPrintedCnt++ == 0) {
                TubePressGallery::printHTML_bigvid($video, 
                    $options, $tpl);
            }
            
            /* Here's where each thumbnail gets printed */
            TubePressGallery::_printHTML_smallvid($video,
                 $options, $tpl);
        }

        return $tpl->get();
    }
    
     /**
     * Prints out an embedded video at the top of the gallery.
     * Used in "normal" video playing mode.
     * 
     * @param vid A TubePressVideo object of the video we're going to play
     * @param options A TubePressTag object holding all of our options
     */
    function printHTML_bigvid($vid, $options, &$tpl)
    {    
        /* we only do this stuff if we're operating in "normal" play mode */
        if ($options->getValue(TP_OPT_PLAYIN) != TP_PLAYIN_NORMAL) {
            return "";
        }

        $tpl->setCurrentBlock('bigVideo');
        $tpl->setVariable('WIDTH', $options->getValue(TP_OPT_VIDWIDTH));
        $tpl->setVariable('TITLE', $vid->getTitle());
        $tpl->setVariable('HEIGHT', $options->getValue(TP_OPT_VIDHEIGHT));
        $tpl->setVariable('ID', $vid->getId());
        $tpl->parseCurrentBlock();
    }
    
    /**
     * Used in "single video" mode to print out a single video and a 
     * "back to gallery" link
     *
     * @param options A TubePressTag object holding all of our options
     */
    function printHTML_singleVideo($options)
    {
        $url = new Net_URL(TubePressStatic::fullURL());
        $url->removeQueryString(TP_PARAM_VID);

    	$tpl =& new TubePressSavant();
    	$tpl->assign('width', $options->getValue(TP_OPT_VIDWIDTH));
    	$tpl->assign('height', $options->getValue(TP_OPT_VIDHEIGHT));
    	$tpl->assign('id', $id);
    	$tpl->assign('url', $url->getURL());
    	$tpl->assign('linktext', _tpMsg("BACK2GALLERY"));
        
        return $tpl->fetch('single_video.tpl.php');
    }
    
    /*************************************************************************/
    /************************* "PRIVATE" FUNCTIONS ***************************/
    /*************************************************************************/
    
    /**
     * Prints out video meta information below a video thumbnail.
     * The title and runtime are handled slightly differently than 
     * the rest since they output so differently.
     * 
     * @param vid A TubePressVideo object of the video in question
     * @param options A TubePressTag object holding all of our options
     * @param link The attributes of the anchor for the title text. 
     * This is generated from 
     * TubePressGallery::printHTML_smallVidLinkAttributes()
     */
    function _printHTML_metaInfo($vid, $options, $link, &$tpl)
    {
        if ($options->getValue(TP_VID_TITLE)) {
        	$tpl->setCurrentBlock('title');
        	$tpl->setVariable('PLAYLINK', $link);
        	$tpl->setVariable('TITLE', $vid->getTitle());
        }
        
        if ($options->getValue(TP_VID_LENGTH)) {
        	$tpl->setCurrentBlock('runtime');
        	$tpl->setVariable('RUNTIME', $vid->getRuntime());
            $tpl->parseCurrentBlock();
        }

        if ($options->getValue(TP_VID_DESC)) {
            $tpl->setCurrentBlock('description');
            $tpl->setVariable('DESCRIPTION', $vid->getDescription());
            $tpl->parseCurrentBlock();
        }
        
        if ($options->getValue(TP_VID_AUTHOR)) {
            $tpl->setCurrentBlock('author');
            $tpl->setVariable('METANAME', $options->getTitle(TP_VID_AUTHOR));
            $tpl->setVariable('AUTHOR', $vid->getAuthor());
            $tpl->parseCurrentBlock();
        }
        
        if ($options->getValue(TP_VID_COMMENT_CNT)) {
            $tpl->setCurrentBlock('comments');
            $tpl->setVariable('METANAME', $options->getTitle(TP_VID_COMMENT_CNT));
            $tpl->setVariable('COUNT', $vid->getCommentCount());
            $tpl->setVariable('ID', $vid->getId());
            $tpl->parseCurrentBlock();
        }
        
        if ($options->getValue(TP_VID_TAGS)) {
            $tags = explode(" ", $vid->getTags());
            $tags = implode("%20", $tags);
            $tpl->setCurrentBlock('tags');
            $tpl->setVariable('METANAME', $options->getTitle(TP_VID_TAGS));
            $tpl->setVariable('SEARCHSTRING', $tags);
            $tpl->setVariable('TAGS', $vid->getTags());
            $tpl->parseCurrentBlock();
        }
        
        if ($options->getValue(TP_VID_THUMBURL)) {
            $tpl->setCurrentBlock('url');
        	$tpl->setVariable('LINKVALUE', $vid->getThumbURL());
        	$tpl->setVariable('LINKTEXT', $options->getTitle(TP_VID_THUMBURL));
            $tpl->parseCurrentBlock();
        }
        
        if ($options->getValue(TP_VID_URL)) {
            $tpl->setCurrentBlock('url');
        	$tpl->setVariable('LINKVALUE', $vid->getURL());
        	$tpl->setVariable('LINKTEXT', $options->getTitle(TP_VID_URL));
            $tpl->parseCurrentBlock();
        }
        
        $left = array(TP_VID_VIEW, TP_VID_ID, TP_VID_RATING_AVG,
            TP_VID_RATING_CNT, TP_VID_UPLOAD_TIME);
        
        foreach ($left as $leftover) {
            if ($options->getValue($leftover)) {
                $tpl->setCurrentBlock('meta');
       	        $tpl->setVariable('METANAME', $options->getTitle($leftover));
       	        
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
       	        }
                
                $tpl->parseCurrentBlock();
            }
        }
        
    	
    }

    /**
     * Handles the logic and printing of pagination links ("next" and "prev")
     * 
     * @param vidCount How many videos we're supposed to print out per page 
     * (+ 1, unless we're on the last page)
     * @param options A TubePressTag object holding all of our options
     * @param css A CSS holder object
     */
    function _printHTML_pagination($vidCount, $options)
    {
        /* if we're already on a page, save that value, otherwise assume 
         * we're on the first page */
        $currentPage = TubePressStatic::getPageNum();
        $vidsPerPage = $options->getValue(TP_OPT_VIDSPERPAGE);
    
        $newurl = new Net_URL(TubePressStatic::fullURL());
        $newurl->removeQueryString(TP_PARAM_PAGE);
 
         if ($options->getValue(TP_OPT_MODE) == TP_MODE_TAG) {
             $vidCount = min($vidCount, 400);
         }
 
        return diggstyle_getPaginationString($currentPage, $vidCount,
            $vidsPerPage, 1, $newurl->getURL(), TP_PARAM_PAGE);
    }
    
    /**
     * The main wrapper method for printing out a single video 
     * thumbnail and the meta information for it.
     * 
     * @param vid A TubePressVideo object for which this method 
     * will print the thumb
     * @param options A TubePressTag object holding all of our options
     */
    function _printHTML_smallvid($vid, $options, &$tpl)
    {
        $playLink = TubePressGallery::_printHTML_playLink($vid,
            $options);
        TubePressGallery::_printHTML_metaInfo($vid, $options,$link, $tpl);
        $tpl->setVariable('PLAYLINK', $playLink);
        $tpl->setVariable('TITLE', $vid->getTitle());
        $tpl->setVariable('THUMBURL', $vid->getThumbURL());
        $tpl->setVariable('THUMBWIDTH', $options->getValue(TP_OPT_THUMBWIDTH));
        $tpl->setVariable('THUMBHEIGHT', $options->getValue(TP_OPT_THUMBHEIGHT));
        
		$tpl->parse('thumb');
    }
    
    /**
     * Prints out the "play" link attributes for a video thumbnail
     * 
     * @param vid A TubePressVideo object for the video in question
     * @param options A TubePressTag object holding all of our options
     */
    function _printHTML_playLink($vid, $options)
    {
        global $tubepress_base_url;
        $id = $vid->getId();
        $height = $options->getValue(TP_OPT_VIDHEIGHT);
        $width = $options->getValue(TP_OPT_VIDWIDTH);
        $title = $vid->getTitle();
        
        switch ($options->getValue(TP_OPT_PLAYIN)) {
            case TP_PLAYIN_GREYBOX:
                return sprintf(
                    'href="%s/common/popup.php?h=%s&amp;w=%s' .
                    '&amp;id=%s&amp;name=%s" title="%s" ' .
                    'rel="gb_page_center[%s, %s]"',
                    $tubepress_base_url, $height, $width, $id, $title, $title, 
                    $width, $height);
                
            case TP_PLAYIN_NW:
                $url = new Net_URL(TubePressStatic::fullURL());
                $url->addQueryString(TP_PARAM_VID, $id);
                return sprintf('href="%s"', $url->getURL());
            
            case TP_PLAYIN_YT:
                return sprintf('href="http://youtube.com/watch?v=%s"', $id);
            
            case TP_PLAYIN_LWINDOW:
                return sprintf('href="%s/common/popup.php?' .
                        'name=%s&id=%s&w=%s&h=%s" class="lWOn" title="%s" ' .
                        'params="lWWidth=%s,lWHeight=%s"', 
                    $tubepress_base_url, rawurlencode($title), $id,
                    $width, $height, $title, $width, $height);
        
            case TP_PLAYIN_NORMAL:
                return sprintf('href="#" onclick="javascript:playVideo(' .
                        '\'%s\', \'%s\', \'%s\', \'%s\', \'%s\',' .
                        ' \'normal\', \'%s\')"',
                    $id, $height, $width, rawurlencode($title),
                    $vid->getRuntime(), "http://localhost/wp");
    
            default:
                return sprintf('href="#" onclick="javascript:playVideo(' .
                        '\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'popup\',' .
                        ' \'%s\')"',
                    $id, $height, $width,
                    rawurlencode($title), $vid->getRuntime(),
                    $tubepress_base_url);
        }
    }
}
?>