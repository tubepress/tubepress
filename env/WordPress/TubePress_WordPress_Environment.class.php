<?php
final class TubePress_WordPress_Environment
{
    /* need to keep the tag string around for string replacement later */
    private $tagString;
    
    /**
     * Private constructor
     */
    private function __construct() {
        /* don't let anyone instantiate me */
    }
    
    /**
     * Tries to strip out any quotes from a tag option name or option value. This
     * is ugly, ugly, ugly, and it still doesn't work as well as I'd like it to
     */
    public static function cleanupTagValue(&$nameOrValue)
    {
        $returnVal = trim(
            str_replace(
                array("&#8220;", "&#8221;", "&#8217;", "&#8216;",
                      "&#8242;", "&#8243;", "&#34"),"", 
                      trim($nameOrValue)));
        if ($returnVal == "true") {
            return true;
        }
        if ($returnVal == "false") {
            return false;
        }
        $nameOrValue = $returnVal;
    }
    
    /**
     *  Gets rid of legacy options if they still exist.
     *  Please email me if you think I missed one!
     */
    public static function deleteLegacyOptions()
    {
        delete_option(TP_OPTS_ADV);
        delete_option(TP_OPTS_DISP);
        delete_option(TP_OPTS_META);
        delete_option(TP_OPTS_PLAYERLOCATION);
        delete_option(TP_OPTS_PLAYERMENU);
        delete_option(TP_OPTS_SEARCH);
        delete_option(TP_OPTS_SRCHV);
        delete_option("tubepress_accountInfo");
        delete_option("[tubepress]");
        delete_option("TP_OPT_MODE_TAGVAL");
        delete_option("TP_OPT_MODE_USERVAL");
        delete_option("TP_OPT_SEARCHKEY");
        delete_option("TP_OPT_THUMBHEIGHT");
        delete_option("tp_display_author");
        delete_option("tp_display_comment_count");
        delete_option("tp_display_description");
        delete_option("tp_display_id");
        delete_option("tp_display_length");
        delete_option("tp_display_rating_avg");
        delete_option("tp_display_rating_count");
        delete_option("tp_display_tags");
        delete_option("tp_display_title");
        delete_option("tp_display_upload_time");
        delete_option("tp_display_url");
        delete_option("tp_display_view_count");
        delete_option("mainVidHeight");
        delete_option("mainVidWidth");
        delete_option("searchBy");
        delete_option("searchByTagValue");
        delete_option("searchByUserValue");
        delete_option("thumbHeight");
        delete_option("thumbWidth");
        delete_option("timeout");
        delete_option("TP_OPT_THUMBEIGHT");
        delete_option("TP_VID_METAS");
        delete_option("username");
        delete_option("devID");
        delete_option("devIDlink");
        delete_option("searchByValue");
    }
    
    /**
     * Will initialize our database entry for WordPress
     */
    public static function initDB()
    {
        TubePress_WordPress_Environment::deleteLegacyOptions();
        $opts = get_option("tubepress");
        
        if ($opts == NULL
            || (!($opts instanceof TubePressStorage_v157))) {
            delete_option("tubepress");
            add_option("tubepress", 
                new TubePressStorage_v157());
        }
    }
    
    /**
     * This function is used when the plugin parses a tag from a post/page.
     * It pulls all the options from the db, but uses option values found in
     * the tag when it can.
     */
    public static function applyTag($keyword, $content, &$dbStored, &$dbOptions)
    {
        $customOptions = array();
        $matches = array();
          
        /* Use a regular expression to match everything in square brackets 
         * after the TubePress keyword */
        $regexp = '\[' . $keyword . "(.*)\]";
        preg_match("/$regexp/", $content, $matches);
        
        /* Anything was matched by the parentheses? */
        if ($matches[1] != "") {
        
            /* Break up the options by comma and store them in an 
             * associative array */
            $pairs = explode(",", $matches[1]);

            foreach ($pairs as $pair) {
                $pieces = explode("=", $pair);
                $customOptions[WordPressStorageBox::cleanupTagValue($pieces[0])] = 
                    WordPressStorageBox::cleanupTagValue($pieces[1]);
            }
        }

        /* we'll need the full tag string so we can replace it later */
        $dbStored->tagString = $matches[0];

        foreach (array_keys($customOptions) as $customOptionName) {
            if (!in_array($customOptionName, $dbOptions->getNames())) {

                if (strpos($customOptionName, "Value") === -1) {
                    continue;
                }
                
               $modeObj =& $dbStored->modes->get(str_replace("Value", "", $customOptionName));
               if (PEAR::isError($modeObj)) {
                   continue;
               }
               $modeObj->setValue($customOptions[$customOptionName]);
               continue;
            }
            $realOpt =& $dbOptions->get($customOptionName);
            $realOpt->setValue($customOptions[$customOptionName]);
        }
    }   
}
?>
