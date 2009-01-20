<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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
class org_tubepress_options_category_Advanced implements org_tubepress_options_category_Category
{
    const DATEFORMAT    	= "dateFormat";
    const DEBUG_ON      	= "debugging_enabled";
    const FILTER        	= "filter_racy";
    const KEYWORD       	= "keyword";
    const RANDOM_THUMBS 	= "randomize_thumbnails";
    const CLIENT_KEY    	= "clientKey";
    const DEV_KEY       	= "developerKey";
    const CACHE_ENABLED		= "cacheEnabled";
    const NOFOLLOW_LINKS	= "nofollowLinks";
    
	private $_messageService;
    
    public function setMessageService(org_tubepress_message_MessageService $messageService)
    {
    	$this->_messageService = $messageService;
    }
    
    /**
     * Displays advanced options for the options form
     *
     * @param HTML_Template_IT        &$tpl The template to write to
     * @param org_tubepress_options_storage_StorageManager $tpsm The TubePress storage manager
     * 
     * @return void
     */
    public function printForOptionsForm(HTML_Template_IT $tpl, 
        org_tubepress_options_storage_StorageManager $tpsm)
    {
        $title = "advanced";
        
        $tpl->setVariable("OPTION_CATEGORY_TITLE",
            $this->_messageService->_("options-category-title-" . $title));

        $class = new ReflectionClass("org_tubepress_options_category_Advanced");    

        /* go through each option in the category */
        foreach ($class->getConstants() as $constant) {
            $tpl->setVariable("OPTION_TITLE", 
                $this->_messageService->_(sprintf("options-%s-title-%s", $title, $constant)));
            $tpl->setVariable("OPTION_DESC", 
                $this->_messageService->_(sprintf("options-%s-desc-%s", $title, $constant)));
            $tpl->setVariable("OPTION_NAME", $constant);
            
            switch ($constant) {
                
            case org_tubepress_options_category_Advanced::DATEFORMAT:
            case org_tubepress_options_category_Advanced::KEYWORD:
            case org_tubepress_options_category_Advanced::CLIENT_KEY:
            case org_tubepress_options_category_Advanced::DEV_KEY:
                org_tubepress_options_Form::displayTextInput($tpl, 
                    $constant, $tpsm->get($constant));
                break;
              
            case org_tubepress_options_category_Advanced::DEBUG_ON:
            case org_tubepress_options_category_Advanced::FILTER:
            case org_tubepress_options_category_Advanced::RANDOM_THUMBS:
            case org_tubepress_options_category_Advanced::CACHE_ENABLED:
            case org_tubepress_options_category_Advanced::NOFOLLOW_LINKS:
                org_tubepress_options_Form::displayBooleanInput($tpl, 
                    $constant, $tpsm->get($constant));
                break;
            }
            
            $tpl->parse("optionRow");
        }
        $tpl->parse("optionCategory");
    }
} 