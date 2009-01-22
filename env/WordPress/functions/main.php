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

	$wpsm             = new org_tubepress_options_storage_WordPressStorageManager();
    $trigger          = $wpsm->get(org_tubepress_options_category_Advanced::KEYWORD);
	$shortcodeService = new org_tubepress_shortcode_SimpleShortcodeService();
    
	/* no shortcode? get out */
    if (!$shortcodeService->somethingToParse($content, $trigger)) {
	    return $content;
	}

	/* Store everything we generate in the following string */
    $newcontent = $content;
    
    while ($shortcodeService->somethingToParse($newcontent, $trigger)) {
 
	    $tpom = new org_tubepress_options_manager_SimpleOptionsManager();
	    $tpom->setStorageManager($wpsm);
	    $shortcodeService->parse($newcontent, $tpom);

	    $gallery = new org_tubepress_gallery_Gallery();
	    _tubepress_dependency_inject($gallery, $tpom);
    	if (org_tubepress_util_Debug::areWeDebugging($tpom)) {
	        org_tubepress_util_Debug::execute($gallery, $tpom);
	    }

	    /* replace the tag with our new content */
	    $newcontent = org_tubepress_util_StringUtils::replaceFirst($tpom->getShortcode(), 
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
        <link rel="stylesheet" href="$tubepress_base_url/ui/gallery/css/tubepress.css" 
            type="text/css" />
       <link rel="stylesheet" href="$tubepress_base_url/ui/widget/css/tubepress_widget.css" 
            type="text/css" />
        <link rel="stylesheet" href="$tubepress_base_url/ui/gallery/css/pagination.css" 
            type="text/css" />
GBS;

    $wpsm = new org_tubepress_options_storage_WordPressStorageManager();
    
    if ($wpsm->get(org_tubepress_options_category_Advanced::KEYWORD) === NULL) {
        return;
    }
    
    $playerName = $wpsm->get(org_tubepress_options_category_Display::CURRENT_PLAYER_NAME);
    $factory    = new org_tubepress_player_factory_SimplePlayerFactory();
    $player     = $factory->getInstance($playerName);
    print $player->getHeadContents();
}

function _tubepress_dependency_inject(org_tubepress_gallery_Gallery $gallery, 
    org_tubepress_options_manager_OptionsManager $tpom)
{
	$cacheService          = new org_tubepress_cache_SimpleCacheService();
	$embedService          = new org_tubepress_video_embed_SimpleEmbeddedPlayerService();
	$feedInspectionService = new org_tubepress_gdata_inspection_SimpleFeedInspectionService();
	$feedRetrievalService  = new org_tubepress_gdata_retrieval_HTTPRequest2();
	$messageService        = new org_tubepress_message_WordPressMessageService();
	$playerFactory         = new org_tubepress_player_factory_SimplePlayerFactory();
	$queryStringService    = new org_tubepress_querystring_SimpleQueryStringService();
	$paginationService     = new org_tubepress_pagination_DiggStylePaginationService();
	$thumbService          = new org_tubepress_thumbnail_SimpleThumbnailService();
	$urlBuilderService     = new org_tubepress_url_SimpleUrlBuilder();
    $videoFactory          = new org_tubepress_video_factory_SimpleVideoFactory();
	
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