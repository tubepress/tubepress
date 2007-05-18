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
     * Helper utility to print out a list of text boxes, labels, descriptions,
     * and default values
     */
    function printHTML_genericOptionsArray($theArray, 
        $arrayName, $inputSize = 20, $radioName = '')
    {
        WordPressOptionsPage::printHTML_optionHeader($arrayName);
    
        $openBracket = "";
        $closeBracket = "";
        foreach ($theArray as $option) {
    
            if ($option->getName() == TP_OPT_KEYWORD) {
                $openBracket = '[';
                $closeBracket = ']';
            } else {
                $openBracket = "";
                $closeBracket = "";
            }
            print <<<EOT
                <tr valign="top">
                    <th style="font-weight: bold; font-size: 1em" 
                        scope="row">$option->getTitle():</th>
                    <td>$openBracket<input name="$option->getName()" 
                        type="text" id="$option->getName()" class="code"
                        value="$option->getValue()" size="$inputSize" />
                        $closeBracket
                        <br />$option->getDescription()
                    </td>
                </tr>
EOT;
        }
        WordPressOptionsPage::printHTML_optionFooter();
    }
    
    /**
     * Prints out the meta value checkboxes. Fascinating stuff here!
     */
    function printHTML_metaArray($theArray)
    {
        WordPressOptionsPage::printHTML_optionHeader(_tpMsg("META"));
        
        echo "<tr><td width='10%'></td><td><table cellspacing='0' " .
                "cellpadding='0' width='100%'>";
    
        $colIterator = 0;
        foreach ($theArray as $metaOption) {
    
            $colCount = $colIterator % 5;
    
            $selected = "";
            if ($metaOption->getValue() == true) {
            	$selected = "CHECKED";
            }
            
            if ($colCount == 0) {
            	echo "<tr>";
            }
    
            print <<<EOT
                <td>
                    <input type="checkbox" name="meta[]" value="$metaOption->getName()"
                    $selected />
                </td>
                <td><b>$metaOption->getTitle()</b></td>
EOT;
            
            if ($colCount == 4) {
            	echo "</tr>";
            }
            
            $colIterator++;
        }
        echo "</tr></table>";
        
        WordPressOptionsPage::printHTML_optionFooter();
    }
    
    /**
     * Spits out a bit of HTML at the top of each option group
     */
    function printHTML_optionHeader($arrayName)
    {
        echo "<fieldset>";
        
        if ($arrayName != "") {
            echo '<h3>' . $arrayName . '</h3>';
        }
    
        echo '<table class="editform optiontable">';
    }
    
    /**
     * Spits out a bit of HTML at the bottom of each option group
     */
    function printHTML_optionFooter() {
        echo "</table></fieldset>";
    }
    
    /**
     * Prints out the drop down menu asking where to play the videos
     * (normally, new window, popup, in youtube, etc.)
     */
    function printHTML_playerLocationMenu($dbOptions) {
        $locationVars =     $dbOptions->getPlayerLocationOptions();
        $theOption =        $dbOptions->get(TP_OPT_PLAYIN);
        
        WordPressOptionsPage::printHTML_optionHeader("");
    
        print <<<EOT
            <tr>
                <th style="font-weight: bold; font-size: 1em">
                    $theOption->getTitle()</th>
                <td><select name="$theOption->getName()">
EOT;
        foreach ($locationVars as $location) {
            $selected = "";
            if ($location->getName() == $theOption->getValue())
                $selected = "selected";
            $inputBox = "";
    
        print <<<EOT
            <option value="$location->getName()" $selected>$location->getTitle()</option>
EOT;
        }
        
        echo "</select></td></tr>";
        
        WordPressOptionsPage::printHTML_optionFooter();
    }
    
    /**
     * Prints out the HTML inputs for determining which videos to play
     * (all tags, any tags, etc.). This is really a helper function
     * for printHTML_searchArray()
     */
    function printHTML_quickSrchVal($value, $searchVars, $inputSize)
    {
        $whichValue = "";
        
        switch ($value) {
        
            case TP_SRCH_TAG:
                $whichValue = TP_SRCH_TAGVAL;
                $inputSize = 40;
                break;
        
            case TP_SRCH_REL:
                $whichValue = TP_SRCH_RELVAL;
                $inputSize = 40;
                break;
        
            case TP_SRCH_USER:
                $whichValue = TP_SRCH_USERVAL;
                break;
        
            case TP_SRCH_PLST:
                $whichValue = TP_SRCH_PLSTVAL;
                break;
        
            case TP_SRCH_POPULAR:
                $whichValue = TP_SRCH_POPVAL;
                break;
        
            //case TP_SRCH_CATEGORY: $whichValue = TP_SRCH_CATVAL;break;
        
            case TP_SRCH_FAV:
                $whichValue = TP_SRCH_FAVVAL;
                break;
        }
        return '<input type="text" name="' . $searchVars[$whichValue]->getName() 
            . '" size="' . $inputSize . '" value="' 
            . $searchVars[$whichValue]->getValue()
            . '" />';
    }
    
    /**
     * 
     */
    function printHTML_searchArray($theArray, 
        $searchVars, $inputSize=20)
    {
        WordPressOptionsPage::printHTML_optionHeader(_tpMsg("WHICHVIDS"));

        $radioName = TP_OPT_SEARCHBY;
    
        foreach ($theArray as $option) {
            $selected = "";
            
            if ($option->getName() == $searchVars[TP_OPT_SEARCHBY]->getValue()) {
                $selected = "CHECKED";
            }
            $inputBox = "";
            
            /* The idea here is only the "featured" mode doesn't need any kind of input */
            if ($option->getName() != TP_SRCH_FEATURED) {
                    $inputBox = WordPressOptionsPage::printHTML_quickSrchVal($option->getName(), 
                        $searchVars, $inputSize);
            }
            
            /* handle the "popular" mode */
            if ($option->getName() == TP_SRCH_POPULAR) {
            	
                $name = TP_SRCH_POPVAL;
                $inputBox = '<select name="' . $name . '">';
                $period = array("day", "week", "month");
                foreach ($period as $thisPeriod) {
                    $inputBox .= '<option value="' . $thisPeriod . '"';
                    if ($thisPeriod == $searchVars[TP_SRCH_POPVAL]->getValue()) {
                        $inputBox .= ' SELECTED';
                    }
                    $inputBox .= '>' . $thisPeriod . '</option>';
                }
                $inputBox .= '</select>';
            }
            
            print <<<EOT
                <tr>
                    <th style="font-weight: bold; font-size: 1em" valign="top">$option->getTitle()</th>
                    <td>
                        <input type="radio" name="$radioName" id="$option->getName()" value="$option->getName()" $selected /> $inputBox
                        <br />$option->getDescription()
                    </td>
                </tr>
EOT;
        }
   
        WordPressOptionsPage::printHTML_optionFooter();
    }
    
    /**
	 * Go through all the post variables and update the corresponding
	 * database entries.
	 */
	function update()
	{
	    $css = new TubePressCSS();
	    $errors = false;
	
	    $oldOpts = new WordPressOptionsPackage();
	    
	    if (PEAR::isError($oldOpts)) {
	    	WordPressOptionsPage::printStatusMsg($oldOpts->message, $css->failure_class);
	    	return;
	    }
	
	    /* go through the post variables and try to update */
        foreach (array_keys($oldOpts->_allOptions) as $optName) {
        	if (($optName == TP_DEBUG_ON)
        	    || array_key_exists($optName, $oldOpts->getMetaOptions())
        	    || array_key_exists($optName, $oldOpts->getSearchByOptions())
        	    || array_key_exists($optName, $oldOpts->getPlayerLocationOptions())) {
        	    	continue;
            }
        	
            $result = $oldOpts->set($oldOpts->get($optName), $_POST[$optName]);
        		
        	if (PEAR::isError($result)) {
        		$errors = true;
        		WordPressOptionsPage::printStatusMsg($result->message, $css->failure_class);
        		break;
        	}
        }

	    /* We treat meta values differently since they rely on true/false */
	    $metaOptions = $oldOpts->getMetaOptions();
	    
	    foreach (array_keys($metaOptions) as $index) {
	        $metaOption =& $metaOptions[$index];
	        if (in_array($metaOption->getName(), $_POST['meta'])) {	
	            $oldOpts->set($oldOpts->get($metaOption->getName()), true);
	        } else {
	            $oldOpts->set($oldOpts->get($metaOption->getName()), false);
	        }
	    }
	
	    if (!$errors) {
	    	update_option(TP_OPTION_NAME, $oldOpts->_allOptions);
	    } else {
	    	return;
	    }
	    
	    $oldOpts = new WordPressOptionsPackage();
	    
	    if (PEAR::isError($oldOpts)) {
	    	WordPressOptionsPage::printStatusMsg($oldOpts->msg, $css->failure_class);
	    } else {
	    	WordPressOptionsPage::printStatusMsg("Success!", $css->success_class);
	    }
    }
    
    function printStatusMsg($msg, $cssClass)
    {
	    print <<<EOT
	        <div id="message" class="$cssClass">
	            <p><strong></strong></p>
	        </div>
EOT;
    }
}
?>
