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
    
    const pageParameter = "tubepress_page";
    const idParameter = "tubepress_id";
    
    protected abstract $value;
    protected abstract $description;
    protected abstract $name;
    protected abstract $title;
    
    public abstract function getVideos(int $start, int $perPage);
    
    final public function getTitle()       { return $this->title; }
    final public function getName() 	   { return $this->name; }
    final public function getValue() 	   { return $this->value; }
    final public function getDescription() { return $this->description; }
    
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
        /* load up the gallery template */
        $tpl = new HTML_Template_IT(dirname(__FILE__) . "/../templates");
        $tpl->loadTemplatefile("gallery.tpl.html", true, true);
        if (PEAR::isError($tpl)) {
        	return $tpl;
        }

        /* Grab the XML from YouTube */
        $request = TubePressXML::generateGalleryRequest($stored);
        if (PEAR::isError($request)) {
            return $request;
        }
        
        $youtube_xml = TubePressXML::fetch($request, $stored->options);
        
        /* Any HTTP errors? */
        if (PEAR::isError($youtube_xml)) {
            return $youtube_xml;
        }
        
        /* put the XML into a friendly array */
        $videoArray = TubePressXML::toArray($youtube_xml);
        
        /* Any parsing errors? */
        if (PEAR::isError($videoArray)) {
            return $videoArray;
        }
        
        $totalVideoResults = $videoArray['openSearch:totalResults'];
        
        /* keeps track of how many videos we've actually printed */
        $videosPrintedCnt = 0;
        
        if ($totalVideoResults == 0) {
            return "No matching videos!";
        }
        
        /* how many videos we actually got from YouTube */
        if ($totalVideoResults > 1) {
            $videosReturnedCnt = count($videoArray['entry']);
        } else {
            $videosReturnedCnt = 1;
        }

        /* Next few lines figure out how many videos we're going to show */
        $vidsPerPage = $stored->options->get(TP_OPT_VIDSPERPAGE);
        $vidLimit = $vidsPerPage->getValue(); 
        
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
                TubePressGallery::printHTML_bigvid($video, 
                    $stored->options, $tpl);
            }
            
            /* Here's where each thumbnail gets printed */
            TubePressGallery::_printHTML_smallvid($video,
                 $stored, $tpl);
        }
        
                /* Spit out the top/bottom pagination */
        $pagination =  TubePressGallery::_printHTML_pagination(
                $totalVideoResults, 
                $stored->options);
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
    function _printHTML_pagination($vidCount, $options)
    {
        $currentPage = TubePressStatic::getPageNum();
        $vidsPerPage = $options->get(TP_OPT_VIDSPERPAGE);
    
        $newurl = new Net_URL(TubePressStatic::fullURL());
        $newurl->removeQueryString(TP_PARAM_PAGE);

        $currentMode = $options->get(TP_OPT_MODE);
        if ($currentMode->getValue() == TP_MODE_TAG) {
            $vidCount = min($vidCount, 1000);
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
}
?>