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

class_exists('TubePressOption') || require('TubePressOption.php');
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
    var $_allOptions;

    /**
     * Don't let anyone instantiate this directly.
     */
    function TubePressOptionsPackage()
    {
        //die("This is an abstract class");
    }

    /**
     * Tries to get a single option from this package. Returns
     * error if the option is not part of the package.
     */
    function _get($optionName)
    {
        if (!array_key_exists($optionName, $this->_allOptions)) {
            return PEAR::raiseError(_tpMsg("SETOPT", array($optionName)));
        }
        return $this->_allOptions[$optionName];
    }
    
    /**
     * FIXME
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
     * FIXME
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
     * FIXME
     */
    function getValue($optionName)
    {
        $result = $this->_get($optionName);
        if (PEAR::isError($result)) {
            return $result;
        }
        return $result->getValue();
    }
    
    /**
     * Set a single option's value for this package. Returns error if
     * option does not exist, or invalid option value.
     */
    function setValue($optionName, $optionValue)
    {
        if (!array_key_exists($optionName, $this->_allOptions)) {
            return PEAR::raiseError(_tpMsg("SETOPT", array($optionName)));
        }
        
        $result = $this->_allOptions[$optionName]->setValue($optionValue);
        if (PEAR::isError($result)) {
            return $result;
        }
    }

    /**
     * Returns a fresh array of TubePress options.
     * The structure of this array defines what is stored in our db row.
     */
    function getDefaultPackage()
    {
        $returnVal =
        
        /* -------- META OPTIONS ------------------------------------------- */
        
            array(TP_VID_TITLE =>       new TubePressOption(TP_VID_TITLE, 
                                            _tpMsg("VIDTITLE"), '',
                                            true, "boolean"),
                  TP_VID_LENGTH =>      new TubePressOption(TP_VID_LENGTH,
                                            _tpMsg("VIDLEN"),      '',
                                            true, "boolean"),
                  TP_VID_VIEW =>        new TubePressOption(TP_VID_VIEW,
                                            _tpMsg("VIDVIEWS"),    '',
                                            true, "boolean"),
                  TP_VID_AUTHOR =>      new TubePressOption(TP_VID_AUTHOR ,
                                            _tpMsg("VIDAUTHOR"),   '',
                                            false, "boolean"),
                  TP_VID_ID =>          new TubePressOption(TP_VID_ID,
                                            _tpMsg("VIDID"),       '',
                                            false, "boolean"),
                  TP_VID_RATING_AVG =>  new TubePressOption(TP_VID_RATING_AVG,
                                            _tpMsg("VIDRATING"),   '',
                                            false, "boolean"),
                  TP_VID_RATING_CNT =>  new TubePressOption(TP_VID_RATING_CNT,
                                            _tpMsg("VIDRATINGS"),  '',
                                            false, "boolean"),
                  TP_VID_UPLOAD_TIME => new TubePressOption(TP_VID_UPLOAD_TIME,
                                            _tpMsg("VIDUPLOAD"),   '',
                                            false, "boolean"),
                  TP_VID_COMMENT_CNT => new TubePressOption(TP_VID_COMMENT_CNT,
                                            _tpMsg("VIDCOMMENTS"), '',
                                            false, "boolean"),
                  TP_VID_TAGS =>        new TubePressOption(TP_VID_TAGS,
                                            _tpMsg("VIDTAGS"),     '',
                                            false, "boolean"),
                  TP_VID_URL =>         new TubePressOption(TP_VID_URL,
                                            _tpMsg("VIDURL"),      '',
                                            false, "boolean"),
                  TP_VID_THUMBURL =>    new tubePressOption(TP_VID_THUMBURL,
                                            _tpMsg("VIDTHUMBURL"), '',
                                            false, "boolean"),
                  TP_VID_DESC =>        new TubePressOption(TP_VID_DESC,
                                            _tpMsg("VIDDESC"),     '',
                                            false, "boolean"),

        /* -------- VIDEO SEARCH VALUES ------------------------------------ */
        
                  TP_OPT_TAGVAL =>  new TubePressOption(TP_OPT_TAGVAL, ' ',
                                         '', "stewart daily show"),
                  TP_OPT_USERVAL => new TubePressOption(TP_OPT_USERVAL, ' ',
                                     '', "3hough"),
                  TP_OPT_PLSTVAL => new TubePressOption(TP_OPT_PLSTVAL,
                                         ' ', '', "D2B04665B213AE35"),
                  TP_OPT_FAVVAL =>  new TubePressOption(TP_OPT_FAVVAL, ' ',
                                         '', "mrdeathgod"),
                  TP_OPT_POPVAL =>  new TubePressOption(TP_OPT_POPVAL,
                                         ' ', '', "day"),
                  //TP_OPT_CATVAL =>  new TubePressOption(TP_OPT_CATVAL, ' ',
                  //                     '', "19"),
            
           /* -------- DISPLAY OPTIONS -------------------------------------- */
                  
                  TP_OPT_VIDSPERPAGE=>  new TubePressOption(TP_OPT_VIDSPERPAGE,
                                            _tpMsg("VIDSPERPAGE_TITLE"),
                                            _tpMsg("VIDSPERPAGE_DESC"),
                                            "20", "integer"),      
                  TP_OPT_VIDWIDTH =>    new TubePressOption(TP_OPT_VIDWIDTH,
                                            _tpMsg("VIDWIDTH_TITLE"),
                                            _tpMsg("VIDWIDTH_DESC"),
                                            "425", "integer"),
                  TP_OPT_VIDHEIGHT =>   new TubePressOption(TP_OPT_VIDHEIGHT,
                                            _tpMsg("VIDHEIGHT_TITLE"),
                                            _tpMsg("VIDHEIGHT_DESC"),
                                            "350", "integer"),
                  TP_OPT_THUMBWIDTH =>  new TubePressOption(TP_OPT_THUMBWIDTH,
                                            _tpMsg("THUMBWIDTH_TITLE"),
                                            _tpMsg("THUMBWIDTH_DESC"),
                                            "120", "integer"),
                  TP_OPT_THUMBHEIGHT => new TubePressOption(TP_OPT_THUMBHEIGHT,
                                            _tpMsg("THUMBHEIGHT_TITLE"),
                                            _tpMsg("THUMBHEIGHT_DESC"),
                                            "90", "integer"),

                  
              /* -------- ADVANCED OPTIONS ------------------------------------- */                    
                  
                  TP_OPT_KEYWORD =>  new TubePressOption(TP_OPT_KEYWORD,
                                         _tpMsg("KEYWORD_TITLE"),
                                         _tpMsg("KEYWORD_DESC"), TP_OPTION_NAME),
                                         
                  TP_OPT_TIMEOUT =>  new TubePressOption(TP_OPT_TIMEOUT,
                                         _tpMsg("TIMEOUT_TITLE"),
                                         _tpMsg("TIMEOUT_DESC"), "6", "integer"),
                                         
                  TP_OPT_DEVID =>    new TubePressOption(TP_OPT_DEVID,
                                         _tpMsg("DEVID_TITLE"),
                                         _tpMsg("DEVID_DESC") .
                                         ' <a href="' . TP_YOUTUBEDEVLINK . '">' .
                                         TP_YOUTUBEDEVLINK . '</a>', "qh7CQ9xJIIc"),
                                         
                  TP_OPT_USERNAME => new TubePressOption(TP_OPT_USERNAME,
                                         _tpMsg("USERNAME_TITLE"), 
                                         _tpMsg("USERNAME_DESC"), "3hough"),
                                          
                  TP_OPT_DEBUG => new TubePressOption(TP_OPT_DEBUG,
                                     _tpMsg("DEBUGTITLE"), true, "boolean"),
 
         /* -------- VIDEO SEARCH OPTION ----------------------------------- */

                  TP_OPT_SEARCHBY => new TubePressOption(TP_OPT_SEARCHBY, ' ',
                                         '', TP_MODE_FEATURED),

        /* -------- PLAYER LOCATION OPTION ----------------------------------- */
 
                  TP_OPT_PLAYIN => new TubePressOption(TP_OPT_PLAYIN, 
                                       _tpMsg("PLAYIN_TITLE"), ' ', TP_PLAYIN_NORMAL));
                          
        $returnVal[TP_OPT_PLAYIN]->setValidValues(TubePressOptionsPackage::getPlayerLocationNames());
        $returnVal[TP_OPT_SEARCHBY]->setValidValues(TubePressOptionsPackage::getModeNames());
                
        return $returnVal;                        
    }

    /**
     * Checks to see if parameter appears to be a correct set of options
     * 
     * @param An array of the options that the user currently has
     * (typically pulled from the db)
     */
    function areValid($suspectOptions)
    {
        if ($suspectOptions == NULL) {
            return PEAR::raiseError(_tpMsg("NODB"));
        }
        if (!is_array($suspectOptions)) {
            return PEAR::raiseError(_tpMsg("BADDB"));
        }
        
        $modelOptions = TubePressOptionsPackage::getDefaultPackage();
        
        foreach ($modelOptions as $defaultOption) {
        	/* Make sure we have all the keys */
            if (!array_key_exists($defaultOption->getName(), $suspectOptions)) {
                return PEAR::raiseError(_tpMsg("DBMISS", 
                    array($defaultOption->getName(), 
                        count($suspectOptions), count($modelOptions))));
            }

        	/* Make sure each entry is a valid TubePressOption */
            if (!is_a($suspectOptions[$defaultOption->getName()], TubePressOption)) {
                return PEAR::raiseError(_tpMsg("OLDDB"));
            }
        }
        
        /* finally, make sure that we have the right number of options */
        if (count($suspectOptions) != count($modelOptions)) {
        	return PEAR::raiseError("You have extra options! Expecting " . count($modelOptions)
        	. " but you seem to have " . count($suspectOptions));
        }
    }
    
    /**
     * FIXME
     */
    function getPlayerLocationNames()
    {
        return
            array(TP_PLAYIN_NORMAL, TP_PLAYIN_NW, TP_PLAYIN_YT, 
                TP_PLAYIN_POPUP,TP_PLAYIN_LWINDOW,TP_PLAYIN_THICKBOX);
    }
    
    /**
     * FIXME
     */
    function getModeNames()
    {
        return
            array(TP_MODE_USER, TP_MODE_FAV, TP_MODE_PLST,TP_MODE_TAG, 
                 TP_MODE_FEATURED, TP_MODE_POPULAR);
    }
}
?>
