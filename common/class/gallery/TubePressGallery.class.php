<?php

/* we need this function for pagination */
function_exists("diggstyle_getPaginationString")
    || require(dirname(__FILE__) . "/../../../lib/diggstyle_function.php");

abstract class TubePressGallery
	implements TubePressValue, TubePressHasValue, TubePressHasDescription,
		TubePressHasName, TubePressHasTitle {
	
	/* All valid gallery types here */
	const favorites = 	"favorites";
	const tag = 		"tag";
    const related= 		"related";
    const user= 		"user";
    const playlist = 	"playlist";
    const featured = 	"featured";
    const popular = 	"popular";
    const category = 	"category";
    const top_rated = 	"top_rated";
    const mobile = 		"mobile";
    
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
        $videoArray = TubePressGallery::getVideoArray($stored);
        
        TubePressGallery::countResults($videoArray, $totalResults, $thisResult);
        
        /* Figure out how many videos we're going to show */
        $vidLimit = $stored->getCurrentValue(TubePressDisplayOptions::resultsPerPage);
        if ($thisResult < $vidLimit) {
            $vidLimit = $thisResult;
        }   
        
        /* parse 'em out */
        for ($x = 0; $x < $vidLimit; $x++) {
            TubePressGallery::parseVideo($videoArray, $x, $totalResults, $stored, $tpl);
        }
        
        /* Spit out the top/bottom pagination */
        TubePressGallery::parsePaginationHTML($totalResults, $stored, $tpl);

        return $tpl->get();
    }
    
    private static function parseVideo(array $videoArray, $index, $totalResults, TubePressStorage_v157 $stored, HTML_Template_IT &$tpl) {
        
        /* Create a TubePressVideo object from the XML */
        if ($totalResults == 1) {
            $video = new TubePressVideo($videoArray['entry']);
        } else {
             $video = new TubePressVideo($videoArray['entry'][$index]);
        }
            
        /* Top of the gallery is special */
        if ($index == 0) {
            TubePressGallery::parseBigVidHTML($video, $stored, $tpl);
        }
            
        /* Here's where each thumbnail gets printed */
        TubePressGallery::parseSmallVideoHTML($video, $stored, $tpl);        
    }
    
    private static function countResults(array $videoArray, &$totalResults, &$thisResult)
    {
        /* how many YouTube said we got */
        $totalResults = $videoArray['openSearch:totalResults'];
        
        /* see if we got any */
        if ($totalResults == 0) {
            throw new Exception("YouTube returned no videos for your query!");
        }
        
        if ($totalResults > 1) {
            $thisResult = count($videoArray['entry']);
        } else {
            $thisResult = 1;
        }
    }
    
    private function getVideoArray(TubePressStorage_v157 $stored)
    {
        /* Grab the video XML from YouTube */
        $request = $this->getRequestURL();
        TubePressGallery::urlPostProcessing($request, $stored);
        $youtube_xml = TubePressXML::fetch($request, $stored);
        
        /* put the XML into an array */
        return TubePressXML::toArray($youtube_xml);
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
        $player = $stored->getCurrentValue(TubePressDisplayOptions::currentPlayerName);
        if (!($player instanceof TPNormalPlayer)) {
            return;
        }
        
        $width = $stored->getCurrentValue(TubePressDisplayOptions::mainVidWidth);
        $height = $stored->getCurrentValue(TubePressDisplayOptions::mainVidHeight);
        
        $tpl->setVariable('WIDTH', $width);
        $tpl->setVariable('TITLE', $vid->getTitle());
        $tpl->setVariable('HEIGHT', $height);
        $tpl->setVariable('ID', $vid->getId());
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
        $player = $stored->getCurrentValue(TubePressDisplayOptions::currentPlayerName);
        $randomizeOpt = $stored->getCurrentValue(TubePressAdvancedOptions::randomThumbs);
        
        $thumbWidth = $stored->getCurrentValue(TubePressDisplayOptions::thumbWidth);
        $thumbHeight = $stored->getCurrentValue(TubePressDisplayOptions::thumbHeight);
        
        $height = $stored->getCurrentValue(TubePressDisplayOptions::mainVidHeight);
        $width = $stored->getCurrentValue(TubePressDisplayOptions::mainVidWidth);
        
        $playLink = $player->getPlayLink($vid, $height, $width);

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
      
        $requestURL->addQueryString("orderby", $order);       
        
        $request = $requestURL->getURL();
    }
    
    /* getters */
    public final function getTitle()       { return $this->title; }
    public final function getName() 	   { return $this->name; }
    public final function &getValue() 	   { return $this->value; }
    public final function getDescription() { return $this->description; }
    
    /* setters */
    protected final function setTitle($newTitle) { $this->title = $newTitle; }
    protected final function setName($newName) { $this->name = $newName; }
    protected final function setValue(TubePressValue $newValue) { $this->value = $newValue; }
    protected final function setDescription($newDesc) { $this->description = $newDesc; }
    
}
?>