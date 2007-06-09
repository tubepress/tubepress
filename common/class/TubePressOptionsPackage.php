<?php
/**
 * TubePressOptionsPackage.php
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

if (!class_exists('TubePressIntegerOption')) {
    require('options/TubePressIntegerOpt.php');
    require('options/TubePressStringOpt.php');
    require('options/TubePressEnumOpt.php');
    require('options/TubePressBooleanOpt.php');
}
function_exists("_tpMsg") || require(dirname(__FILE__) . "/../messages.php");
defined("TP_OPTION_NAME") || require(dirname(__FILE__) . "/../defines.php");

/**
 * This is meant to be an abstract class, though PHP 4 doesn't support
 * them :(. The idea here is that each implementation (WordPress, MoveableType)
 * extends this class and passes it around as the class that holds all 
 * of the users options. It's essentially just an array of TubePressOptions 
 * with some extra methods related to metadata on those options.
*/
class TubePressOptionsPackage
{
    /* this is our array of TubePressOptions */
    var $_allOptions;

    /**
     * Don't let anyone instantiate this directly.
     */
    function TubePressOptionsPackage()
    {
        $this->_allOptions = TubePressOptionsPackage::getDefaultPackage();
    }
    
    /**
     * Checks to see if parameter appears to be a correct set of options
     * 
     * @param An array of the options that the user currently has
     * (typically pulled from the db)
     */
    function checkValidity()
    {
        /* make sure the db looks ok */
        if ($this->_allOptions == NULL) {
            return PEAR::raiseError(_tpMsg("NODB"));
        }
        if (!is_array($this->_allOptions)) {
            return PEAR::raiseError(_tpMsg("BADDB",
            array(gettype($this->_allOptions))));
        }
        
        $modelOptions = array_keys(TubePressOptionsPackage::getDefaultPackage());
        
        foreach ($modelOptions as $defaultOption) {
            /* Make sure we have all the keys */
            if (!array_key_exists($defaultOption, $this->_allOptions)) {
                return PEAR::raiseError(_tpMsg("DBMISS", 
                    array($defaultOption, 
                        count($this->_allOptions), count($modelOptions))));
            }

            /* Make sure each entry is a valid TubePressOption */
            if ((!is_a($this->_allOptions[$defaultOption], TubePressBooleanOpt))
                && (!is_a($this->_allOptions[$defaultOption], TubePressEnumOpt))
                && (!is_a($this->_allOptions[$defaultOption], TubePressIntegerOpt))
                && (!is_a($this->_allOptions[$defaultOption], TubePressStringOpt))) {
                return PEAR::raiseError(_tpMsg("OLDDB"));
            }
        }
        
        /* finally, make sure that we have the right number of options */
        if (count($this->_allOptions) != count($modelOptions)) {
            return PEAR::raiseError("You have extra options! Expecting " . 
                count($modelOptions)
                . " but you seem to have " . count($this->_allOptions));
        }
    }
    
    /**
     * Used during debugging. Really meant to be overriden by
     * subclasses
     */
    function debug()
    {
        return "";
    }
    
