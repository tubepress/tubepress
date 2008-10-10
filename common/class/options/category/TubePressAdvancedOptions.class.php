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

/**
 * Advanced options for the plugin
 *
 */
class TubePressAdvancedOptions
{
	
    const DATEFORMAT    = "dateFormat";
    const DEBUG_ON      = "debugging_enabled";
    const FILTER        = "filter_racy";
    const KEYWORD       = "keyword";
    const RANDOM_THUMBS = "randomize_thumbnails";
    const CLIENT_KEY    = "clientKey";
    const DEV_KEY       = "developerKey";
    const CACHE_ENABLED	= "cacheEnabled";
    
    /**
     * Displays advanced options for the options form
     *
     * @param HTML_Template_IT        &$tpl The template to write to
     * @param TubePressStorageManager $tpsm The TubePress storage manager
     * 
     * @return void
     */
    public function printForOptionsForm(HTML_Template_IT &$tpl, 
        TubePressStorageManager $tpsm)
    {
        $title = "advanced";
        
        $tpl->setVariable("OPTION_CATEGORY_TITLE",
            TpMsg::_("options-category-title-" . $title));

        $class = new ReflectionClass("TubePressAdvancedOptions");    

        /* go through each option in the category */
        foreach ($class->getConstants() as $constant) {
            $tpl->setVariable("OPTION_TITLE", 
                TpMsg::_(sprintf("options-%s-title-%s", $title, $constant)));
            $tpl->setVariable("OPTION_DESC", 
                TpMsg::_(sprintf("options-%s-desc-%s", $title, $constant)));
            $tpl->setVariable("OPTION_NAME", $constant);
            
            switch ($constant) {
                
            case TubePressAdvancedOptions::DATEFORMAT:
            case TubePressAdvancedOptions::KEYWORD:
            case TubePressAdvancedOptions::CLIENT_KEY:
            case TubePressAdvancedOptions::DEV_KEY:
                TubePressOptionsForm::displayTextInput($tpl, 
                    $constant, $tpsm->get($constant));
                break;
              
            case TubePressAdvancedOptions::DEBUG_ON:
            case TubePressAdvancedOptions::FILTER:
            case TubePressAdvancedOptions::RANDOM_THUMBS:
            case TubePressAdvancedOptions::CACHE_ENABLED:
                TubePressOptionsForm::displayBooleanInput($tpl, 
                    $constant, $tpsm->get($constant));
                break;
            }
            
            $tpl->parse("optionRow");
        }
        $tpl->parse("optionCategory");
    }
} 