<?php

/**
 * Registers TubePress as a widget
 *
 */
function tubepress_init_widget()
{
	$widget_ops = array('classname' => 'widget_tubepress', 
	    'description' => TpMsg::_("widget-description"));
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
	$wpsm = new WordPressStorageManager();
	$tpom = new TubePressOptionsManager($wpsm);
	$tpom->setCustomOptions(
	    array(TubePressDisplayOptions::RESULTS_PER_PAGE => 3,
	        TubePressMetaOptions::VIEWS => false,
	        TubePressMetaOptions::DESCRIPTION => true,
	        TubePressDisplayOptions::DESC_LIMIT => 50
	        ));
	TubePressTag::parse($wpsm->get(TubePressWidgetOptions::TAGSTRING), $tpom);
	$gallery = new TubePressWidgetGallery();
	$out = $gallery->generate($tpom);
	
	echo $before_widget . $before_title . 
	    $wpsm->get(TubePressWidgetOptions::TITLE) .
	    $after_title . $out . $after_widget;
}

/**
 * Handles the widget configuration panel
 *
 */
function tubepress_widget_control() {
	
	if ( $_POST["tubepress-widget-submit"] ) {
		$wpsm = new WordPressStorageManager();
		$wpsm->set(TubePressWidgetOptions::TAGSTRING, 
		    strip_tags(stripslashes($_POST["tubepress-widget-tagstring"])));
		$wpsm->set(TubePressWidgetOptions::TITLE, 
		    strip_tags(stripslashes($_POST["tubepress-widget-title"])));
	}

    /* load up the gallery template */
    $tpl = new HTML_Template_IT(dirname(__FILE__) . "/../../../common/ui");
    if (!$tpl->loadTemplatefile("widget_controls.tpl.html", true, true)) {
        throw new Exception("Couldn't load widget control template");
    }
    
    $wpsm = new WordPressStorageManager();
    
    $tpl->setVariable("WIDGET-TITLE", TpMsg::_("options-meta-title-title"));
    $tpl->setVariable("WIDGET-TITLE-VALUE", $wpsm->get(TubePressWidgetOptions::TITLE));
    $tpl->setVariable("WIDGET-TAGSTRING", TpMsg::_("widget-tagstring-description"));
    $tpl->setVariable("WIDGET-TAGSTRING-VALUE", $wpsm->get(TubePressWidgetOptions::TAGSTRING));
    echo $tpl->get();
}

?>