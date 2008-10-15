<?php
/**
 * Copyright 2006, 2007, 2008 Eric D. Hough (http://ehough.com)
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

/* we need this function for pagination */
function_exists("diggstyle_getPaginationString")
    || require dirname(__FILE__) . "/../../../lib/diggstyle_function.php";

/* 
 * thanks to shickm for this...
 * http://code.google.com/p/tubepress/issues/detail?id=27
*/
function_exists("sys_get_temp_dir")
    || require dirname(__FILE__) . "/../../../lib/sys_get_temp_dir.php";

/**
 * Parent class of all TubePress galleries
 */
abstract class AbstractTubePressGallery
{
    
    /**
     * Generates the content of this gallery
     * 
     * @param TubePressOptionsManager $tpom The TubePress options 
     *        manager containing all the user's options
     * 
     * @return The HTML content for this gallery
     */
    public final function generateThumbs($template, TubePressOptionsManager $tpom)
    {
        /* load up the gallery template */
        $tpl = new HTML_Template_IT(dirname(__FILE__) . "/../../ui");
        if (!$tpl->loadTemplatefile($template, true, true)) {
            throw new Exception("Couldn't load gallery template");
        }
        
        /* get the videos as an array */
        $xml = TubePressNetwork::getRss($tpom);

        TubePressGallery::_countResults($xml, $totalResults, $thisResult);
        
        /* Figure out how many videos we're going to show */
        $vidLimit =
            $tpom->get(TubePressDisplayOptions::RESULTS_PER_PAGE);
        if ($thisResult < $vidLimit) {
            $vidLimit = $thisResult;
        }   
        
        /* parse 'em out */
        $displayOrder = AbstractTubePressGallery::_getDisplayOrder($tpom, $vidLimit);
        for ($x = 0; $x < $vidLimit; $x++) {
            TubePressGallery::_parseVideo($xml, $displayOrder[$x], 
                $totalResults, $tpom, $tpl);
        }
        
        /* Spit out the top/bottom pagination if we have any videos */
        if ($vidLimit > 0) {
            TubePressGallery::_parsePaginationHTML($totalResults, $tpom, $tpl);
        }
        
        return $tpl->get();
    }

    
    /**
     * Counts the number of videos that we got back from YouTube
     * 
     * @param DOMDocument $xml           The raw YouTube RSS
     * @param int         &$totalResults How many YouTube said we got overall
     * @param int         &$thisResult   How many we counted in this query
     * 
     * @return int The number of videos we got back from YouTube
     */
    private static function _countResults(DOMDocument $xml, &$totalResults, 
        &$thisResult)
    {
        /* how many YouTube said we got */
        $totalResults =
            $xml->getElementsByTagNameNS('http://a9.com/-/spec' . 
            '/opensearchrss/1.0/', 'totalResults')->item(0)->nodeValue;
        
        /* see if we got any */
        if ($totalResults == 0) {
            throw new Exception("YouTube returned no videos for your query!");
        }
        
        $thisResult = $xml->getElementsByTagName('entry')->length;
    }

    
    /**
     * Prints out an embedded video at the top of the gallery.
     * Used in "normal" video playing mode only
     * 
     * TODO: move to normal player class? maybe an abstract method on player?
     * 
     * @param TubePressVideo          $vid  The video to parse
     * @param TubePressOptionsManager $tpom The TubePress options manager
     * @param HTML_Template_IT        &$tpl HTML template to write to
     * 
     * @return void
     */
    private function _parseBigVidHTML(TubePressVideo $vid, 
        TubePressOptionsManager $tpom, HTML_Template_IT &$tpl)
    {    

        /* we only do this stuff if we're operating in "normal" play mode */
        $playerName =
            $tpom->
                get(TubePressDisplayOptions::CURRENT_PLAYER_NAME);
        $player     = TubePressPlayer::getInstance($playerName);
        
        //TODO: this is hacky
        if (!($player instanceof TPNormalPlayer)) {
            return;
        }
        
        $embed = new TubePressEmbeddedPlayer($vid, $tpom);
        $tpl->setVariable("EMBEDSRC", $embed->toString());
        $tpl->setVariable("TITLE", $vid->getTitle());
        $tpl->setVariable("WIDTH", 
            $tpom->get(TubePressEmbeddedOptions::EMBEDDED_WIDTH));
        
        $tpl->parse('bigVideo');
    }
    
