<?php
/**
 * Main filter hook. Looks for a tubepress tag
 * and, if found, replaces it with a gallery
*/
function tp_main($content = '')
{
    /* Store everything we generate in the following string */
    $newcontent = "";

    try {

        if (!tp_shouldWeExecute($content)) {
            return $content;
        }
        
        $stored = get_option("tubepress");
        $stored->parse($content);
        
        if (TubePressStatic::areWeDebugging($stored)) {
        	TubePressStatic::debugEnvironment($stored);
        }
        
        $modeName = $stored->getCurrentValue(TubePressGalleryOptions::mode);
        $gallery = $stored->getGalleryOptions()->getGallery($modeName);
        $newcontent .= $gallery->generate($stored);

    	/* replace the tag with our new content */
        return str_replace($stored->getTagString(), $newcontent, $content);
    
    } catch (Exception $e) {
        return $e->getMessage();
    }
}


/**
 * Spits out the CSS and JS files that we always need for TubePress
 */
function tp_insertCSSJS()
{
    
    global $tubepress_base_url;
    $url = $tubepress_base_url . "/common";
    print<<<GBS
        <script type="text/javascript" src="$url/js/tubepress.js"></script>
        <link rel="stylesheet" href="$url/css/tubepress.css" 
            type="text/css" />
        <link rel="stylesheet" href="$url/css/pagination.css" 
            type="text/css" />
GBS;
    
    $stored = get_option("tubepress");
   
    /* we're in the head here, so just return quietly */
    if ($stored == NULL || !($stored instanceof TubePressStorage_v160)) {
        return;
    }
    
    try {
        $playerName = $stored->getCurrentValue(TubePressDisplayOptions::CURRENT_PLAYER_NAME);
        $player = TubePressPlayer::getInstance($playerName);
        print $player->getHeadContents();
    } catch (Exception $e) {
        /* this is in the head, so just print an HTML comment and proceed */
        print "<!-- " . $e->getMessage() . " -->";
    }
}

function tp_shouldWeExecute($content) {
    
    $stored = get_option("tubepress");
    
    if ($stored == NULL) {
        return false;
    }
    
    if (!($stored instanceof TubePressStorage_v160)) {
        WordPressStorage_v160::initDB();
        $stored = get_option("tubepress");
    }
    
    $trigger = $stored->getCurrentValue(TubePressAdvancedOptions::triggerWord);
    
    if (strpos($content, '[' . $trigger) === false) {
        return false;
    }
    
    return true;
}

/* don't forget to add our hooks! */
add_filter('the_content', 'tp_main');
add_action('wp_head', 'tp_insertCSSJS');
?>