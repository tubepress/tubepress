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
	$wpsm = new org_tubepress_options_storage_WordPressStorageManager();
	$tpom = new org_tubepress_options_manager_SimpleOptionsManager();
	$ref = new org_tubepress_options_reference_SimpleOptionsReference();
	$ms = new org_tubepress_message_WordPressMessageService();
	$val = new org_tubepress_options_validation_SimpleInputValidationService();
	$val->setMessageService($ms);
	$tpom->setStorageManager($wpsm);
	$tpom->setValidationService($val);
	$tpom->setOptionsReference($ref);
	$tpom->setCustomOptions(
	    array(org_tubepress_options_category_Display::RESULTS_PER_PAGE => 3,
	        org_tubepress_options_category_Meta::VIEWS => false,
	        org_tubepress_options_category_Meta::DESCRIPTION => true,
	        org_tubepress_options_category_Display::DESC_LIMIT => 50,
	        org_tubepress_options_category_Display::CURRENT_PLAYER_NAME => org_tubepress_player_Player::POPUP,
	        org_tubepress_options_category_Display::THUMB_HEIGHT => 105,
	        org_tubepress_options_category_Display::THUMB_WIDTH => 135
	        )
	);
	
	/* now apply the user's shortcode */
	$shortcodeService = new org_tubepress_shortcode_SimpleShortcodeService();
	$shortcodeService->parse($wpsm->get(org_tubepress_options_category_Widget::TAGSTRING), $tpom);
	
	$gallery = new org_tubepress_gallery_WidgetGallery();
	tubepress_widget_inject_deps($gallery, $tpom);
		
	/* get the output */
	$out = $gallery->generate($tpom);

	/* do the standard WordPress widget dance */
	echo $before_widget . $before_title . 
	    $wpsm->get(org_tubepress_options_category_Widget::TITLE) .
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
		$wpsm = new org_tubepress_options_storage_WordPressStorageManager();
		$wpsm->setValidationService(new org_tubepress_options_validation_SimpleInputValidationService());
		$wpsm->set(org_tubepress_options_category_Widget::TAGSTRING, 
		    strip_tags(stripslashes($_POST["tubepress-widget-tagstring"])));
		$wpsm->set(org_tubepress_options_category_Widget::TITLE, 
		    strip_tags(stripslashes($_POST["tubepress-widget-title"])));
	}

    /* load up the gallery template */
    $tpl = new net_php_pear_HTML_Template_IT(dirname(__FILE__) . "/../../../ui/widget/html_templates");
    if (!$tpl->loadTemplatefile("controls.tpl.html", true, true)) {
        throw new Exception("Couldn't load widget control template");
    }
    
    $wpsm = new org_tubepress_options_storage_WordPressStorageManager();

    $msg = new org_tubepress_message_WordPressMessageService();
    
    $tpl->setVariable("WIDGET-TITLE", 
        $msg->_("options-meta-title-title"));
    $tpl->setVariable("WIDGET-TITLE-VALUE", 
        $wpsm->get(org_tubepress_options_category_Widget::TITLE));
    $tpl->setVariable("WIDGET-TAGSTRING", 
        $msg->_("widget-tagstring-description"));
    $tpl->setVariable("WIDGET-TAGSTRING-VALUE", 
        $wpsm->get(org_tubepress_options_category_Widget::TAGSTRING));
    echo $tpl->get();
    
    restore_exception_handler();
}

function tubepress_widget_inject_deps(org_tubepress_gallery_AbstractGallery $gallery, 
    org_tubepress_options_manager_OptionsManager $tpom)
{
	$cacheService          = new org_tubepress_cache_SimpleCacheService();
	$embedService          = new org_tubepress_video_embed_SimpleEmbeddedPlayerService();
	$feedInsepctionService = new org_tubepress_gdata_inspection_SimpleFeedInspectionService();
	$feedRetrievalService  = new org_tubepress_gdata_retrieval_HTTPRequest2();
	$messageService        = new org_tubepress_message_WordPressMessageService();
	$paginationService     = new org_tubepress_pagination_DiggStylePaginationService();
	$playerFactory         = new org_tubepress_player_factory_SimplePlayerFactory();
	$queryStringService    = new org_tubepress_querystring_SimpleQueryStringService();
	$thumbService          = new org_tubepress_thumbnail_SimpleThumbnailService();
    $urlBuilderService     = new org_tubepress_url_SimpleUrlBuilder();
    $videoFactory          = new org_tubepress_video_factory_SimpleVideoFactory();
    
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