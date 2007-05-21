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

class_exists('TubePressOptionsPackage')
    || require(dirname(__FILE__) .
        "/../../common/class/TubePressOptionsPackage.php");
class_exists("PEAR")
    || require(ABSPATH .
        "wp-content/plugins/tubepress/lib/PEAR/PEAR.php");

class WordPressOptionsPackage extends TubePressOptionsPackage
{
    
    /* need to keep the tag string around for string replacement later */
    var $tagString;
     
    /**
     * Default constructor. Just pulls the options from the db. Will return
     * error if the options appear to be corrupt.
     */
    function WordPressOptionsPackage()
    {
        /* In the db we now store all the options in a single, flat array */
        $options = get_option(TP_OPTION_NAME);
        
        $result = TubePressOptionsPackage::areValid($options);
        if (PEAR::isError($result)) {
            $this->error = $result;
            return;
        }
      
        foreach ($options as $option) {
            $this->_allOptions[$option->getName()] = $option;
        }
    } 
     
    /**
     * This function is used when the plugin parses a tag from a post/page.
     * It pulls all the options from the db, but uses option values found in the
     * tag when it can.
     */
    function parse($keyword, $content)
    {
        
        $customOptions = array();  
          
        /* Use a regular expression to match everything in square brackets 
         * after the TubePress keyword */
        $regexp = '\[' . $keyword . "(.*)\]";
        preg_match("/$regexp/", $content, $matches);

        /* Anything was matched by the parentheses? */
        if (isset($matches[1])) {
        
            /* Break up the options by comma and store them in an 
             * associative array */
            $pairs = explode(",", $matches[1]);
        
            $optionsArray = array();
            foreach ($pairs as $pair) {
                $pieces = explode("=", $pair);
                $customOptions[WordPressOptionsPackage::cleanupTagValue($pieces[0])] = 
                    WordPressOptionsPackage::cleanupTagValue($pieces[1]);
            }
        }

        /* options in the tag overwrite any options from the db */
        $dbOptions = new WordPressOptionsPackage();
        
        if (PEAR::isError($dbOptions->error)) {
            return $dbOptions->error;
        }
        
        /* we'll need the full tag string so we can replace it later */
        $dbOptions->tagString = $matches[0];

        foreach ($dbOptions->_allOptions as $dbOption) {
            
            
            
            /* if we have this option in the tag, let's use that instead */        
            if (array_key_exists($dbOption->getName(), $customOptions)) {                
                $result = $dbOptions->setValue($dbOption->getName(), $customOptions[$dbOption->getName()]);
                
                if (PEAR::isError($result)) {
                    return $result;
                }
            }
        }
        
        /* one last error check */
        $result = TubePressOptionsPackage::areValid($dbOptions->_allOptions);
        if (PEAR::isError($result)) {
            return $result;
        }
        
        return $dbOptions;
    }

    /**
     * Will initialize our database entry for WordPress
     */
    function initDB()
    {
        WordPressOptionsPackage::deleteLegacyOptions();
        
        add_option(TP_OPTION_NAME, TubePressOptionsPackage::getDefaultPackage());
    }

    /**
     * Tries to strip out any quotes from a tag option name or option value. This
     * is ugly, ugly, ugly, and it still doesn't work as well as I'd like it to
     */
    function cleanupTagValue($nameOrValue)
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
        return $returnVal;
    }
    
    /**
     *  Gets rid of legacy options if they still exist.
     *  Please email me if you think I missed one!
     */
    function deleteLegacyOptions()
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
        delete_option("TP_OPT_SEARCHBY_TAGVAL");
        delete_option("TP_OPT_SEARCHBY_USERVAL");
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
        delete_option("tubepress");
    }
}
?>
