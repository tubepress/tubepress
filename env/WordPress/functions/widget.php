<?php

/**
 * Registers TubePress as a widget
 *
 */
function tubepress_init_widget()
{
	$msg = new WordPressMessageService();
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
	        ));
	$shortcodeService = new SimpleTubePressShortcodeService();
	$shortcodeService->parse($wpsm->get(TubePressWidgetOptions::TAGSTRING), $tpom);
	
	$gallery = new TubePressWidgetGallery();
	_tp_widget_setGalleryInterfaces($gallery, $tpom);
		
	$out = $gallery->generate($tpom);
		
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

    $msg = new WordPressMessageService();
    
    $tpl->setVariable("WIDGET-TITLE", $msg->_("options-meta-title-title"));
    $tpl->setVariable("WIDGET-TITLE-VALUE", $wpsm->get(TubePressWidgetOptions::TITLE));
    $tpl->setVariable("WIDGET-TAGSTRING", $msg->_("widget-tagstring-description"));
    $tpl->setVariable("WIDGET-TAGSTRING-VALUE", $wpsm->get(TubePressWidgetOptions::TAGSTRING));
    echo $tpl->get();
    
    restore_exception_handler();
}

function _tp_widget_setGalleryInterfaces(AbstractTubePressGallery $gallery, TubePressOptionsManager $tpom)
{
	$messageService = new WordPressMessageService();
	
	$thumbService = new SimpleTubePressThumbnailService();
    $thumbService->setOptionsManager($tpom);
    $thumbService->setMessageService($messageService);
    	
    $queryStringService = new SimpleTubePressQueryStringService();
    	
    $urlBuilderService = new SimpleTubePressUrlBuilder();
    $urlBuilderService->setOptionsManager($tpom);
    	
    $paginationService = new TubePressPaginationService_DiggStyle();
    $paginationService->setMessageService($messageService);
    $paginationService->setOptionsManager($tpom);
    $paginationService->setQueryStringService($queryStringService);
    	
    
	$gallery->setFeedInspectionService( new SimpleTubePressFeedInspectionService());
	$feedRetrievalService = new TubePressFeedRetrievalService_HTTP_Request2();
	$feedRetrievalService->setCacheService(			 new SimpleTubePressCacheService());
	$gallery->setFeedRetrievalService(	$feedRetrievalService );
	$gallery->setOptionsManager(		 $tpom);
	$gallery->setQueryStringService($queryStringService);
	$gallery->setPaginationService(	 $paginationService);
	$gallery->setThumbnailService(		 $thumbService);
	$gallery->setUrlBuilderService(	 $urlBuilderService);
	$gallery->setVideoFactory(			 new SimpleTubePressVideoFactory());
}

?>