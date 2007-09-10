<?php
/**
 * WordPressOptionsPage.php
 * 
 * Handles everything related to the options page
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

class WordPressOptionsPage
{
    /**
     * Prints out the advanced options. Simple!
     */
    function printHTML_advanced(&$tpl, $stored)
    {
        $tpl->setVariable("ADVTITLE", _tpMsg("ADV_GRP_TITLE"));
        
        $texts = array(TP_OPT_KEYWORD, TP_OPT_TIMEOUT);
        $bools = array(TP_OPT_DEBUG);
            
        foreach ($texts as $text) {
            $opt = $stored->options->get($text);
            $tpl->setVariable("ADVTEXTTITLE", $opt->getTitle());
            $tpl->setVariable("ADVTEXTNAME", $text);
            $tpl->setVariable("ADVTEXTVALUE", $opt->getValue());
            $tpl->setVariable("ADVTEXTDESCRIPTION", $opt->getDescription());
            $tpl->parse("advancedTextOption");
        }

        foreach ($bools as $bool) {
            $opt = $stored->options->get($bool);
            $tpl->setVariable("ADVBOOLTITLE", $opt->getTitle());
            $tpl->setVariable("ADVBOOLNAME", $bool);
            if ($opt->getValue() == true) {
                $tpl->setVariable("ADVBOOLSELECTED", "CHECKED");
            }
            $tpl->setVariable("ADVBOOLDESCRIPTION", $opt->getDescription());
            $tpl->parse("advancedBoolOption");
        }
    }
    
    /**
     * Prints out the display options. Simple!
     */
    function printHTML_display(&$tpl, $stored) {

        $tpl->setVariable("DISPLAYTITLE", _tpMsg("VIDDISP"));
        
        $texts = array(TP_OPT_VIDSPERPAGE, TP_OPT_VIDWIDTH,
            TP_OPT_VIDHEIGHT, TP_OPT_THUMBWIDTH, TP_OPT_THUMBHEIGHT);
        $bools = array(TP_OPT_GREYBOXON, TP_OPT_LWON);
            
        foreach ($texts as $text) {
            $opt = $stored->options->get($text);
            $tpl->setVariable("DISPOPTTITLE", $opt->getTitle());
            $tpl->setVariable("DISPOPTNAME", $text);
            $tpl->setVariable("DISPOPTVALUE", $opt->getValue());
            $tpl->setVariable("DISPOPTDESCRIPTION", $opt->getDescription());
            $tpl->parse("displayTextOption");
        }

        foreach ($bools as $bool) {
            $opt = $stored->options->get($bool);
            $tpl->setVariable("DISPBOOLTITLE", $opt->getTitle());
            $tpl->setVariable("DISPBOOLNAME", $bool);
            if ($opt->getValue() == true) {
                $tpl->setVariable("DISPBOOLSELECTED", "CHECKED");
            }
            $tpl->setVariable("DISPBOOLDESCRIPTION", $opt->getDescription());
            $tpl->parse("displayBoolOption");
        }
    }
    
    /**
     * Prints out the drop down menu asking where to play the videos
     * (normally, new window, popup, in youtube, etc.)
     */
    function printHTML_player(&$tpl, $stored) {
        
        $tpl->setVariable("PLAYERTITLE", _tpMsg("PLAYIN_TITLE"));
        
        $currentLoc = $stored->options->get(TP_OPT_PLAYIN);
        
        $locationVars = $stored->players->getNames();

        foreach ($locationVars as $locationName) {
            $actualPlayer = $stored->players->get($locationName);
            $tpl->setVariable("PLAYERNAME", $locationName);
            $tpl->setVariable("PLAYERDESCRIPTION", $actualPlayer->getTitle());
            if ($locationName == $currentLoc->getValue()) {
                $tpl->setVariable("PLAYERSELECTED", "selected=\"selected\"");
            }
            $tpl->parse("playerLocation");
        }
    }
    
    /**
     * Prints out the meta value checkboxes. Fascinating stuff here!
     */
    function printHTML_meta(&$tpl, $stored)
    {
        $tpl->setVariable("METATITLE", _tpMsg("META"));
        
        $metas = TubePressOptionsPackage::getMetaNames();
        
        $colIterator = 0;
        foreach ($metas as $meta) {
            $actualMeta = $stored->options->get($meta);
            
            $tpl->setVariable("METANAME", $meta);
            if ($actualMeta->getValue() == true) {
                $tpl->setVariable("METASELECTED", "CHECKED");
            }
            $tpl->setVariable("METAOPTIONTITLE", $actualMeta->getTitle());
            $tpl->parse("metaOption");
   
            if ($colIterator++ % 5 == 4) {
                $tpl->parse("metaOptionRow");
            }
        }
    }
    
    /**
     * Prints out the mode options. Simple!
     */
    function printHTML_modes(&$tpl, $stored)
    {
        $tpl->setVariable('TITLE', _tpMsg("MODE_HEADER"));

        foreach ($stored->modes->getNames() as $modeName) {
            
            $storedMode = $stored->options->get(TP_OPT_MODE);
            $actualMode = $stored->modes->get($modeName);;
            /* handle featured */
            if ($modeName == TP_MODE_FEATURED) {
                $tpl->setVariable('FEATUREDTITLE', $actualMode->getTitle());
                $tpl->setVariable('FEATUREDNAME', $modeName);
                $tpl->setVariable('FEATUREDVALUE', $actualMode->getValue());
                
                if ($modeName == $storedMode->getValue()) {
                    $tpl->setVariable("FEATUREDSELECTED", "checked");
                }
                
                $tpl->parse("featuredMode");
                continue;
            }
            
            /* handle the "popular" mode */
            if ($modeName == TP_MODE_POPULAR) {
                $tpl->setVariable("POPULARTITLE", $actualMode->getTitle());
                $tpl->setVariable("POPULARNAME", $modeName);
                $tpl->setVariable("POPULARVALUE", $actualMode->getValue());
             
                $period = array("day", "month", "week", "all time");
                foreach ($period as $thisPeriod) {
                    
                    $tpl->setVariable("PERIOD", $thisPeriod);
                    
                    if ($thisPeriod == $actualMode->getValue()) {
                        $tpl->setVariable("PERIODSELECTED", "selected");
                    }
                    $tpl->parse("period");
                }
                if ($modeName == $storedMode->getValue()) {
                    $tpl->setVariable("POPULARSELECTED", "checked=\"checked\"");
                }
                
                $tpl->parse("popularMode");
                continue;
            }
            
            $tpl->setVariable("MODETITLE", $actualMode->getTitle());
            $tpl->setVariable("MODEVALUE", $actualMode->getValue());
            $tpl->setVariable("MODENAME", $modeName);
            if ($modeName == $storedMode->getValue()) {
                    $tpl->setVariable("MODESELECTED", "checked=\"checked\"");
            }
            $tpl->parse("normalMode");
        }
    }
    
    /**
     * Prints out the success or failure message after updating
     */
    function printStatusMsg($msg, $cssClass)
    {
        printf('<div id="message" class="%s"><p><strong>%s</strong></p></div>',
            $cssClass, $msg);
    }
    
    /**
     * Go through all the post variables and update the corresponding
     * database entries.
     */
    function update()
    {
        $errors = false;

        /* First get what we have in the DB */
        $stored = get_option(TP_OPTION_NAME);
        if ($stored == NULL) {
            WordPressOptionsPage::printStatusMsg("Options did not store!",
            TP_CSS_FAILURE);
            return;
        }
        
        $valid = $stored->checkValidity();
        if (PEAR::isError($valid)) {
            WordPressOptionsPage::printStatusMsg($valid->message,
            TP_CSS_FAILURE);
            return;
        }
       
        /* Do the modes */
        $modes = $stored->modes->getNames();
        foreach ($modes as $mode) {
            $actualMode =& $stored->modes->get($mode);
            $result = $actualMode->setValue($_POST[$mode]);
          
            if (PEAR::isError($result)) {
                WordPressOptionsPage::printStatusMsg($result->message, TP_CSS_FAILURE);
                return;
            }
        }
        $actualMode =& $stored->options->get(TP_OPT_MODE);
        $result = $actualMode->setValue($_POST['mode']);
        if (PEAR::isError($result)) {
            WordPressOptionsPage::printStatusMsg($result->message, TP_CSS_FAILURE);
            return; 
        }
        $popMode =& $stored->modes->get(TP_MODE_POPULAR);
        $result = $popMode->setValue($_POST[TP_OPT_POPVAL]);
        if (PEAR::isError($result)) {
            WordPressOptionsPage::printStatusMsg($result->message, TP_CSS_FAILURE);
            return; 
        }
        
        /* Do the display options */
        $texts = array(TP_OPT_VIDSPERPAGE, TP_OPT_VIDWIDTH,
            TP_OPT_VIDHEIGHT, TP_OPT_THUMBWIDTH, TP_OPT_THUMBHEIGHT);
        $bools = array(TP_OPT_GREYBOXON, TP_OPT_LWON);
        foreach ($texts as $text) {
            $actualOpt =& $stored->options->get($text);
            $result = $actualOpt->setValue($_POST[$text]);
          
            if (PEAR::isError($result)) {
                WordPressOptionsPage::printStatusMsg($result->message, TP_CSS_FAILURE);
                return;
            }
        }
        foreach ($bools as $bool) {
            $actualOpt =& $stored->options->get($bool);
            $result = $actualOpt->setValue(isset($_POST[$bool]));
            if (PEAR::isError($result)) {
                WordPressOptionsPage::printStatusMsg($result->message, TP_CSS_FAILURE);
                return;
            }
        }
        
        /* Do the player */
        $playerOpt =& $stored->options->get(TP_OPT_PLAYIN);
        $result = $playerOpt->setValue($_POST[TP_OPT_PLAYIN]);
        if (PEAR::isError($result)) {
                WordPressOptionsPage::printStatusMsg($result->message, TP_CSS_FAILURE);
                return;
        }

        /* Do the meta */
        $metaOptions = TubePressOptionsPackage::getMetaNames();
        foreach ($metaOptions as $metaOption) {
            $actualOpt =& $stored->options->get($metaOption);
            
            $result = $actualOpt->setValue(in_array($metaOption, $_POST['meta']));
            if (PEAR::isError($result)) {
                WordPressOptionsPage::printStatusMsg($result->message, TP_CSS_FAILURE);
                return;
            }
        }

        /* Do the advanced options */
        $texts = array(TP_OPT_KEYWORD, TP_OPT_TIMEOUT);
        $bools = array(TP_OPT_DEBUG);
        foreach ($texts as $text) {
            $actualOpt =& $stored->options->get($text);
            $result = $actualOpt->setValue($_POST[$text]);
          
            if (PEAR::isError($result)) {
                WordPressOptionsPage::printStatusMsg($result->message, TP_CSS_FAILURE);
                return;
            }
        }
        foreach ($bools as $bool) {
            $actualOpt =& $stored->options->get($bool);
            $result = $actualOpt->setValue(isset($_POST[$bool]));
            if (PEAR::isError($result)) {
                WordPressOptionsPage::printStatusMsg($result->message, TP_CSS_FAILURE);
                return;
            }
        }
        
    
        /* now actually update since we didn't have any errors */
        update_option(TP_OPTION_NAME, $stored);
        $new = get_option(TP_OPTION_NAME);

        if ($new == NULL) {
            WordPressOptionsPage::printStatusMsg("Options did not store!",
            TP_CSS_FAILURE);
            return;
        }
        
        $valid = $new->checkValidity();
        if (PEAR::isError($valid)) {
            WordPressOptionsPage::printStatusMsg($valid->message,
            TP_CSS_FAILURE);
            return;
        }
            WordPressOptionsPage::printStatusMsg(_tpMsg("OPTSUCCESS"), TP_CSS_SUCCESS);
      
    }    
}
?>
