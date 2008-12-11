<?php

/**
 * Main filter hook. Looks for a tubepress tag
 * and, if found, replaces it with a gallery
*/
function tp_main($content = '')
{
	try {
		return _tp_main($content);
	} catch (Exception $e) {
		return $e->getMessage();;	
	}
}

function _tp_main($content) {

	$wpsm = new WordPressStorageManager();
    $trigger = $wpsm->get(TubePressAdvancedOptions::KEYWORD);
	$shortcodeService = new SimpleTubePressShortcodeService();
	$messageService = new WordPressMessageService();
    
    if (!$shortcodeService->somethingToParse($content, $trigger)) {
	    return $content;
	}

	/* Store everything we generate in the following string */
    $newcontent = $content;
    
    while ($shortcodeService->somethingToParse($newcontent, $trigger)) {
 
	    $tpom = new SimpleTubePressOptionsManager();
	    $tpom->setStorageManager($wpsm);
	    $shortcodeService->parse($newcontent, $tpom);
	        
	    if (TubePressDebug::areWeDebugging($tpom)) {
	        TubePressDebug::execute($tpom, $wpsm);
	    }

	    $modeName = $tpom->get(TubePressGalleryOptions::MODE);
	    $gallery = new TubePressGallery();
	    _tp_setGalleryInterfaces($gallery, $tpom);
	    
	    /* replace the tag with our new content */
	    $newcontent = TubePressStringUtils::replaceFirst($tpom->getShortcode(), 
	        $gallery->generate(), $newcontent);
    }
    
    return $newcontent;
}

/**
 * Spits out the CSS and JS files that we always need for TubePress
 */
function tp_insertCSSJS()
{
	try {
	    _tp_insertCSSJS();
	} catch (Exception $e) {
		/* this is in the head, so just print an HTML comment and proceed */
        print "<!-- " . $e->getMessage() . " -->";
	}
}

function _tp_insertCSSJS() {
    global $tubepress_base_url;
    print<<<GBS
        <script type="text/javascript" src="$tubepress_base_url/common/js/tubepress.js"></script>
        <link rel="stylesheet" href="$tubepress_base_url/common/css/tubepress.css" 
            type="text/css" />
       <link rel="stylesheet" href="$tubepress_base_url/common/css/tubepress_widget.css" 
            type="text/css" />
        <link rel="stylesheet" href="$tubepress_base_url/common/css/pagination.css" 
            type="text/css" />
GBS;

    $wpsm = new WordPressStorageManager();
    
    if ($wpsm->get(TubePressAdvancedOptions::KEYWORD) === NULL) {
        return;
    }
    
    $playerName = $wpsm->get(TubePressDisplayOptions::CURRENT_PLAYER_NAME);
    $player = TubePressPlayer::getInstance($playerName);
    print $player->getHeadContents();
}

function _tp_setGalleryInterfaces(TubePressGallery $gallery, TubePressOptionsManager $tpom)
{
	$messageService = new WordPressMessageService();
	
	$thumbService = new SimpleTubePressThumbnailService();
    $thumbService->setOptionsManager($tpom);
    $thumbService->setMessageService($messageService);
    	
    $queryStringService = new SimpleTubePressQueryStringService();
    	
    $urlBuilderService = new SimpleTubePressUrlBuilder();
    $urlBuilderService->setOptionsManager($tpom);
    $urlBuilderService->setQueryStringService($queryStringService);
    	
    $paginationService = new TubePressPaginationService_DiggStyle();
    $paginationService->setMessageService($messageService);
    $paginationService->setOptionsManager($tpom);
    $paginationService->setQueryStringService($queryStringService);
    	
    $gallery->setCacheService(			 new SimpleTubePressCacheService());
	$gallery->setFeedInspectionService( new SimpleTubePressFeedInspectionService());
	$gallery->setFeedRetrievalService(	 new TubePressFeedRetrievalService_HTTP_Request2());
	$gallery->setOptionsManager(		 $tpom);
	$gallery->setPaginationService(	 $paginationService);
	$gallery->setThumbnailService(		 $thumbService);
	$gallery->setUrlBuilderService(	 $urlBuilderService);
	$gallery->setVideoFactory(			 new SimpleTubePressVideoFactory());
}

?>