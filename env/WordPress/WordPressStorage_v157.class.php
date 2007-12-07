<?php
/**
 * WordPressOptionsPackage.php
 * 
 * Implements a TubePressOptions package for WordPress. Can parse a tag from 
 * a post/page and can talk to the WP database. Awesome.
 * 
 * Copyright (C) 2007 Eric D. Hough (http://ehough.com)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


class WordPressStorage_v157 extends TubePressStorage_v157
{
    /* the tag the user used in the page */
    private $tagString;
    
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
    
    /* let's us get the tag */
    public function getTagString() {
        return $this->tagString;
    }
    
    /**
     * Will initialize our database entry for WordPress
     */
    public static function initDB()
    {
        WordPressStorage_v157::deleteLegacyOptions();
        
        $storage = get_option("tubepress");
        
        if (!($storage instanceof WordPressStorage_v157)) {
            delete_option("tubepress");
            add_option("tubepress", new WordPressStorage_v157());
        }
    }
    
    /**
     * This function is used when the plugin parses a tag from a post/page.
     * It pulls all the options from the db, but uses option values found in
     * the tag when it can.
     */
    public function parse($content)
    {
        
        /* what trigger word are we using? */
        $keyword = $this->getCurrentValue(TubePressAdvancedOptions::triggerWord);
        
        $customOptions = array();  
          
        /* Match everything in square brackets after the trigger */
        $regexp = '\[' . $keyword . "(.*)\]";
        preg_match("/$regexp/", $content, $matches);

        /* we'll need the full tag string so we can replace it later */
        $this->tagString = $matches[0]; 
        
        /* Anything matched? */
        if (!isset($matches[1])) {
            return;
        }
        
        /* Break up the options by comma */
        $pairs = explode(",", $matches[1]);
        
        $optionsArray = array();
        foreach ($pairs as $pair) {
                
            $pieces = explode("=", $pair);
            $pieces[0] =WordPressStorage_v157::cleanupTagValue($pieces[0]);
            $pieces[1] =WordPressStorage_v157::cleanupTagValue($pieces[1]);
            $customOptions[$pieces[0]] = $pieces[1];
        }
        
        /* for each custom option, try to find it and set it */
        foreach (array_keys($customOptions) as $customOption) {
            
            /* we have to look through every option package */
            foreach ($this->getOptionPackages() as $optionPackage) {
                
                if (!array_key_exists($customOption, $optionPackage->getOptions())) {
                    continue;
                }
                
                /* lots of returning by reference :( */
                $opt = &$optionPackage->get($customOption);
                $val = &$opt->getValue();
                $val->updateManually($customOptions[$customOption]);
            }     
        }
    }
    
    /**
     * Tries to strip out any quotes from a tag option name or option value. This
     * is ugly, ugly, ugly, and it still doesn't work as well as I'd like it to
     */
    private static function cleanupTagValue(&$nameOrValue)
    {
        $nameOrValue = trim(
            str_replace(
                array("&#8220;", "&#8221;", "&#8217;", "&#8216;",
                      "&#8242;", "&#8243;", "&#34"),"", 
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
?>
