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
class TubePressOptionsPackage extends TubePressDataPackage
{
    /**
     * Default options
     */
    function TubePressOptionsPackage()
    {
        $this->_dataArray = TubePressOptionsPackage::getDefaultPackage();
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
}
?>
