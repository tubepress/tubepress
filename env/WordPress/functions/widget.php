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

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../classes/tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_message_WordPressMessageService',
    'org_tubepress_ioc_DefaultIocService',
    'org_tubepress_options_category_Display',
    'org_tubepress_options_category_Meta',
    'org_tubepress_options_category_Widget',
    'net_php_pear_HTML_Template_IT'));

/**
 * Registers TubePress as a widget
 *
 * @return void
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

/**
 * Executes the main widget functionality
 *
 * @param unknown_type $opts
 */
function tubepress_widget($opts)
{
	extract($opts);
	
	$iocContainer = new org_tubepress_ioc_DefaultIocService();
	$tpom         = $iocContainer->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
	
	/* Turn on logging if we need to */
    $log = $iocContainer->get(org_tubepress_ioc_IocService::LOG);
    $log->setEnabled($tpom->get(org_tubepress_options_category_Advanced::DEBUG_ON), $_GET);
	
	/* default widget options */
	$tpom->setCustomOptions(
	    array(org_tubepress_options_category_Display::RESULTS_PER_PAGE  => 3,
	        org_tubepress_options_category_Meta::VIEWS                  => false,
	        org_tubepress_options_category_Meta::DESCRIPTION            => true,
	        org_tubepress_options_category_Display::DESC_LIMIT          => 50,
	        org_tubepress_options_category_Display::CURRENT_PLAYER_NAME => org_tubepress_player_Player::POPUP,
	        org_tubepress_options_category_Display::THUMB_HEIGHT        => 105,
	        org_tubepress_options_category_Display::THUMB_WIDTH         => 135
	    )
	);
	
	/* now apply the user's shortcode */
	$shortcodeService = $iocContainer->get(org_tubepress_ioc_IocService::SHORTCODE_SERVICE);
	$wpsm             = $iocContainer->get(org_tubepress_ioc_IocService::STORAGE_MANAGER);
	$shortcodeService->parse($wpsm->get(org_tubepress_options_category_Widget::TAGSTRING), $tpom, true);

	/* grab a widget gallery to build */
	$gallery = $iocContainer->get(org_tubepress_ioc_IocService::WIDGET_GALLERY);
		
	/* get the output */
	$out = $gallery->getHtml(mt_rand());

	/* do the standard WordPress widget dance */
	echo $before_widget . $before_title . 
	    $wpsm->get(org_tubepress_options_category_Widget::TITLE) .
	    $after_title . $out . $after_widget;
}

/**
 * Handles the widget configuration panel
 *
 */
function tubepress_widget_control()
{
    $iocContainer = new org_tubepress_ioc_DefaultIocService();
    $wpsm         = $iocContainer->get(org_tubepress_ioc_IocService::STORAGE_MANAGER);
    $msg          = $iocContainer->get(org_tubepress_ioc_IocService::MESSAGE_SERVICE);
    
    /* are we saving? */
	if ( $_POST["tubepress-widget-submit"] ) {
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
    
    /* set up the template */
    $tpl->setVariable("WIDGET-TITLE", 
        $msg->_("options-meta-title-title"));
    $tpl->setVariable("WIDGET-TITLE-VALUE", 
        $wpsm->get(org_tubepress_options_category_Widget::TITLE));
    $tpl->setVariable("WIDGET-TAGSTRING", 
        $msg->_("widget-tagstring-description"));
    $tpl->setVariable("WIDGET-TAGSTRING-VALUE", 
        $wpsm->get(org_tubepress_options_category_Widget::TAGSTRING));
        
    /* get the template's output */
    echo $tpl->get();
}

?>
