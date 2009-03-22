<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org)
 * 
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_shortcode_ShortcodeService',
    'org_tubepress_options_category_Advanced',
    'org_tubepress_options_manager_OptionsManager'));

/**
 * Handles some tasks related to the query string
 */
class org_tubepress_shortcode_SimpleShortcodeService implements org_tubepress_shortcode_ShortcodeService
{
   /**
     * This function is used to parse a shortcode into options that TubePress can use.
     *
     * @param string                  $content The haystack in which to search
     * @param org_tubepress_options_manager_OptionsManager &$tpom   The TubePress options manager
     * 
     * @return void
     */
    public function parse($content, org_tubepress_options_manager_OptionsManager $tpom)
    {    
        /* what trigger word are we using? */
        $keyword = $tpom->get(org_tubepress_options_category_Advanced::KEYWORD);
        
        if (!$this->somethingToParse($content, $keyword)) {
            return;
        }
        
        $customOptions = array();  
          
        /* Match everything in square brackets after the trigger */
        $regexp = "\[$keyword(.*)\]";
        preg_match("/$regexp/", $content, $matches);
        
        $tpom->setShortcode($matches[0]);
        
        /* Anything matched? */
        if (!isset($matches[1]) || $matches[1] == "") {
            return;
        }
        
        /* Break up the options by comma */
        $pairs = explode(",", $matches[1]);
        
        $optionsArray = array();
        foreach ($pairs as $pair) {
            $pieces                    = explode("=", $pair);
            $pieces[0]                 = org_tubepress_shortcode_SimpleShortcodeService::_cleanupTagValue($pieces[0]);
            $pieces[1]                 = org_tubepress_shortcode_SimpleShortcodeService::_cleanupTagValue($pieces[1]);
            $customOptions[$pieces[0]] = $pieces[1];
        }

        $tpom->setCustomOptions($customOptions);
    }

    public function somethingToParse($content, $trigger = "tubepress")
    {
        return strpos($content, '[' . $trigger) !== false;
    }
    
    /**
     * Tries to strip out any quotes from a tag option name or option value. This
     * is ugly, ugly, ugly, and it still doesn't work as well as I'd like it to
     *
     * @param string &$nameOrValue The raw option name or value
     * 
     * @return string The cleaned up option name or value
     */
    private static function _cleanupTagValue(&$nameOrValue)
    {
        $nameOrValue = 
            trim(str_replace(array("&#8220;", 
                "&#8221;", "&#8217;", "&#8216;",
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
?>