    /**
     * Returns a fresh array of TubePress options.
     * The structure of this array defines what is stored in our db row.
     */
    function getDefaultPackage()
    {
        return
        
        /* -------- META OPTIONS ------------------------------------------- */
        
            array(TP_VID_TITLE => new TubePressBooleanOpt(_tpMsg("VIDTITLE"),' ', true),
                  TP_VID_LENGTH => new TubePressBooleanOpt(_tpMsg("VIDLEN"),' ', true),
                  TP_VID_VIEW => new TubePressBooleanOpt(_tpMsg("VIDVIEWS"),' ', true),
                  TP_VID_AUTHOR => new TubePressBooleanOpt(_tpMsg("VIDAUTHOR"),' ', false),
                  TP_VID_ID => new TubePressBooleanOpt(_tpMsg("VIDID"),' ', false),
                  TP_VID_RATING_AVG => new TubePressBooleanOpt(_tpMsg("VIDRATING"),' ', false),
                  TP_VID_RATING_CNT => new TubePressBooleanOpt(_tpMsg("VIDRATINGS"),' ', false),
                  TP_VID_UPLOAD_TIME => new TubePressBooleanOpt(_tpMsg("VIDUPLOAD"), ' ', false),
                  TP_VID_COMMENT_CNT => new TubePressBooleanOpt(_tpMsg("VIDCOMMENTS"), ' ', false),
                  TP_VID_TAGS => new TubePressBooleanOpt(_tpMsg("VIDTAGS"), ' ', false),
                  TP_VID_URL => new TubePressBooleanOpt(_tpMsg("VIDURL"), ' ', false),
                  TP_VID_THUMBURL => new TubePressBooleanOpt(_tpMsg("VIDTHUMBURL"), ' ', false),
                  TP_VID_DESC => new TubePressBooleanOpt(_tpMsg("VIDDESC"), ' ', false),

        /* -------- VIDEO SEARCH VALUES ------------------------------------ */
        
                  TP_OPT_TAGVAL =>  new TubePressStringOpt(' ',' ', "stewart daily show"),
                  TP_OPT_USERVAL => new TubePressStringOpt(' ',' ', "3hough"),
                  TP_OPT_PLSTVAL => new TubePressStringOpt(' ',' ', "D2B04665B213AE35"),
                  TP_OPT_FAVVAL =>  new TubePressStringOpt(' ',' ', "mrdeathgod"),
                  TP_OPT_POPVAL =>  new TubePressStringOpt(' ', '', "day"),
            
           /* -------- DISPLAY OPTIONS -------------------------------------- */
                  
                  TP_OPT_VIDSPERPAGE=>  new TubePressIntegerOpt(
                      _tpMsg("VIDSPERPAGE_TITLE"), _tpMsg("VIDSPERPAGE_DESC"), 20, 100),      
                  TP_OPT_VIDWIDTH =>    new TubePressIntegerOpt(
                      _tpMsg("VIDWIDTH_TITLE"), _tpMsg("VIDWIDTH_DESC"), 424, 424),
                  TP_OPT_VIDHEIGHT =>   new TubePressIntegerOpt(
                      _tpMsg("VIDHEIGHT_TITLE"), _tpMsg("VIDHEIGHT_DESC"), 336, 336),
                  TP_OPT_THUMBWIDTH =>  new TubePressIntegerOpt(
                      _tpMsg("THUMBWIDTH_TITLE"), _tpMsg("THUMBWIDTH_DESC"), 120, 120),
                  TP_OPT_THUMBHEIGHT => new TubePressIntegerOpt(
                      _tpMsg("THUMBHEIGHT_TITLE"), _tpMsg("THUMBHEIGHT_DESC"), 90, 90),
                  TP_OPT_GREYBOXON => new TubePressBooleanOpt(
                      _tpMsg("TP_OPT_GREYBOXON_TITLE"), ' ', false),
                  TP_OPT_LWON => new TubePressBooleanOpt(
                      _tpMsg("TP_OPT_LWON_TITLE"), ' ', false),
                
                  
              /* -------- ADVANCED OPTIONS ------------------------------------- */                    
                  
                  TP_OPT_KEYWORD =>  new TubePressStringOpt(
                      _tpMsg("KEYWORD_TITLE"), _tpMsg("KEYWORD_DESC"), "tubepress"),
                                         
                  TP_OPT_TIMEOUT =>  new TubePressIntegerOpt(
                      _tpMsg("TIMEOUT_TITLE"), _tpMsg("TIMEOUT_DESC"), 6),
                                         
                  TP_OPT_DEVID =>    new TubePressStringOpt(
                      _tpMsg("DEVID_TITLE"), _tpMsg("DEVID_DESC"), "qh7CQ9xJIIc"),
                                         
                  TP_OPT_USERNAME => new TubePressStringOpt(
                      _tpMsg("USERNAME_TITLE"), _tpMsg("USERNAME_DESC"), "3hough"),
                                          
                  TP_OPT_DEBUG => new TubePressBooleanOpt(
                      _tpMsg("DEBUGTITLE"), ' ', true),
 
         /* -------- VIDEO SEARCH OPTION ----------------------------------- */

                  TP_OPT_MODE => new TubePressEnumOpt(_tpMsg("MODE_TITLE"),
                      ' ', TP_MODE_FEATURED, TubePressOptionsPackage::getModeNames()),

        /* -------- PLAYER LOCATION OPTION ----------------------------------- */
 
                  TP_OPT_PLAYIN => new TubePressEnumOpt( 
                      _tpMsg("PLAYIN_TITLE"), ' ', TP_PLAYIN_NORMAL,
                      TubePressOptionsPackage::getPlayerLocationNames()));                       
    }
    
    /**
     * A wrapper for TubePressOption's getDescription()
     */
    function getDescription($optionName)
    {
        $result = $this->_get($optionName);
        if (PEAR::isError($result)) {
            return $result;
        }
        return $result->getDescription();
   }
    
    /**
     * The valid ways to play each video (new window, popup, lightWindow, etc)
     */
    function getPlayerLocationNames()
    {
        return
            array(TP_PLAYIN_NORMAL, TP_PLAYIN_NW, TP_PLAYIN_YT, 
                TP_PLAYIN_POPUP,TP_PLAYIN_LWINDOW,TP_PLAYIN_GREYBOX);
    }
    
    /**
     * The allowed mode names (each represents an API call to YouTube)
     */
    function getModeNames()
    {
        return
            array(TP_MODE_USER, TP_MODE_FAV, TP_MODE_PLST,TP_MODE_TAG, 
                 TP_MODE_FEATURED, TP_MODE_POPULAR, TP_MODE_REL);
    }
        
    
    /**
     * A wrapper for TubePressOption's getTitle()
     */
    function getTitle($optionName)
    {
        $result = $this->_get($optionName);
        if (PEAR::isError($result)) {
            return $result;
        }
        return $result->getTitle();
    }
    
    /**
     * A wrapper for TubePressOption's getValue()
     */
    function getValue($optionName)
    {
        $result = $this->_get($optionName);
        if (PEAR::isError($result)) {
            return $result;
        }
        return $result->_value;
    }
    
    /**
     * Set a single option's value for this package. Returns error if
     * option does not exist, or invalid option value.
     */
    function setValue($optionName, $optionValue)
    {
        if (!array_key_exists($optionName, $this->_allOptions)) {
            return PEAR::raiseError(_tpMsg("NOSUCHOPT", array($optionName)));
        }
        
        $result = $this->_allOptions[$optionName]->setValue($optionValue);
        if (PEAR::isError($result)) {
            return $result;
        }
    }
    
    /**
     * Tries to get a single option from this package. Returns
     * error if the option is not part of the package.
     */
    function _get($optionName)
    {
        if ((!array_key_exists($optionName, $this->_allOptions))
            || (!is_a($this->_allOptions[$optionName], "TubePressOption"))) {
            return PEAR::raiseError(_tpMsg("NOSUCHOPT", array($optionName)));
        }
        return $this->_allOptions[$optionName];
    }
}
?>
