<?php

function tubepress_init_widget()
{
	$widget_ops = array('classname' => 'widget_tubepress', 
	    'description' => TpMsg::_("widget-description"));
	wp_register_sidebar_widget('tubepress', "TubePress", 
	    'tubepress_widget', $widget_ops);
}

function tubepress_widget($opts)
{
	extract($opts);
	$tpom = new TubePressOptionsManager(new WordPressStorageManager());
	TubePressTag::parse("[tubepress resultsPerPage='3', views='false', description='true', descriptionLimit='50', thumbHeight='105', thumbWidth='135']", $tpom);
	$gallery = new TubePressWidgetGallery();
	$out = $gallery->generate($tpom);
	
	echo $before_widget . $before_title . "TubePress" . 
	    $after_title . $out . $after_widget;
}

?>