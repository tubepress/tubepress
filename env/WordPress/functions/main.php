<?php
/**
 * Main filter hook. Looks for a tubepress tag
 * and, if found, replaces it with a gallery
*/
function tp_main($content = '')
{
	$wpsm = new WordPressStorageManager();
    $trigger = $wpsm->get(TubePressAdvancedOptions::KEYWORD);
    if (!TubePressTag::somethingToParse($content, $trigger)) {
	    return $content;
	}
	
	/* Store everything we generate in the following string */
    $newcontent = $content;
    
    while (TubePressTag::somethingToParse($newcontent, $trigger)) {
	    try {

	        $tpom = new TubePressOptionsManager($wpsm);
	        TubePressTag::parse($newcontent, $tpom);
	        
	        if (TubePressDebug::areWeDebugging($tpom)) {
	            TubePressDebug::execute($tpom, $wpsm);
	        }
	        
	        $modeName = $tpom->get(TubePressGalleryOptions::MODE);
	        $gallery = new TubePressGallery();
	
	        /* replace the tag with our new content */
	        $newcontent = TubePressTag::str_replaceFirst($tpom->getTagString(), $gallery->generate($tpom), $newcontent);
	    
	    } catch (Exception $e) {
	        return $e->getMessage();
	    }
    }
    
    return $newcontent;
}

/**
 * Spits out the CSS and JS files that we always need for TubePress
 */
function tp_insertCSSJS()
{
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
    
    try {
        $playerName = $wpsm->get(TubePressDisplayOptions::CURRENT_PLAYER_NAME);
        $player = TubePressPlayer::getInstance($playerName);
        print $player->getHeadContents();
    } catch (Exception $e) {
        /* this is in the head, so just print an HTML comment and proceed */
        print "<!-- " . $e->getMessage() . " -->";
    }
}

?>