<?php
class TubePressTag {
    /**
     * This function is used when the plugin parses a tag from a post/page.
     * It pulls all the options from the db, but uses option values found in
     * the tag when it can.
     */
    public function parse($content, TubePressOptionsManager &$tpom)
    {    
        /* what trigger word are we using? */
        $keyword = $tpom->get(TubePressAdvancedOptions::KEYWORD);
        
        $customOptions = array();  
          
        /* Match everything in square brackets after the trigger */
        $regexp = '\[' . $keyword . "(.*)\]";
        preg_match("/$regexp/", $content, $matches);
        
//        if (TubePressStatic::areWeDebugging($this)) {
//            echo "<ol><li>Tag string on this page is <code>" . $this->tagString . "</code></li></ol>";
//        }
        
        $tpom->setTagString($matches[0]);
        
        /* Anything matched? */
        if (!isset($matches[1]) || $matches[1] == "") {
            return;
        }
        
        /* Break up the options by comma */
        $pairs = explode(",", $matches[1]);
        
        $optionsArray = array();
        foreach ($pairs as $pair) {
                
            $pieces = explode("=", $pair);
            $pieces[0] = TubePressTag::_cleanupTagValue($pieces[0]);
            $pieces[1] = TubePressTag::_cleanupTagValue($pieces[1]);
            $customOptions[$pieces[0]] = $pieces[1];
        }

        $tpom->setCustomOptions($customOptions);
    }
    
    /**
     * Tries to strip out any quotes from a tag option name or option value. This
     * is ugly, ugly, ugly, and it still doesn't work as well as I'd like it to
     */
    private static function _cleanupTagValue(&$nameOrValue)
    {
        $nameOrValue = trim(
            str_replace(
                array("&#8220;", "&#8221;", "&#8217;", "&#8216;",
                      "&#8242;", "&#8243;", "&#34", "'", "\""),"", 
                      trim($nameOrValue)));
        if ($nameOrValue == "true") {
            return true;
        }
        if ($nameOrValue == "false") {
            return false;
        }
        return $nameOrValue;
    }
}
