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
abstract class TubePressGallery
    implements TubePressHasDescription,
        TubePressHasName, TubePressHasTitle
{
    
    const PAGE_PARAM = "tubepress_page";

    /* this gallery's description */
    private $_description;
    
    /* this gallery's name */
    private $_name;
    
    /* this gallery's title */
    private $_title;
    
    /**
     * Generates the content of this gallery
     * 
     * @param TubePressStorage_v160 $stored The TubePress storage 
     *        object containing all the user's options
     * 
     * @return The HTML content for this gallery
     */
    public final function generate(TubePressStorage_v160 $stored)
    {
        /* load up the gallery template */
        $tpl = new HTML_Template_IT(dirname(__FILE__) . "/../../ui");
        if (!$tpl->loadTemplatefile("gallery.tpl.html", true, true)) {
            throw new Exception("Couldn't load gallery template");
        }
        
        /* get the videos as an array */
        $xml = TubePressGallery::_getRss($stored);

        TubePressGallery::_countResults($xml, $totalResults, $thisResult);
        
        /* Figure out how many videos we're going to show */
        $vidLimit =
            $stored->getCurrentValue(TubePressDisplayOptions::RESULTS_PER_PAGE);
        if ($thisResult < $vidLimit) {
            $vidLimit = $thisResult;
        }   
        
        /* parse 'em out */
        for ($x = 0; $x < $vidLimit; $x++) {
            TubePressGallery::_parseVideo($xml, $x, 
                $totalResults, $stored, $tpl);
        }
        
        /* Spit out the top/bottom pagination */
        TubePressGallery::_parsePaginationHTML($totalResults, $stored, $tpl);

        return $tpl->get();
    }
    
    /**
     * Defines where to fetch this gallery's feed
     * 
     * @return The location of this gallery's feed from YouTube 
     */
    protected abstract function getRequestURL();
    
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
            $xml->getElementsByTagNameNS('http://a9.com/-/spec' . \
            '/opensearchrss/1.0/', 'totalResults')->item(0)->nodeValue;
        
        /* see if we got any */
        if ($totalResults == 0) {
            throw new Exception("YouTube returned no videos for your query!");
        }
        
        $thisResult = $xml->getElementsByTagName('entry')->length;
    }
    
    /**
     * Fetches the RSS from YouTube (or from cache)
     * 
     * @param TubePressStorage_v160 $stored The TubePress storage object
     * 
     * @return DOMDocument The raw RSS from YouTube
     */
    private function _getRss(TubePressStorage_v160 $stored)
    {
        /* Grab the video XML from YouTube */
        $request = $this->getRequestURL();
        TubePressGallery::_urlPostProcessing($request, $stored);
        
        $cache = new Cache_Lite(array("cacheDir" => sys_get_temp_dir()));

        
        if (!($data = $cache->get($request))) {
            $req =& new HTTP_Request($request);
            if (!PEAR::isError($req->sendRequest())) {
                $data = $req->getResponseBody();
            }
            $cache->save($data, $request);
        }
        
        $doc = new DOMDocument();
        
        if (substr($data, 0, 5) != "<?xml") {
            return $doc;
        }
    
        $doc->loadXML($data);
        return $doc;
    }
    
    /**
     * Prints out an embedded video at the top of the gallery.
     * Used in "normal" video playing mode only
     * 
     * TODO: move to normal player class? maybe an abstract method on player?
     * 
     * @param TubePressVideo        $vid    The video to parse
     * @param TubePressStorage_v160 $stored The TubePressStorage object
     * @param HTML_Template_IT      &$tpl   HTML template to write to
     * 
     * @return void
     */
    private function _parseBigVidHTML(TubePressVideo $vid, 
        TubePressStorage_v160 $stored, HTML_Template_IT &$tpl)
    {    

        /* we only do this stuff if we're operating in "normal" play mode */
        $playerName =
            $stored->
                getCurrentValue(TubePressDisplayOptions::CURRENT_PLAYER_NAME);
        $player     = TubePressPlayer::getInstance($playerName);
        
        //TODO: this is hacky
        if (!($player instanceof TPNormalPlayer)) {
            return;
        }
        
        $embed = new TubePressEmbeddedPlayer($vid, $stored);
        $tpl->setVariable("EMBEDSRC", $embed->toString());
        $tpl->setVariable("TITLE", $vid->getTitle());
        $tpl->setVariable("WIDTH", 
            $stored->getCurrentValue(TubePressEmbeddedOptions::EMBEDDED_WIDTH));
        
        $tpl->parse('bigVideo');
    }
    
    /**
     * Handles the parsing of pagination links ("next" and "prev")
     * 
     * @param int                   $vidCount The grand total video count
     * @param TubePressStorage_v160 $stored   The TubePressStorage object
     * @param HTML_Template_IT      &$tpl     The HTML template to write to
     * 
     * @return void
     */
    private static function _parsePaginationHTML($vidCount, 
        TubePressStorage_v160 $stored, HTML_Template_IT &$tpl)
    {
        $currentPage = TubePressStatic::getPageNum();
        $vidsPerPage = $stored->
            getCurrentValue(TubePressDisplayOptions::RESULTS_PER_PAGE);
    
        $newurl = new Net_URL(TubePressStatic::fullURL());
        $newurl->removeQueryString(TubePressGallery::PAGE_PARAM);
 
        $pagination = diggstyle_getPaginationString($currentPage, $vidCount,
            $vidsPerPage, 1, $newurl->getURL(), 
                TubePressGallery::PAGE_PARAM);
            
        $tpl->setVariable('TOPPAGINATION', $pagination);
        $tpl->setVariable('BOTPAGINATION', $pagination);
    }
    
    /**
     * The main method for printing out a single video 
     * thumbnail and the meta information for it
     * 
     * @param TubePressVideo        $vid    The video to parse
     * @param TubePressStorage_v160 $stored The TubePressStorage object
     * @param HTML_Template_IT      &$tpl   The HTML template to write to
     * 
     * @return void
     */
    private static function _parseSmallVideoHTML(TubePressVideo $vid, 
        TubePressStorage_v160 $stored, HTML_Template_IT &$tpl)
    {
        $playerName   = $stored->
            getCurrentValue(TubePressDisplayOptions::CURRENT_PLAYER_NAME);
        $player       = TubePressPlayer::getInstance($playerName);
        $randomizeOpt = $stored->
            getCurrentValue(TubePressAdvancedOptions::RANDOM_THUMBS);
        $thumbWidth   = $stored->
            getCurrentValue(TubePressDisplayOptions::THUMB_WIDTH);
        $thumbHeight  = $stored->
            getCurrentValue(TubePressDisplayOptions::THUMB_HEIGHT);
        $height       = $stored->
            getCurrentValue(TubePressEmbeddedOptions::EMBEDDED_HEIGHT);
        $width        = $stored->
            getCurrentValue(TubePressEmbeddedOptions::EMBEDDED_WIDTH);
        
        $playLink = $player->getPlayLink($vid, $stored);

        $tpl->setVariable('IMAGEPLAYLINK', $playLink);
        $tpl->setVariable('IMAGETITLE', $vid->getTitle());
        
        TubePressMetaProcessor::process($vid, $stored, $playLink, $tpl);
        
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
     * @param DOMDocument           $rss          The RSS retrieved from 
     * 	                                           YouTube
     * @param int                   $index        The index (in the RSS) 
     *                                             of the video we're going to
     *                                             parse
     * @param int                   $totalResults The total number of results 
     *                                             that we got back for this 
     *                                             query
     * @param TubePressStorage_v160 $stored       The TubePress storage object
     * @param HTML_Template_IT      &$tpl         The HTML template to write to
     * 
     * @return string The HTML for a single video returned from YouTube
     */
    private static function _parseVideo(DOMDocument $rss, 
        $index, $totalResults, TubePressStorage_v160 $stored, 
        HTML_Template_IT &$tpl)
    {

        /* Create a TubePressVideo object from the XML */
        $video = new TubePressVideo(
            $rss->getElementsByTagName('entry')->item($index));
            
        /* Top of the gallery is special */
        if ($index == 0) {
            TubePressGallery::_parseBigVidHTML($video, $stored, $tpl);
        }
            
        /* Here's where each thumbnail gets printed */
        TubePressGallery::_parseSmallVideoHTML($video, $stored, $tpl);        
    }
    
    /**
     * Appends some global query parameters on the request
     * before we fire it off to YouTube
     *
     * @param string                &$request The request to be manipulated
     * @param TubePressStorage_v160 $stored   The TubePressStorage object
     * 
     * @return void
     */
    private static function _urlPostProcessing(&$request, 
        TubePressStorage_v160 $stored)
    {
        
        $perPage = $stored->
            getCurrentValue(TubePressDisplayOptions::RESULTS_PER_PAGE);
        $filter  = $stored->getCurrentValue(TubePressAdvancedOptions::FILTER);
        $order   = $stored->getCurrentValue(TubePressDisplayOptions::ORDER_BY);
        $mode    = $stored->getCurrentValue(TubePressGalleryOptions::MODE);
        
        $currentPage = TubePressStatic::getPageNum();
        
        $start = ($currentPage * $perPage) - $perPage + 1;
        
        if ($start + $val > 1000) {
            $val = 1000 - $start;
        }
        
        $requestURL = new Net_URL($request);
        $requestURL->addQueryString("start-index", $start);
        $requestURL->addQueryString("max-results", $perPage);
        
        if ($filter) {
            $requestURL->addQueryString("racy", "exclude");
        } else {
            $requestURL->addQueryString("racy", "include");
        }
      
        if ($mode != TubePressGalleryValue::PLAYLIST) {
            $requestURL->addQueryString("orderby", $order);
        }       
        
        $request = $requestURL->getURL();
    }

    /**
     * Gets the title of this gallery
     * 
     * @return string The title of this gallery
     */
    public final function getTitle()
    {
        return $this->_title;
    }
    
    /**
     * Gets the name of this gallery
     * 
     * @return string The name of this gallery
     */
    public final function getName()
    {
        return $this->_name;
    }
    
    /**
     * Gets the description of this gallery
     * 
     * @return string The description of this gallery
     */
    public final function getDescription()
    {
        return $this->_description;
    }
    
    /**
     * Sets the title of this gallery
     * 
     * @param string $newTitle The new title of the gallery
     * 
     * @return void
     */
    protected final function setTitle($newTitle)
    {
        $this->_title = $newTitle;
    }
    
    /**
     * Sets the name of this gallery
     * 
     * @param string $newName The new name of the gallery
     * 
     * @return void
     */
    protected final function setName($newName)
    { 
        $this->_name = $newName;
    }
    
    /**
     * Sets the description of this gallery
     * 
     * @param string $newDesc The new description of the gallery
     * 
     * @return void
     */
    protected final function setDescription($newDesc)
    {
        $this->_description = $newDesc;
    }
}