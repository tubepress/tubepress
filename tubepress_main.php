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
        
        $wpsm = new WordPressStorageManager();
        $tpom = new TubePressOptionsManager($wpsm);
        TubePressTag::parse($content, &$tpom);
        
        if (TubePressDebug::areWeDebugging($tpom)) {
            TubePressDebug::execute($tpom, $wpsm);
        }
        
        $modeName = $tpom->get(TubePressGalleryOptions::MODE);
        $gallery = new TubePressGallery();
        $newcontent .= $gallery->generate($tpom);

        /* replace the tag with our new content */
        return str_replace($tpom->getTagString(), $newcontent, $content);
    
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

function tp_shouldWeExecute($content)
{    
    $wpsm = new WordPressStorageManager();
    
    $trigger = $wpsm->get(TubePressAdvancedOptions::KEYWORD);
    
    if (strpos($content, '[' . $trigger) === false) {
        return false;
    }
    
    return true;
}

/* don't forget to add our hooks! */
add_filter('the_content', 'tp_main');
add_action('wp_head', 'tp_insertCSSJS');
?>