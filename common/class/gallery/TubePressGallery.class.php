<?php
abstract class TubePressGallery
	implements TubePressValue, TubePressHasValue, TubePressHasDescription,
		TubePressHasName, TubePressHasTitle {
	
	/* All valid modes here */
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
    
    /* query string parameter for paging */
    const pageParameter = "tubepress_page";
    
    /* some galleries need user input */
    private $value;
    
    /* this gallery's description */
    private $description;
    
    /* this gallery's name */
    private $name;
    
    /* this gallery's title */
    private $title;
    
    protected abstract function getRequestURL();
    
    /* getters */
    public final function getTitle()       { return $this->title; }
    public final function getName() 	   { return $this->name; }
    public final function getValue() 	   { return $this->value; }
    public final function getDescription() { return $this->description; }
    
    /* setters */
    protected final function setTitle($newTitle) {
        $this->title = $newTitle;
    }
    protected final function setName($newName) {
        $this->name = $newName;
    }
    protected final function setValue($newValue) {
        $this->value = $newValue;
    }
    protected final function setDescription($newDesc) {
        $this->description = $newDesc;
    }
    
	/*
     * This is the main function that we expose, so we have to
     * do some ugly error checking first.
     */
    public final function generate($storage)
    {
        if (!is_a($stored, "TubePressStorage")) {
        	throw new Exception("Wrong data type");
        }
        
        return TubePressGallery::_generate($stored);
    }
    
    /**
     * This is the main method. At this point we must assume that our options
     * are valid.
     */
    private function _generate($stored)
    {           
        /* load up the gallery template */
        $tpl = HTML_Template_IT::HTML_Template_IT(dirname(__FILE__) . "/../../ui");
        if (!$tpl->loadTemplatefile("gallery.tpl.html", true, true)) {
            throw new Exception("Couldn't load gallery template");
        }

        /* Grab the video XML from YouTube */
        $request = $this->getRequestURL();
        TubePressGallery::urlPostProcessing($request, $stored);
        $youtube_xml = TubePressXML::fetch($request, $stored);
        
        /* put the XML into an array */
        $videoArray = TubePressXML::toArray($youtube_xml);
        
        /* how many YouTube said we got */
        $totalVideoResults = $videoArray['openSearch:totalResults'];
        
        /* how many videos we've actually printed */
        $videosPrintedCnt = 0;
        
        /* see if we got any */
        if ($totalVideoResults == 0) {
            return "No matching videos!";
        }
        
        
        if ($totalVideoResults > 1) {
            $videosReturnedCnt = count($videoArray['entry']);
        } else {
            $videosReturnedCnt = 1;
        }

        /* Figure out how many videos we're going to show */
        $vidLimit = $stored->getDisplayOptions()->get(TubePressDisplayOptions::resultsPerPage)->getCurrentValue();
        if ($videosReturnedCnt < $vidLimit) {
            $vidLimit = $videosReturnedCnt;
        }
        
        for ($x = 0; $x < $vidLimit; $x++) {

            /* Create a TubePressVideo object from the XML (if we can) */
            if ($totalVideoResults == 1) {
                $video = new TubePressVideo($videoArray['entry']);
            } else {
                 $video = new TubePressVideo($videoArray['entry'][$x]);
            }
            
            /* Top of the gallery is special */
            if ($videosPrintedCnt++ == 0) {
                $this->parseBigVidHTML($video, 
                    $stored, $tpl);
            }
            
            /* Here's where each thumbnail gets printed */
            $this->parseSmallVidHTML($video, $stored, $tpl);
        }
        
        /* Spit out the top/bottom pagination */
        $pagination =  TubePressGallery::getPaginationHTML(
                $totalVideoResults, 
                $stored);
        $tpl->setVariable('TOPPAGINATION', $pagination);
        $tpl->setVariable('BOTPAGINATION', $pagination);

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
    private function parseBigVidHTML($vid, $stored, &$tpl)
    {    
        /* we only do this stuff if we're operating in "normal" play mode */
        if (!is_a($this, "TubePressNormalGallery")) {
            return;
        }
        
        $dispOptions = $stored->getDisplayOptions();
        $width = $dispOptions->get(TubePressDisplayOptions::mainVidWidth)->getCurrentValue();
        $height = $dispOptions->get(TubePressDisplayOptions::mainVidHeight)->getCurrentValue();
        
        $tpl->setVariable('WIDTH', $width);
        $tpl->setVariable('TITLE', $vid->getTitle());
        $tpl->setVariable('HEIGHT', $height);
        $tpl->setVariable('ID', $vid->getId());
        $tpl->parse('bigVideo');
    }
   
    /**
     * Prints out video meta information below a video thumbnail. This
     * function needs a makeover at some point.
     * 
     * @param vid A TubePressVideo object of the video in question
     * @param options A TubePressTag object holding all of our options
     * @param link The attributes of the anchor for the title text
     * @param tpl
     */
    private static function parseMetaHTML($vid, $stored, &$tpl)
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
        //$comment = $options->get(TP_VID_COMMENT_CNT);
        //if ($comment->getValue()) {
         //   $opt = $options->get(TP_VID_COMMENT_CNT);
        //    $tpl->setVariable('METANAME', $opt->getTitle());
        //    $tpl->setVariable('COUNT', $vid->getCommentCount());
        //    $tpl->setVariable('ID', $vid->getId());
        //    $tpl->parse('comments');
        //}
        
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
    private static function getPaginationHTML($vidCount, $stored)
    {
        $currentPage = TubePressStatic::getPageNum();
        $vidsPerPage = $stored->getDisplayOptions->get(TubePressDisplayOptions::resultsPerPage)->getCurrentValue();
    
        $newurl = new Net_URL(TubePressStatic::fullURL());
        $newurl->removeQueryString(TubePressGallery::pageParameter);
 
        return diggstyle_getPaginationString($currentPage, $vidCount,
            $vidsPerPage, 1, $newurl->getURL(), TubePressGallery::pageParameter);
    }
    
    /**
     * The main wrapper method for printing out a single video 
     * thumbnail and the meta information for it.
     * 
     * @param TubePressVideo vid 
     * @param TubePressStorage options
     * @param HTML_Template_IT tpl
     */
    private static function parseSmallVideoHTML($vid, $stored, &$tpl)
    {
        $playerOpt = $stored->options->get(TP_OPT_PLAYIN);
        $playerObj = $stored->players->get($playerOpt->getValue());
        $playLink = $playerObj->getPlayLink($vid, $stored->options);
        
        $randomizeOpt = $stored->options->get(TP_OPT_RANDOM_THUMBS);
        
        TubePressGallery::_printHTML_metaInfo($vid, $stored->options,$playLink, $tpl, $whichThumb);
        
        $height = $stored->options->get(TP_OPT_THUMBHEIGHT);
        $width = $stored->options->get(TP_OPT_THUMBWIDTH);
        
        $tpl->setVariable('PLAYLINK', $playLink);
        $tpl->setVariable('TITLE', $vid->getTitle());
        
        if ($randomizeOpt->getValue()) {
            $tpl->setVariable('THUMBURL', $vid->getThumbURL());
        } else {
             $tpl->setVariable('THUMBURL', $vid->getThumbURL(0));
        }    
        
        $tpl->setVariable('THUMBWIDTH', $width->getValue());
        $tpl->setVariable('THUMBHEIGHT', $height->getValue());
        
		$tpl->parse('thumb');
    }
    
    /**
     * Appends some global query parameters on the request
     * before we fire it off to YouTube
     *
     * @param string $request The request to be manipulated (pass by reference)
     * @param TubePressStorage $stored The TubePress options
     */
    private static function urlPostProcessing(&$request, $stored) {
        
        $perPage = $stored->getDisplayOptions->get(TubePressDisplayOptions::resultsPerPage)->getCurrentValue();
        $filter = $stored->getAdvancedOptions->get(TubePressAdvancedOptions::filter)->getCurrentValue();
        $order = $stored->getDisplayOptions->get(TubePressDisplayOptions::orderBy)->getCurrentValue();
        
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
}
?>