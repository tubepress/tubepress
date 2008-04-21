<?php
/**
 * Copyright 2006, 2007, 2008 Eric D. Hough (http://ehough.com)
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
class TubePressOptionsForm {
    
    public final function display(TubePressStorage_v160 $stored) {

        /* load up the template */
        $tpl = new HTML_Template_IT(dirname(__FILE__) . "/../../ui");
        if (!$tpl->loadTemplatefile("options_page.tpl.html", true, true)) {
            throw new Exception("Could not load options template");
        }
        
        $tpl->setVariable("PAGETITLE", "TubePress Options");
        $tpl->setVariable("INTROTEXT", "Set default options for the plugin. Each option here can be overridden on a per page/post basis. See the <a href=\"http://code.google.com/p/tubepress/wiki/Documentation\">documentation</a> for more info.");
        $tpl->setVariable("SAVE", "Save");
        
        /* go through each category */
        foreach ($stored->getOptionPackages() as $optionCategory) {
            $optionCategory->printForOptionsForm($tpl);
        }
  
        $tpl->parse("options");
        print $tpl->get();
    }
    
    /**
     * Updates options from a keyed array
     *
     * @param TubePressStorage_v160 $stored
     * @param array $postVars
     */
    public final function collect(TubePressStorage_v160 &$stored, array $postVars) {
    	
        /* go through each category */
    	$packages =& $stored->getOptionPackages();
        foreach ($packages as &$optionCategory) {
        	
        	$options =& $optionCategory->getOptions();
        	
        	/* update each option */
            foreach ($options as &$option) {
            	$value =& $option->getValue();
            	$value->updateFromOptionsPage($postVars);
            }
        }
    }
}
?>