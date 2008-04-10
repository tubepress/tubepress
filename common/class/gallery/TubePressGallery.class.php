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
    || require(dirname(__FILE__) . "/../../../lib/diggstyle_function.php");
    
abstract class TubePressGallery
	implements TubePressHasDescription,
		TubePressHasName, TubePressHasTitle {
    
    const pageParameter = "tubepress_page";

    /* some galleries need user input */
    private $value;
    
    /* this gallery's description */
    private $description;
    
    /* this gallery's name */
    private $name;
    
    /* this gallery's title */
    private $title;
    
    /* defines where to fetch this gallery's feed */
    protected abstract function getRequestURL();
    
    public final function generate(TubePressStorage_v157 $stored)
    {
        /* load up the gallery template */
        $tpl = new HTML_Template_IT(dirname(__FILE__) . "/../../ui");
        if (!$tpl->loadTemplatefile("gallery.tpl.html", true, true)) {
            throw new Exception("Couldn't load gallery template");
        }
        
        /* get the videos as an array */
        $xml = TubePressGallery::getRss($stored);

        TubePressGallery::countResults($xml, $totalResults, $thisResult);
        
        /* Figure out how many videos we're going to show */
        $vidLimit = $stored->getCurrentValue(TubePressDisplayOptions::resultsPerPage);
        if ($thisResult < $vidLimit) {
            $vidLimit = $thisResult;
        }   
        
        /* parse 'em out */
        for ($x = 0; $x < $vidLimit; $x++) {
            TubePressGallery::parseVideo($xml, $x, $totalResults, $stored, $tpl);
        }
        
        /* Spit out the top/bottom pagination */
        TubePressGallery::parsePaginationHTML($totalResults, $stored, $tpl);

        return $tpl->get();
    }
    
    private static function parseVideo(DOMDocument $rss, $index, $totalResults, TubePressStorage_v157 $stored, HTML_Template_IT &$tpl) {

        /* Create a TubePressVideo object from the XML */
        $video = new TubePressVideo($rss->getElementsByTagName('entry')->item($index));
            
        /* Top of the gallery is special */
        if ($index == 0) {
            TubePressGallery::parseBigVidHTML($video, $stored, $tpl);
        }
            
        /* Here's where each thumbnail gets printed */
        TubePressGallery::parseSmallVideoHTML($video, $stored, $tpl);        
    }
    
    private static function countResults(DOMDocument $xml, &$totalResults, &$thisResult)
    {
        /* how many YouTube said we got */
        $totalResults = $xml->getElementsByTagNameNS(
        	'http://a9.com/-/spec/opensearchrss/1.0/',
        	'totalResults'
        )->item(0)->nodeValue;
        
        /* see if we got any */
        if ($totalResults == 0) {
            throw new Exception("YouTube returned no videos for your query!");
        }
        
		$thisResult = $xml->getElementsByTagName('entry')->length;
    }
    
    private function getRss(TubePressStorage_v157 $stored)
    {
        /* Grab the video XML from YouTube */
        $request = $this->getRequestURL();
        TubePressGallery::urlPostProcessing($request, $stored);
        
		$Cache_Lite = new Cache_Lite(array("cacheDir" => sys_get_temp_dir()));

		
		if (!($data = $Cache_Lite->get($request))) {
			$req =& new HTTP_Request($request);
			if (!PEAR::isError($req->sendRequest())) {
    			$data = $req->getResponseBody();
			}
			$Cache_Lite->save($data, $request);
		}
        
		$doc = new DOMDocument();
    	$doc->loadXML($data);
    	return $doc;
    }

    
     /**
     * Prints out an embedded video at the top of the gallery.
     * Used in "normal" video playing mode only.
     * 
     * @param vid A TubePressVideo object of the video we're going to play
     * @param options A TubePressTag object holding all of our options
     * @param tpl Our template object
     */
    private function parseBigVidHTML(TubePressVideo $vid, TubePressStorage_v157 $stored, HTML_Template_IT &$tpl)
    {    

        /* we only do this stuff if we're operating in "normal" play mode */
        $playerName = $stored->getCurrentValue(TubePressDisplayOptions::currentPlayerName);
        $player = TubePressPlayer::getInstance($playerName);
        if (!($player instanceof TPNormalPlayer)) {
            return;
        }
        
        
        $embed = new TubePressEmbeddedPlayer($vid, $stored);
        $tpl->setVariable("EMBEDSRC", $embed->toString());
        $tpl->setVariable("TITLE", $vid->getTitle());
        $tpl->setVariable("WIDTH", $stored->getCurrentValue(TubePressEmbeddedOptions::embeddedWidth));
        
        $tpl->parse('bigVideo');
    }

    /**
     * Handles the logic and printing of pagination links ("next" and "prev")
     * 
     * @param vidCount The grand total video count
     * @param options Current options
     */
    private static function parsePaginationHTML($vidCount, TubePressStorage_v157 $stored, HTML_Template_IT &$tpl)
    {
        $currentPage = TubePressStatic::getPageNum();
        $vidsPerPage = $stored->getCurrentValue(TubePressDisplayOptions::resultsPerPage);
    
        $newurl = new Net_URL(TubePressStatic::fullURL());
        $newurl->removeQueryString(TubePressGallery::pageParameter);
 
        $pagination = diggstyle_getPaginationString($currentPage, $vidCount,
            $vidsPerPage, 1, $newurl->getURL(), TubePressGallery::pageParameter);
            
        $tpl->setVariable('TOPPAGINATION', $pagination);
        $tpl->setVariable('BOTPAGINATION', $pagination);
    }
    
    /**
     * The main wrapper method for printing out a single video 
     * thumbnail and the meta information for it.
     * 
     * @param TubePressVideo vid 
     * @param TubePressStorage options
     * @param HTML_Template_IT tpl
     */
    private static function parseSmallVideoHTML(TubePressVideo $vid, TubePressStorage_v157 $stored, HTML_Template_IT &$tpl)
    {
        $playerName = $stored->getCurrentValue(TubePressDisplayOptions::currentPlayerName);
        $player = TubePressPlayer::getInstance($playerName);
        $randomizeOpt = $stored->getCurrentValue(TubePressAdvancedOptions::randomThumbs);
        
        $thumbWidth = $stored->getCurrentValue(TubePressDisplayOptions::thumbWidth);
        $thumbHeight = $stored->getCurrentValue(TubePressDisplayOptions::thumbHeight);
        
        $height = $stored->getCurrentValue(TubePressEmbeddedOptions::embeddedHeight);
        $width = $stored->getCurrentValue(TubePressEmbeddedOptions::embeddedWidth);
        
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
     * Appends some global query parameters on the request
     * before we fire it off to YouTube
     *
     * @param string $request The request to be manipulated (pass by reference)
     * @param TubePressStorage $stored The TubePress options
     */
    private static function urlPostProcessing(&$request, TubePressStorage_v157 $stored) {
        
        $perPage = $stored->getCurrentValue(TubePressDisplayOptions::resultsPerPage);
        $filter = $stored->getCurrentValue(TubePressAdvancedOptions::filter);
        $order = $stored->getCurrentValue(TubePressDisplayOptions::orderBy);
        $mode = $stored->getCurrentValue(TubePressGalleryOptions::mode);
        
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
      
        if ($mode != TubePressGalleryValue::playlist) {
        	$requestURL->addQueryString("orderby", $order);
        }       
        
        $request = $requestURL->getURL();
    }
    
    /* getters */
    public final function getTitle()       { return $this->title; }
    public final function getName() 	   { return $this->name; }
    public final function getDescription() { return $this->description; }
    
    /* setters */
    protected final function setTitle($newTitle) { $this->title = $newTitle; }
    protected final function setName($newName) { $this->name = $newName; }
    protected final function setDescription($newDesc) { $this->description = $newDesc; }
    
}
?>