    /**
     * Handles the parsing of pagination links ("next" and "prev")
     * 
     * @param int                     $vidCount The grand total video count
     * @param TubePressOptionsManager $tpom     The TubePress options manager
     * @param HTML_Template_IT        &$tpl     The HTML template to write to
     * 
     * @return void
     */
    private static function _parsePaginationHTML($vidCount, 
        TubePressOptionsManager $tpom, HTML_Template_IT &$tpl)
    {
        $currentPage = TubePressQueryString::getPageNum();
        $vidsPerPage = $tpom->
            get(TubePressDisplayOptions::RESULTS_PER_PAGE);
    
        $newurl = new Net_URL(TubePressQueryString::fullURL());
        $newurl->removeQueryString("tubepress_page");
 
        $pagination = diggstyle_getPaginationString($currentPage, $vidCount,
            $vidsPerPage, 1, $newurl->getURL(), 
                "tubepress_page");
            
        $tpl->setVariable('TOPPAGINATION', $pagination);
        $tpl->setVariable('BOTPAGINATION', $pagination);
    }
    
    /**
     * The main method for printing out a single video 
     * thumbnail and the meta information for it
     * 
     * @param TubePressVideo          $vid  The video to parse
     * @param TubePressOptionsManager $tpom The TubePress options manager
     * @param HTML_Template_IT        &$tpl The HTML template to write to
     * 
     * @return void
     */
    private static function _parseSmallVideoHTML(TubePressVideo $vid, 
        TubePressOptionsManager $tpom, HTML_Template_IT &$tpl)
    {
        $playerName   = $tpom->
            get(TubePressDisplayOptions::CURRENT_PLAYER_NAME);
        $player       = TubePressPlayer::getInstance($playerName);
        $randomizeOpt = $tpom->
            get(TubePressAdvancedOptions::RANDOM_THUMBS);
        $thumbWidth   = $tpom->
            get(TubePressDisplayOptions::THUMB_WIDTH);
        $thumbHeight  = $tpom->
            get(TubePressDisplayOptions::THUMB_HEIGHT);
        $height       = $tpom->
            get(TubePressEmbeddedOptions::EMBEDDED_HEIGHT);
        $width        = $tpom->
            get(TubePressEmbeddedOptions::EMBEDDED_WIDTH);
        
        $playLink = $player->getPlayLink($vid, $tpom);

        $tpl->setVariable('IMAGEPLAYLINK', $playLink);
        $tpl->setVariable('IMAGETITLE', $vid->getTitle());
        
        TubePressMetaProcessor::process($vid, $tpom, $playLink, $tpl);
        
        if ($randomizeOpt) {
            $tpl->setVariable('THUMBURL', $vid->getRandomThumbURL());
        } else {
             $tpl->setVariable('THUMBURL', $vid->getDefaultThumbURL());
        }    
        
        $tpl->setVariable('THUMBWIDTH', $thumbWidth);
        $tpl->setVariable('THUMBHEIGHT', $thumbHeight);
        
        $tpl->parse('thumb');
    }
    
    /**
     * Creates the HTML for a single video retrieved from YouTube
     * 
     * @param DOMDocument             $rss          The RSS retrieved from 
     *                                                YouTube
     * @param int                     $index        The index (in the RSS) 
     *                                               of the video we're going to
     *                                               parse
     * @param int                     $totalResults The total number of results 
     *                                               that we got back for this 
     *                                               query
     * @param TubePressOptionsManager $tpom         The TubePress options manager
     * @param HTML_Template_IT        &$tpl         The HTML template to write to
     * 
     * @return string The HTML for a single video returned from YouTube
     */
    private static function _parseVideo(DOMDocument $rss, 
        $index, $totalResults, TubePressOptionsManager $tpom, 
        HTML_Template_IT &$tpl)
    {

        /* Create a TubePressVideo object from the XML */
        $video = new TubePressVideo(
            $rss->getElementsByTagName('entry')->item($index));
            
        /* Top of the gallery is special */
        if ($index == 0) {
            TubePressGallery::_parseBigVidHTML($video, $tpom, $tpl);
        }
            
        /* Here's where each thumbnail gets printed */
        TubePressGallery::_parseSmallVideoHTML($video, $tpom, $tpl);        
    }
    
    private static function _getDisplayOrder(TubePressOptionsManager $tpom, $vidLimit) 
    {
    	
    	$toReturn = array();
		for ($y = 0; $y < $vidLimit; $y++) {
    		$toReturn[] = $y;
    	}
    	if ($tpom->get(TubePressDisplayOptions::ORDER_BY) == "random") {
    		shuffle($toReturn);
    	}
    	
		return $toReturn;
    }

}