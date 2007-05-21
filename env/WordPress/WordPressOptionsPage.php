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
	function printHTML_advanced($options) {
	   WordPressOptionsPage::printHTML_optionHeader(_tpMsg("ADV_GRP_TITLE"));

	   WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_KEYWORD, $options);
	   WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_TIMEOUT, $options);
	   WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_DEVID, $options);
	   WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_USERNAME, $options);
	  
	   $selected = "";
            if ($options->getValue(TP_DEBUG_ON) == true) {
            	$selected = "CHECKED";
            }
        $debugName = TP_DEBUG_ON;
	  print <<< EOT
	                  <td>
                    <input type="checkbox" name="$debugName" value="$debugName"
                    $selected />
                </td>
                <td><b>$options->getTitle($debugName)</b></td>
EOT;
	  
		WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_THUMBHEIGHT, $options);

       WordPressOptionsPage::printHTML_optionFooter(); 
	}
	
	function printHTML_display($options) {
	   WordPressOptionsPage::printHTML_optionHeader(_tpMsg("VIDDISP"));
	   
	   WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_VIDSPERPAGE, $options);
	   WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_VIDWIDTH, $options);
	   WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_VIDHEIGHT, $options);
	   WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_THUMBWIDTH, $options);
	   WordPressOptionsPage::_printHTML_textBoxOption(TP_OPT_THUMBHEIGHT, $options);

       WordPressOptionsPage::printHTML_optionFooter();    
	}
	
	
	/**
     * Prints out the drop down menu asking where to play the videos
     * (normally, new window, popup, in youtube, etc.)
     */
    function printHTML_player($options) {
        $locationVars =     $options->getPlayerLocationNames();
        
        WordPressOptionsPage::printHTML_optionHeader("");

        print <<<EOT
            <tr>
                <th style="font-weight: bold; font-size: 1em">
                    $options->getTitle(TP_OPT_PLAYIN)</th>
                <td><select name="$options->getName(TP_OPT_PLAYIN)">
EOT;
        foreach ($locationVars as $location) {
            $selected = "";
            if ($location == $options->getValue(TP_OPT_PLAYIN))
                $selected = "selected";
            $inputBox = "";
    
        print <<<EOT
            <option value="$location" $selected>$location</option>
EOT;
        }
        
        echo "</select></td></tr>";
        
        WordPressOptionsPage::printHTML_optionFooter();
    }
	
	function _printHTML_textBoxOption($optionName, $options) {
		$openBracket = "";
        $closeBracket = "";
		if ($optionName == TP_OPT_KEYWORD) {
        	$openBracket = '[';
           	$closeBracket = ']';
        } 
		print <<<EOT
        	<tr valign="top">
            	<th style="font-weight: bold; font-size: 1em" scope="row">
                		$options->getTitle($optionName):
               	</th>
              	<td>$openBracket
              		<input name="$options->getName($optionName)" 
                    	type="text" id="$options->getName($optionName)" class="code"
                        value="$options->getValue($optionName)" size="$inputSize" />
                        	$closeBracket<br />$options->getDescription($optionName)
              	</td>
          	</tr>
EOT;
	}
    
    /**
     * Prints out the meta value checkboxes. Fascinating stuff here!
     */
    function printHTML_meta($options)
    {
        WordPressOptionsPage::printHTML_optionHeader(_tpMsg("META"));
        
        $metas = TubePressOptionsPackage::getMetaOptionNames();
        
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
    
            print <<<EOT
                <td>
                    <input type="checkbox" name="meta[]" value="$options->getName($meta)"
                    $selected />
                </td>
                <td><b>$options->getTitle($meta)</b></td>
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
     * 
     */
    function printHTML_modes($options)
    {
        WordPressOptionsPage::printHTML_optionHeader(_tpMsg("WHICHVIDS"));

        $radioName = TP_OPT_SEARCHBY;
        
        $modes = TubePressOptionsPackage::getModeNames();
        
        foreach ($modes as $mode) {
            $selected = "";
            
            if ($mode == $options->getValue(TP_OPT_SEARCHBY)) {
                $selected = "CHECKED";
            }
            $inputBox = "";
            
            /* The idea here is only the "featured" mode doesn't need any kind of input */
            if ($mode != TP_SRCH_FEATURED) {
                    $inputBox = WordPressOptionsPage::_printHTML_quickSrchVal($mode, 
                        $options, 20);
            }
            
            /* handle the "popular" mode */
            if ($mode == TP_SRCH_POPULAR) {
            	
                $name = TP_SRCH_POPVAL;
                $inputBox = '<select name="' . $name . '">';
                $period = array("day", "week", "month");
                foreach ($period as $thisPeriod) {
                    $inputBox .= '<option value="' . $thisPeriod . '"';
                    if ($thisPeriod == $options->getValue(TP_SRCH_POPVAL)) {
                        $inputBox .= ' SELECTED';
                    }
                    $inputBox .= '>' . $thisPeriod . '</option>';
                }
                $inputBox .= '</select>';
            }

            print <<<EOT
                <tr>
                    <th style="font-weight: bold; font-size: 1em" valign="top">{$options->getDescription($mode)}</th>
                    <td>
                        <input type="radio" name="$radioName" id="$mode" value="$mode" $selected /> $inputBox
                        <br />{$options->getDescription($mode)}
                    </td>
                </tr>
EOT;
        }
   
        WordPressOptionsPage::printHTML_optionFooter();
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
        
            case TP_SRCH_CATEGORY:
            	$whichValue = TP_SRCH_CATVAL;
            	break;
        
            case TP_SRCH_FAV:
                $whichValue = TP_SRCH_FAVVAL;
                break;
        }
        return sprintf('<input type="text" name="%s" size="%s" value="%s" />',
        	$mode, $inputSize, $options->getValue($mode));
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
