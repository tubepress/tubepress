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


/**
 * The idea here is that each implementation (WordPress, MoveableType)
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
     * Tells which options are the meta value-related ones
     */
    function getMetaNames()
    {
        return array(
            TP_VID_TITLE, TP_VID_LENGTH, TP_VID_VIEW,
            TP_VID_AUTHOR, TP_VID_ID, TP_VID_RATING_AVG,
            TP_VID_RATING_CNT, TP_VID_UPLOAD_TIME,
            TP_VID_TAGS, TP_VID_URL, TP_VID_DESC,
            TP_VID_CATEGORY
            //,TP_VID_COMMENT_CNT
        );
    }
    
    /**
     * Returns a fresh array of TubePress options.
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
                  //TP_VID_COMMENT_CNT => new TubePressBooleanOpt(_tpMsg("VIDCOMMENTS"), ' ', false),
                  TP_VID_TAGS => new TubePressBooleanOpt(_tpMsg("VIDTAGS"), ' ', false),
                  TP_VID_URL => new TubePressBooleanOpt(_tpMsg("VIDURL"), ' ', false),
     
                  TP_VID_DESC => new TubePressBooleanOpt(_tpMsg("VIDDESC"), ' ', false),
                  TP_VID_CATEGORY => new TubePressBooleanOpt(_tpMsg("VIDCAT"), ' ', false),
            
           /* -------- DISPLAY OPTIONS -------------------------------------- */
                  
                  TP_OPT_ORDERBY => new TubePressEnumOpt(
                      "Order videos by", " ", "updated",
                      array("updated", "viewCount", "rating", "relevance")),
                  
                  TP_OPT_VIDSPERPAGE=>  new TubePressIntegerOpt(
                      _tpMsg("VIDSPERPAGE_TITLE"), _tpMsg("VIDSPERPAGE_DESC"), 20, 50),      
                  TP_OPT_VIDWIDTH =>    new TubePressIntegerOpt(
                      _tpMsg("VIDWIDTH_TITLE"), _tpMsg("VIDWIDTH_DESC"), 424),
                  TP_OPT_VIDHEIGHT =>   new TubePressIntegerOpt(
                      _tpMsg("VIDHEIGHT_TITLE"), _tpMsg("VIDHEIGHT_DESC"), 336),
                  TP_OPT_THUMBWIDTH =>  new TubePressIntegerOpt(
                      _tpMsg("THUMBWIDTH_TITLE"), _tpMsg("THUMBWIDTH_DESC"), 120, 120),
                  TP_OPT_THUMBHEIGHT => new TubePressIntegerOpt(
                      _tpMsg("THUMBHEIGHT_TITLE"), _tpMsg("THUMBHEIGHT_DESC"), 90, 90),
                  TP_OPT_GREYBOXON => new TubePressBooleanOpt(
                      _tpMsg("TP_OPT_GREYBOXON_TITLE"), _tpMsg("TP_OPT_GREYBOXON_DESC"),
                       false),
                  TP_OPT_LWON => new TubePressBooleanOpt(
                      _tpMsg("TP_OPT_LWON_TITLE"), _tpMsg("TP_OPT_LWON_DESC")
                      , false),
                   
              /* -------- ADVANCED OPTIONS ------------------------------------- */                    
                  
                  TP_OPT_KEYWORD =>  new TubePressStringOpt(
                      _tpMsg("KEYWORD_TITLE"), _tpMsg("KEYWORD_DESC"), "tubepress"),
                                         
                  TP_OPT_TIMEOUT =>  new TubePressIntegerOpt(
                      _tpMsg("TIMEOUT_TITLE"), _tpMsg("TIMEOUT_DESC"), 6),
                                          
                  TP_OPT_DEBUG => new TubePressBooleanOpt(
                      _tpMsg("DEBUGTITLE"), _tpMsg("DEBUGDESC"), true),
                      
                  TP_OPT_RANDOM_THUMBS => new TubePressBooleanOpt(
                    "Randomize thumbnails", "Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video's thumbnail randomized", true),
 
                  TP_OPT_FILTERADULT => new TubePressBooleanOpt(
                  "Filter \"racy\" content", "Not sure who decides what's racy and what isn't, but YouTube has this as an option for you", false),
                  
         /* -------- VIDEO SEARCH OPTION ----------------------------------- */

                  TP_OPT_MODE => new TubePressEnumOpt(_tpMsg("MODE_TITLE"),
                      ' ', TP_MODE_FEATURED, 
                      TubePressModePackage::getNames()),

        /* -------- PLAYER LOCATION OPTION ----------------------------------- */
 
                  TP_OPT_PLAYIN => new TubePressEnumOpt( 
                      _tpMsg("PLAYIN_TITLE"), ' ', TP_PLAYIN_NORMAL,
                      TubePressPlayerPackage::getNames()));                       
    }
    
    /**
     * This is ugly as hell, but MUCH faster than calling array_keys
     * on a new default package
     */
    function getNames()
    {
        return array(TP_VID_TITLE, TP_VID_LENGTH, TP_VID_VIEW, TP_VID_AUTHOR,
            TP_VID_ID, TP_VID_RATING_AVG, TP_VID_RATING_CNT, TP_VID_UPLOAD_TIME,
             TP_VID_TAGS, TP_VID_URL, TP_VID_DESC,
            TP_VID_CATEGORY,
            TP_OPT_VIDSPERPAGE, TP_OPT_VIDWIDTH, TP_OPT_VIDHEIGHT, TP_OPT_THUMBWIDTH,
            TP_OPT_THUMBHEIGHT, TP_OPT_GREYBOXON, TP_OPT_LWON, TP_OPT_KEYWORD,
            TP_OPT_TIMEOUT, TP_OPT_DEBUG, TP_OPT_MODE,
            TP_OPT_PLAYIN,TP_OPT_RANDOM_THUMBS, TP_OPT_FILTERADULT, TP_OPT_ORDERBY
            //,TP_VID_COMMENT_CNT
            );
    }
    
    /**
     * Which types of data can we store?
     */
    function getValidTypes()
    {
        return array(
            "TubePressBooleanOpt", "TubePressEnumOpt",
            "TubePressIntegerOpt", "TubePressStringOpt"
        );
    }

}
?>
