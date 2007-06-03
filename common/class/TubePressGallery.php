<?php
/**
 * TubePressGallery.php
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

class_exists("TubePressCSS") || require("TubePressCSS.php");
class_exists("TubePressXML") || require("TubePressXML.php");
class_exists("TubePressVideo") || require("TubePressVideo.php");
class_exists("Net_URL") || require(dirname(__FILE__) . "/../../lib/PEAR/Networking/Net_URL/URL.php");

function_exists("diggstyle_getPaginationString") || require(dirname(__FILE__) . "/../../lib/diggstyle_function.php");

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

    /**
     * This is the main method
     */
    function generate($options)
    {
        /* Print out the header */
        $newcontent = TubePressGallery::printHTML_videoheader();
    
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

		$css = new TubePressCSS();

        for ($x = 0; $x < $vidLimit; $x++) {
            
            /* Create a TubePressVideo object from the XML (if we can) */
            if ($videosReturnedCnt == 1) {
            	$video = new TubePressVideo($videoArray['video']);
            } else {
            	$video = new TubePressVideo($videoArray['video'][$x]);
            }
            
    
            if (PEAR::isError($video)) {
                continue;
            }
        
            /* Top of the gallery is special */
            if ($videosPrintedCnt++ == 0) {
                $newcontent .= TubePressGallery::printHTML_bigvid($video, $options);
                if ($paging) {
                    $newcontent .= 
                        TubePressGallery::_printHTML_pagination($videoArray['total'], 
                            $options);
                }
                $newcontent .= sprintf('<div class="%s">',
                    $css->thumb_container_class) . "\r\n";
            }
            
            /* Here's where each thumbnail gets printed */
            $newcontent .= TubePressGallery::_printHTML_smallvid($video, $options);
        }
    
        $newcontent .= sprintf('</div><!-- %s -->', $css->thumb_container_class);
        if ($paging) {
            $newcontent .= 
                TubePressGallery::_printHTML_pagination($videoArray['total'],
                    $options);
        }
    
        $newcontent .= TubePressGallery::printHTML_videofooter();
    
        return $newcontent;
    }
    
     /**
     * Prints out an embedded video at the top of the gallery.
     * Used in "normal" video playing mode.
     * 
     * @param vid A TubePressVideo object of the video we're going to play
     * @param options A TubePressTag object holding all of our options
     */
    function printHTML_bigvid($vid, $options)
    {    
        /* we only do this stuff if we're operating in "normal" play mode */
        if ($options->getValue(TP_OPT_PLAYIN) != TP_PLAYIN_NORMAL) {
            return "";
        }
        
        $css = new TubePressCSS();
        
        $returnVal = sprintf('<div id="%s"><div id="%s" style="width: %spx">', 
            $css->mainVid_id, $css->mainVid_inner,
            $options->getValue(TP_OPT_VIDWIDTH)) . "\r\n";
        
        $returnVal .= sprintf('<div id="%s">%s</div>',
            $css->mainVid_title,
        	$vid->metaValues[TP_VID_TITLE]) . "\r\n";
  
        $returnVal .= 
            TubePressGallery::printHTML_embeddedVid($vid->metaValues[TP_VID_ID], $options);
        
        $returnVal .= sprintf('</div><!-- %s --></div> <!--%s--> <br />',
            $css->mainVid_inner,
            $css->mainVid_id) . "\r\n";
        
        return $returnVal;
    }
    
    /**
     * Handles the dirty work of printing out the embedded flash
     * 
     * @param id The YouTube video ID
     * @param options A TubePressTag object holding all of our options
     */
    function printHTML_embeddedVid($id, $options)
    {    	 
        return sprintf('<object type="application/x-shockwave-flash" style="width:%spx;height:%spx;" data="http://www.youtube.com/v/%s">' .
        		'<param name="movie" value="http://www.youtube.com/v/%s" />' .
        		'</object>', min($options->getValue(TP_OPT_VIDWIDTH), 424), 
        		min($options->getValue(TP_OPT_VIDHEIGHT), 336), $id, $id) . "\r\n";
    }
    
    /**
     * Used in "single video" mode to print out a single video and a 
     * "back to gallery" link
     *
     * @param options A TubePressTag object holding all of our options
     */
    function printHTML_singleVideo($options)
    {
        $css = new TubePressCSS();
        
        $url = new Net_URL(TubePressStatic::fullURL());
        $url->removeQueryString(TP_VID_PARAM);
        
        $returnVal = sprintf('<div id="%s"><div id="%s" style="width: %spx">', 
            $css->mainVid_id, $css->mainVid_inner,
            $options->getValue(TP_OPT_VIDWIDTH)) . "\r\n";
        	
        $returnVal .= TubePressGallery::printHTML_embeddedVid($_GET[TP_VID_PARAM],
            $options);
            
        $returnVal .= sprintf('</div><!-- %s -->' .
        		'</div><!-- %s --><br /><a href="%s">%s</a>',
        		$css->mainVid_id, $css->mainVid_inner,
        	    $url->getURL(), _tpMsg("BACK2GALLERY"));
        
        return $returnVal;
    }
    
    /**
     * Prints out the tail end of the gallery
     * 
     * @param css A CSS holder object
     */
    function printHTML_videofooter()
    {
    	$css = new TubePressCSS();
        return sprintf('</div><!-- %s -->', $css->container);
    }
    
    /**
     * Prints out the very beginning of the gallery
     * 
     * @param css A CSS holder object
     */
    function printHTML_videoheader()
    {
        $css = new TubePressCSS();
        return sprintf('<div class="%s">', $css->container) . "\r\n";
    }
    
    /*************************************************************************/
	/*************************************************************************/
	/************************* "PRIVATE" FUNCTIONS ***************************/
	/*************************************************************************/
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
    function _printHTML_metaInfo($vid, $options, $link)
    {
    	$css = new TubePressCSS();
    
        /* first do the title */    //TODO: is this right???
        $content = sprintf('<div class="%s">', $css->title_class);
        if ($options->getValue(TP_VID_TITLE) == true) {
            $content .= sprintf('<a %s>%s</a><br/></div><!-- %s -->', 
                $link, $vid->metaValues[TP_VID_TITLE], $css->title_class);
        }
        
        /* now do the runtime */
        if ($options->getValue(TP_VID_LENGTH) == true) {
            $content .= sprintf('<span class="%s">%s</span><br/>',
                $css->runtime_class, $vid->metaValues[TP_VID_LENGTH]) . "\r\n";
        }
        
        $metaOptions = TubePressVideo::getMetaNames();
        /* now do the rest, since they all look alike */
        foreach ($metaOptions as $metaName) {
            
            /* ignore the title and runtime */
            if (($metaName == TP_VID_LENGTH) || ($metaName == TP_VID_TITLE)) {
                continue;
            }
            
            /* only bother with the ones the user wants to see */
            if ($options->getValue($metaName)) {
                $content .=  sprintf('<span class="%s">', $css->meta_class) . "\r\n";
                switch ($metaName) {
                    
                    case TP_VID_DESC:
                        $content .= 
                            sprintf('</span>%s', $vid->metaValues[$metaName]) . "\r\n";
                        break;
                        
                    case TP_VID_THUMBURL:
                        $content .= 
                            TubePressGallery::_printHTML_metaLink($options->getTitle($metaName),
                            $vid->metaValues[$metaName]);
                        break;
                        
                    case TP_VID_URL:
                        $content .=
                            TubePressGallery::_printHTML_metaLink($options->getTitle($metaName), 
                            $vid->metaValues[$metaName]);
                        break;
                        
                    case TP_VID_AUTHOR:
                        $content .= sprintf('%s: %s', $metaName,
                            TubePressGallery::_printHTML_metaLink($vid->metaValues[$metaName],
                            'http://www.youtube.com/profile?user='
                            . $vid->metaValues[$metaName])) . "\r\n";
                        break;
                        
                    case TP_VID_COMMENT_CNT:
                        $content .= sprintf('%s: %s', $metaName,
                            TubePressGallery::_printHTML_metaLink($vid->metaValues[$metaName],
                           'http://youtube.com/comment_servlet?all_comments&amp;v='
                            . $vid->metaValues[TP_VID_ID])) . "\r\n";
                        break;
                        
                    case TP_VID_TAGS:
                        $tags = explode(" ", $vid->metaValues[$metaName]);
                        $tags = implode("%20", $tags);
                        $content .= sprintf('%s: %s', $metaName,
                            TubePressGallery::_printHTML_metaLink($vid->metaValues[$metaName],
                                sprintf('http://youtube.com/results?' .
                                    'search_query=%s&amp;search=Search',
                                    $tags))) . "\r\n";
                        break;
                        
                    default:
                        $content .=  sprintf('%s: </span>%s', $metaName,
                            $vid->metaValues[$metaName]) . "\r\n";
                }
                $content .= '<br/>';
            }
        }
        $content .= sprintf('</div><!-- %s -->', $css->meta_group);
        
        return $content;
    }
    
    /**
     * Simple helper method for TubePressGallery::printHTML_metaInfo(). Prints
     * out a link for a line of meta information.
     * 
     * @param linkText The text of the link
     * @param linkvalue The anchor attributes
     */
    function _printHTML_metaLink($linkText, $linkValue)
    {
        return sprintf('</span><a href="%s">%s</a>', $linkValue, $linkText) . "\r\n";
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
        $css = new TubePressCSS();
    
        /* if we're already on a page, save that value, otherwise assume 
         * we're on the first page */
        $currentPage = TubePressStatic::getPageNum();
    	$vidsPerPage = $options->getValue(TP_OPT_VIDSPERPAGE);
    
    	$newurl = new Net_URL(TubePressStatic::fullURL());
        $newurl->removeQueryString(TP_PAGE_PARAM);
 
 		if ($options->getValue(TP_OPT_SEARCHBY) == TP_MODE_TAG) {
 			$vidCount = min($vidCount, 400);
 		}
 
        return diggstyle_getPaginationString($currentPage, $vidCount, $vidsPerPage, 
    	    1, $newurl->getURL(), TP_PAGE_PARAM);
    }
    
    /**
     * The main wrapper method for printing out a single video 
     * thumbnail and the meta information for it.
     * 
     * @param vid A TubePressVideo object for which this method 
     * will print the thumb
     * @param options A TubePressTag object holding all of our options
     */
    function _printHTML_smallvid($vid, $options)
    {
        $css = new TubePressCSS();

        $link = TubePressGallery::_printHTML_smallVidLinkAttributes($vid,
            $options);
  
        $content = sprintf('<div class="%s"><div class="%s" style="width: %spx">', 
            $css->thumb_class, $css->thumbInner, $options->getValue(TP_OPT_THUMBWIDTH)) . "\r\n";
   
        $content .= sprintf('<a %s><img alt="%s" src="%s" width="%s" height="%s" /></a>',
        	$link, $vid->metaValues[TP_VID_TITLE], $vid->metaValues[TP_VID_THUMBURL],
        	$options->getValue(TP_OPT_THUMBWIDTH), $options->getValue(TP_OPT_THUMBHEIGHT)) . "\r\n";
  
  		$content .= sprintf('<div class="%s">', $css->meta_group) . "\r\n";
 
        $content .= TubePressGallery::_printHTML_metaInfo($vid, $options,
            $link);
            
        $content .= sprintf('</div><!-- %s --></div><!-- %s -->', $css->thumbInner, $css->thumb_class) . "\r\n";
        
        return $content;
    }
    
    /**
     * Prints out the "play" link attributes for a video thumbnail
     * 
     * @param vid A TubePressVideo object for the video in question
     * @param options A TubePressTag object holding all of our options
     */
    function _printHTML_smallVidLinkAttributes($vid, $options)
    {
    	global $tubepress_base_url;
        $id = $vid->metaValues[TP_VID_ID];
        $height = $options->getValue(TP_OPT_VIDHEIGHT);
        $width = $options->getValue(TP_OPT_VIDWIDTH);
        $title = $vid->metaValues[TP_VID_TITLE];
        
        switch ($options->getValue(TP_OPT_PLAYIN)) {
            case TP_PLAYIN_GREYBOX:
                return sprintf(
                    'href="%s/common/popup.php?h=%s&amp;w=%s' .
                    '&amp;id=%s&amp;name=%s" title="%s" rel="gb_page_center[%s, %s]"',
                    $tubepress_base_url, $height, $width, $id, $title, $title, 
                    $width, $height);
                
            case TP_PLAYIN_NW:
                $url = new Net_URL(TubePressStatic::fullURL());
                $url->addQueryString(TP_VID_PARAM, $id);
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
                        '\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'normal\', \'%s\')"',
                    $id, $height, $width, rawurlencode($title),
                    $vid->metaValues[TP_VID_LENGTH], "http://localhost/wp");
    
            default:
                return sprintf('href="#" onclick="javascript:playVideo(' .
                        '\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'popup\', \'%s\')"',
                    $id, $height, $width,
                    rawurlencode($title), $vid->metaValues[TP_VID_LENGTH],
                    $tubepress_base_url);
        }
    }
    

}
?>