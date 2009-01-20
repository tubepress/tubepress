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
 * Registers TubePress as a widget
 *
 */
function tubepress_init_widget()
{
	$msg = new org_tubepress_message_WordPressMessageService();
	$widget_ops = array('classname' => 'widget_tubepress', 
	    'description' => $msg->_("widget-description"));
	wp_register_sidebar_widget('tubepress', "TubePress", 
	    'tubepress_widget', $widget_ops);
	wp_register_widget_control('tubepress', "TubePress", 
	    'tubepress_widget_control');
}

function tubepress_widget_exception_handler($e) {
	print $e->getMessage();
}

/**
 * Executes the main widget functionality
 *
 * @param unknown_type $opts
 */
function tubepress_widget($opts)
{
	set_exception_handler("tubepress_widget_exception_handler");
	extract($opts);
	
	/* set up the options manager with some sensible defaults */
	$wpsm = new WordPressStorageManager();
	$tpom = new SimpleTubePressOptionsManager();
	$tpom->setStorageManager($wpsm);
	$tpom->setCustomOptions(
	    array(TubePressDisplayOptions::RESULTS_PER_PAGE => 3,
	        TubePressMetaOptions::VIEWS => false,
	        TubePressMetaOptions::DESCRIPTION => true,
	        TubePressDisplayOptions::DESC_LIMIT => 50,
	        TubePressDisplayOptions::CURRENT_PLAYER_NAME => TubePressPlayer::POPUP,
	        TubePressDisplayOptions::THUMB_HEIGHT => 105,
	        TubePressDisplayOptions::THUMB_WIDTH => 135
	        )
	);
	
	/* now apply the user's shortcode */
	$shortcodeService = new SimpleTubePressShortcodeService();
	$shortcodeService->parse($wpsm->get(TubePressWidgetOptions::TAGSTRING), $tpom);
	
	$gallery = new org_tubepress_gallery_WidgetGallery();
	tubepress_widget_inject_deps($gallery, $tpom);
		
	/* get the output */
	$out = $gallery->generate($tpom);

	/* do the standard WordPress widget dance */
	echo $before_widget . $before_title . 
	    $wpsm->get(TubePressWidgetOptions::TITLE) .
	    $after_title . $out . $after_widget;
    restore_exception_handler();
}

/**
 * Handles the widget configuration panel
 *
 */
function tubepress_widget_control() {

	set_exception_handler("tubepress_widget_exception_handler");
	
	if ( $_POST["tubepress-widget-submit"] ) {
		$wpsm = new WordPressStorageManager();
		$wpsm->setValidationService(new SimpleTubePressInputValidationService());
		$wpsm->set(TubePressWidgetOptions::TAGSTRING, 
		    strip_tags(stripslashes($_POST["tubepress-widget-tagstring"])));
		$wpsm->set(TubePressWidgetOptions::TITLE, 
		    strip_tags(stripslashes($_POST["tubepress-widget-title"])));
	}

    /* load up the gallery template */
    $tpl = new HTML_Template_IT(dirname(__FILE__) . "/../../../common/ui/widget");
    if (!$tpl->loadTemplatefile("controls.tpl.html", true, true)) {
        throw new Exception("Couldn't load widget control template");
    }
    
    $wpsm = new WordPressStorageManager();

    $msg = new org_tubepress_message_WordPressMessageService();
    
    $tpl->setVariable("WIDGET-TITLE", 
        $msg->_("options-meta-title-title"));
    $tpl->setVariable("WIDGET-TITLE-VALUE", 
        $wpsm->get(TubePressWidgetOptions::TITLE));
    $tpl->setVariable("WIDGET-TAGSTRING", 
        $msg->_("widget-tagstring-description"));
    $tpl->setVariable("WIDGET-TAGSTRING-VALUE", 
        $wpsm->get(TubePressWidgetOptions::TAGSTRING));
    echo $tpl->get();
    
    restore_exception_handler();
}

function tubepress_widget_inject_deps(org_tubepress_gallery_AbstractGallery $gallery, 
    TubePressOptionsManager $tpom)
{
	$cacheService          = new org_tubepress_cache_SimpleCacheService();
	$embedService          = new SimpleTubePressEmbeddedPlayerService();
	$feedInsepctionService = new SimpleTubePressFeedInspectionService();
	$feedRetrievalService  = new TubePressFeedRetrievalService_HTTP_Request2();
	$messageService        = new org_tubepress_message_WordPressMessageService();
	$paginationService     = new TubePressPaginationService_DiggStyle();
	$playerFactory         = new SimpleTubePressPlayerFactory();
	$queryStringService    = new SimpleTubePressQueryStringService();
	$thumbService          = new SimpleTubePressThumbnailService();
    $urlBuilderService     = new SimpleTubePressUrlBuilder();
    $videoFactory          = new SimpleTubePressVideoFactory();
    
	$thumbService->setOptionsManager($tpom);
    $thumbService->setMessageService($messageService);
    $urlBuilderService->setOptionsManager($tpom);
    $feedRetrievalService->setCacheService($cacheService);
    $paginationService->setMessageService($messageService);
    $paginationService->setOptionsManager($tpom);
    $paginationService->setQueryStringService($queryStringService);
	$gallery->setFeedInspectionService($feedInsepctionService);
	$gallery->setFeedRetrievalService($feedRetrievalService );
	$gallery->setOptionsManager($tpom);
	$gallery->setQueryStringService($queryStringService);
	$gallery->setEmbeddedPlayerService($embedService);
	$gallery->setPaginationService($paginationService);
	$gallery->setPlayerFactory($playerFactory);
	$gallery->setThumbnailService($thumbService);
	$gallery->setUrlBuilderService($urlBuilderService);
	$gallery->setVideoFactory($videoFactory);
}

?>