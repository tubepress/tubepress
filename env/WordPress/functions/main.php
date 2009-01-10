<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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

/**
 * Main filter hook. Looks for a tubepress tag
 * and, if found, replaces it with a gallery
*/
function tubepress_content_filter($content = '')
{
	try {
		return _tubepress_content_filter($content);
	} catch (Exception $e) {
		return $e->getMessage() . $content;
	}
}

function _tubepress_content_filter($content) {

	$wpsm             = new WordPressStorageManager();
    $trigger          = $wpsm->get(TubePressAdvancedOptions::KEYWORD);
	$shortcodeService = new SimpleTubePressShortcodeService();
    
	/* no shortcode? get out */
    if (!$shortcodeService->somethingToParse($content, $trigger)) {
	    return $content;
	}

	/* Store everything we generate in the following string */
    $newcontent = $content;
    
    while ($shortcodeService->somethingToParse($newcontent, $trigger)) {
 
	    $tpom = new SimpleTubePressOptionsManager();
	    $tpom->setStorageManager($wpsm);
	    $shortcodeService->parse($newcontent, $tpom);

	    $gallery = new TubePressGallery();
	    _tubepress_dependency_inject($gallery, $tpom);
    	if (TubePressDebug::areWeDebugging($tpom)) {
	        TubePressDebug::execute($gallery, $tpom);
	    }

	    /* replace the tag with our new content */
	    $newcontent = TubePressStringUtils::replaceFirst($tpom->getShortcode(), 
	        $gallery->generate(), $newcontent);
    }
    
    return $newcontent;
}

/**
 * Spits out the CSS and JS files that we always need for TubePress
 */
function tubepress_head_filter()
{
	try {
	    _tubepress_head_filter();
	} catch (Exception $e) {
		/* this is in the head, so just print an HTML comment and proceed */
        print "<!-- " . $e->getMessage() . " -->";
	}
}

function _tubepress_head_filter() {
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
    $factory    = new SimpleTubePressPlayerFactory();
    $player     = $factory->getInstance($playerName);
    print $player->getHeadContents();
}

function _tubepress_dependency_inject(TubePressGallery $gallery, 
    TubePressOptionsManager $tpom)
{
	$cacheService          = new SimpleTubePressCacheService();
	$embedService          = new SimpleTubePressEmbeddedPlayerService();
	$feedInspectionService = new SimpleTubePressFeedInspectionService();
	$feedRetrievalService  = new TubePressFeedRetrievalService_HTTP_Request2();
	$messageService        = new WordPressMessageService();
	$playerFactory         = new SimpleTubePressPlayerFactory();
	$queryStringService    = new SimpleTubePressQueryStringService();
	$paginationService     = new TubePressPaginationService_DiggStyle();
	$thumbService          = new SimpleTubePressThumbnailService();
	$urlBuilderService     = new SimpleTubePressUrlBuilder();
    $videoFactory          = new SimpleTubePressVideoFactory();
	
	$thumbService->setOptionsManager($tpom);
    $thumbService->setMessageService($messageService);
    $urlBuilderService->setOptionsManager($tpom);
    $paginationService->setMessageService($messageService);
    $paginationService->setOptionsManager($tpom);
    $paginationService->setQueryStringService($queryStringService);
    $feedRetrievalService->setCacheService($cacheService);
	$gallery->setFeedInspectionService($feedInspectionService);
	$gallery->setFeedRetrievalService($feedRetrievalService);
	$gallery->setOptionsManager($tpom);
	$gallery->setPaginationService($paginationService);
	$gallery->setPlayerFactory($playerFactory);
	$gallery->setQueryStringService($queryStringService);
	$gallery->setEmbeddedPlayerService($embedService);
	$gallery->setThumbnailService($thumbService);
	$gallery->setUrlBuilderService($urlBuilderService);
	$gallery->setVideoFactory($videoFactory);
}

?>