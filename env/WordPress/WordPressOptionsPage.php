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
    function printHTML_advanced($options)
    {
        WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_KEYWORD, $options);
        WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_TIMEOUT, $options);
        WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_DEVID, $options);
        WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_USERNAME, $options);
      
        WordPressOptionsPage::_printHTML_booleanOpt($options, TP_OPT_DEBUG, "DEBUGDESC");
        WordPressOptionsPage::_printHTML_optionFooter(); 
    }
    
    /**
     * Prints out the display options. Simple!
     */
    function printHTML_display($options) {
        WordPressOptionsPage::_printHTML_optionHeader(_tpMsg("VIDDISP"));
       
        WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_VIDSPERPAGE, $options);
        WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_VIDWIDTH, $options);
        WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_VIDHEIGHT, $options);
        WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_THUMBWIDTH, $options);
        WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_THUMBHEIGHT, $options);

        WordPressOptionsPage::_printHTML_booleanOpt($options, TP_OPT_GREYBOXON,
            "TP_OPT_GREYBOXON_DESC");
        WordPressOptionsPage::_printHTML_booleanOpt($options, TP_OPT_LWON,
            "TP_OPT_LWON_DESC");
      
        WordPressOptionsPage::_printHTML_optionFooter();    
    }
    
    function _printHTML_booleanOpt($options, $optName, $msg) {
        $selected = "";
        if ($options->getValue($optName) == true) {
            $selected = "CHECKED";
        }
        printf('<tr valign="top"><th style="font-weight: bold; font-size: 1em" scope="row">' .
                '%s</th><td><input type="checkbox" name="%s" value="%s" %s /><br />%s</td>', 
            $options->getTitle($optName), $optName, $optName, $selected,
            _tpMsg("$msg"));
    }
    
    /**
     * Prints out the drop down menu asking where to play the videos
     * (normally, new window, popup, in youtube, etc.)
     */
    function printHTML_player($options) {
        $locationVars =     $options->getPlayerLocationNames();
        
        WordPressOptionsPage::_printHTML_optionHeader("");

        printf('<tr> <th style="font-weight: bold; font-size: 1em">%s</th>', 
            $options->getTitle(TP_OPT_PLAYIN));
        printf('<td><select name="%s">', TP_OPT_PLAYIN);

        foreach ($locationVars as $location) {
            $selected = "";
            if ($location == $options->getValue(TP_OPT_PLAYIN))
                $selected = "selected";
            $inputBox = "";
    
            $desc = "";
            switch ($location) {
                case TP_PLAYIN_NORMAL:
                    $desc = _tpMsg("PLAYIN_NORMAL_TITLE");
                    break;
                case TP_PLAYIN_NW:
                    $desc = _tpMsg("PLAYIN_NW_TITLE");
                    break;
                case TP_PLAYIN_YT:
                    $desc = _tpMsg("PLAYIN_YT_TITLE");
                    break;
                case TP_PLAYIN_POPUP:
                    $desc = _tpMsg("PLAYIN_POPUP_TITLE");
                    break;
                case TP_PLAYIN_LWINDOW:
                    $desc = _tpMsg("PLAYIN_LW_TITLE");
                    break;
                case TP_PLAYIN_GREYBOX:
                    $desc = _tpMsg("PLAYIN_TB_TITLE");
                    break;
            }
    
    
            printf('<option value="%s" %s>%s</option>', $location, $selected, $desc);
        }
        
        echo "</select></td></tr>";
        
        WordPressOptionsPage::_printHTML_optionFooter();
    }
    

    
    /**
     * Prints out the meta value checkboxes. Fascinating stuff here!
     */
    function printHTML_meta($options)
    {
        WordPressOptionsPage::_printHTML_optionHeader(_tpMsg("META"));
        
        $metas = TubePressVideo::getMetaNames();
        
        echo "<tr><td width='10%'></td><td><table cellspacing='0' " .
                "cellpadding='0' width='100%'>";
    
        $colIterator = 0;
        foreach ($metas as $meta) {
    
            $colCount = $colIterator % 5;
    
            $selected = "";
            if ($options->getValue($meta) == true) {
                $selected = "CHECKED";
            }
            
            if ($colCount == 0) {
                echo "<tr>";
            }
    
            printf('<td><input type="checkbox" name="meta[]" value="%s" %s />' .
                '</td><td><b>%s</b></td>',
                $meta, $selected,
                $options->getTitle($meta));
                    
            if ($colCount == 4) {
                echo "</tr>";
            }
            
            $colIterator++;
        }
        echo "</tr></table>";
        
        WordPressOptionsPage::_printHTML_optionFooter();
    }
    


    /**
     * Prints out the mode options. Simple!
     */
    function printHTML_modes(&$tpl, $options)
    {
    	$tpl->setCurrentBlock('modes')
        $tpl->setVariable('TITLE', _tpMsg("MODE_HEADER"));
        
        $modes = TubePressOptionsPackage::getModeNames();

        foreach ($modes as $mode) {
        	if ($mode == TP_MODE_REL) {
        		continue;
        	}
        	
            $selected = "";
            
            if ($mode == $options->getValue(TP_OPT_MODE)) {
                $selected = "CHECKED";
            }
            $inputBox = "";
            
            /* The idea here is only the "featured" mode doesn't need any kind of input */
            if ($mode != TP_MODE_FEATURED) {
                    $inputBox = WordPressOptionsPage::_printHTML_quickSrchVal($mode, 
                        $options, 20);
            }
            
            /* handle the "popular" mode */
            if ($mode == TP_MODE_POPULAR) {
                
                $name = TP_OPT_POPVAL;
                $inputBox = sprintf('<select name="%s">', $name);
                $period = array("day", "week", "month");
                foreach ($period as $thisPeriod) {
                    $inputBox .= sprintf('<option value="%s"', $thisPeriod);
                    if ($thisPeriod == $options->getValue(TP_OPT_POPVAL)) {
                        $inputBox .= ' SELECTED';
                    }
                    $inputBox .= sprintf('>%s</option>', $thisPeriod);
                }
                $inputBox .= '</select>';
            }

            $title = "";
            $desc = "";
            
            switch($mode) {
                case TP_MODE_POPULAR:
                    $title = _tpMsg("MODE_POPULAR_TITLE");
                    break;
                case TP_MODE_FEATURED:
                    $title = _tpMsg("MODE_FEAT_TITLE");
                    break;
                case TP_MODE_FAV:
                    $title = _tpMsg("MODE_FAV_TITLE");
                    $desc = _tpMsg("MODE_FAV_DESC");
                    break;
                case TP_MODE_PLST:
                    $title = _tpMsg("MODE_PLST_TITLE");
                    $desc = _tpMsg("MODE_PLST_DESC");
                    break;
                case TP_MODE_TAG:
                    $title = _tpMsg("MODE_TAG_TITLE");
                    break;
                case TP_MODE_USER:
                    $title = _tpMsg("MODE_USER_TITLE");
                    break;
                default:
            }

            printf('<tr><th style="font-weight: bold; font-size: 1em" valign="top">%s</th>' .
                '<td><input type="radio" name="%s" id="%s" value="%s" %s /> %s <br />%s</td></tr>',
                $title, TP_OPT_MODE, $mode, $mode, $selected, $inputBox,
                $desc);
        }
         echo "<sup>*</sup><i>mode supports pagination</i>";
        WordPressOptionsPage::_printHTML_optionFooter();
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
    
        /* get what we have in the db */
        $oldOpts = new WordPressOptionsPackage();
        if (PEAR::isError($oldOpts)) {
            WordPressOptionsPage::printStatusMsg($oldOpts->message, TP_CSS_FAILURE);
            return;
        }

        /* go through the post variables and try to update */
        foreach (array_keys($oldOpts->_allOptions) as $optName) {
            if (($optName == TP_OPT_DEBUG)
                || ($optName == TP_OPT_GREYBOXON)
                || ($optName == TP_OPT_LWON)
                || in_array($optName, TubePressVideo::getMetaNames())
                || in_array($optName, TubePressOptionsPackage::getPlayerLocationNames())
                || in_array($optName, TubePressOptionsPackage::getModeNames())) {
                    continue;
            }
            
            $result = $oldOpts->setValue($optName, $_POST[$optName]);
                
            if (PEAR::isError($result)) {
                $errors = true;
                WordPressOptionsPage::printStatusMsg($result->message, TP_CSS_FAILURE);
                return;
            }
        }

        /* Do the booleans */
        $metaOptions = TubePressVideo::getMetaNames();
        foreach ($metaOptions as $metaOption) {
        	if (!WordPressOptionsPage::_updateBoolean($metaOption, $oldOpts, $errors,
        	    in_array($metaOption, $_POST['meta']))) {
        		return;
        	}
        }

        if (!WordPressOptionsPage::_updateBoolean(TP_OPT_DEBUG, $oldOpts,
            $errors, isset($_POST[TP_OPT_DEBUG]))) {
        	return;
        }
        if (!WordPressOptionsPage::_updateBoolean(TP_OPT_GREYBOXON, $oldOpts,
            $errors, isset($_POST[TP_OPT_GREYBOXON]))) {
        	return;
        }
        if (!WordPressOptionsPage::_updateBoolean(TP_OPT_LWON, $oldOpts,
            $errors, isset($_POST[TP_OPT_LWON]))) {
        	return;
        }
    
        /* now actually update is we didn't have any errors */
        if (!$errors) {
            update_option(TP_OPTION_NAME, $oldOpts->_allOptions);
        } else {
            return;
        }
        
        /* make sure the store happened */
        $oldOpts = new WordPressOptionsPackage();
        if (PEAR::isError($oldOpts)) {
            WordPressOptionsPage::printStatusMsg($oldOpts->msg, TP_CSS_FAILURE);
        } else {
            WordPressOptionsPage::printStatusMsg(_tpMsg("OPTSUCCESS"), TP_CSS_SUCCESS);
        }
    }    
    
    function _updateBoolean($optName, &$options, &$errors, $newValue) {
        $result = $options->setValue($optName, $newValue);
        if (PEAR::isError($result)) {
            $errors = true;
            WordPressOptionsPage::printStatusMsg($result->message, TP_CSS_FAILURE);
            return false;
        }
        return true;
    }
    
    /**
     * Spits out a bit of HTML at the bottom of each option group
     */
    function _printHTML_optionFooter() {
        echo "</table></fieldset>";
    }
    
        /**
     * Prints out the HTML inputs for determining which videos to play
     * (all tags, any tags, etc.). This is really a helper function
     * for printHTML_searchArray()
     */
    function _printHTML_quickSrchVal($mode, $options, $inputSize)
    {
        $whichValue = "";
        
        switch ($mode) {
        
            case TP_MODE_TAG:
                $whichValue = TP_OPT_TAGVAL;
                $inputSize = 40;
                break;

            case TP_MODE_USER:
                $whichValue = TP_OPT_USERVAL;
                break;
        
            case TP_MODE_PLST:
                $whichValue = TP_OPT_PLSTVAL;
                break;
        
            case TP_MODE_POPULAR:
                $whichValue = TP_OPT_POPVAL;
                break;
        
            case TP_MODE_CATEGORY:
                $whichValue = TP_OPT_CATVAL;
                break;
        
            case TP_MODE_FAV:
                $whichValue = TP_OPT_FAVVAL;
                break;
        }
        return sprintf('<input type="text" name="%s" size="%s" value="%s" />',
            $whichValue, $inputSize, $options->getValue($whichValue));
    }
    
    /**
     * Helper function to spit out a textbox input
     */
    function _printHTML_textBoxOption($optionName, $options) {
        $openBracket = "";
        $closeBracket = "";
        if ($optionName == TP_OPT_KEYWORD) {
            $openBracket = '[';
               $closeBracket = ']';
        } 
        printf('<tr valign="top"><th style="font-weight: bold; font-size: 1em" scope="row">' .
            '%s</th><td>%s<input name="%s" type="text" id="%s" class="code" value="%s" ' .
            'size="%s" />%s<br />%s</td></tr>',               
            $options->getTitle($optionName), $openBracket,
            $optionName, $optionName,
            $options->getValue($optionName), 20, $closeBracket,
            $options->getDescription($optionName)
            );
    }
}
?>
