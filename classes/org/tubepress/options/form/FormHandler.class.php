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
 * Displays a generic options form for TubePress
 *
 */
class org_tubepress_options_form_FormHandler
{
    private $_optionsReference;
	private $_messageService;
	
    /**
     * Displays all the TubePress options in HTML
     *
     * @param org_tubepress_options_storage_StorageManager $tpsm The TubePress storage manager
     * 
     * @return void
     */
    public final function display(org_tubepress_options_storage_StorageManager $tpsm)
    {
        /* load up the template */
        $tpl = new net_php_pear_HTML_Template_IT(dirname(__FILE__) . "/../../../../../ui/options_page/html_templates");
        if (!$tpl->loadTemplatefile("options_page.tpl.html", true, true)) {
            throw new Exception("Could not load options page template");
        }
        
        /* set the surrounding text */
        $tpl->setVariable("PAGETITLE", $this->_messageService->_("options-page-title"));
        $tpl->setVariable("INTROTEXT", $this->_messageService->_("options-page-intro-text"));
        $tpl->setVariable("DONATION",  $this->_messageService->_("options-page-donation"));
        $tpl->setVariable("SAVE",      $this->_messageService->_("options-page-save-button"));

        /* now parse each option category */
        $optionCategoryNames = $this->_optionsReference->getOptionCategoryNames();
        $categoryPrinter = new org_tubepress_options_form_CategoryPrinter();
        
        foreach ($optionCategoryNames as $optionCategoryName) {
            
            if ($optionCategoryName == org_tubepress_options_Category::WIDGET) {
                continue;
            }
            
            $categoryHtml = $categoryPrinter->getHtml($optionCategoryName, 
                $tpsm, $this->_messageService, $this->_optionsReference);
            
	        $tpl->setVariable("OPTION_CATEGORY", $categoryHtml);
	        $tpl->parse("optionCategory");
        }
  
        print $tpl->get();
    }
    
    /**
     * Updates options from a keyed array
     *
     * @param org_tubepress_options_storage_StorageManager $tpsm     The TubePress storage manager
     * @param array                   $postVars The POST variables
     * 
     * @return void
     */
    public final function collect(org_tubepress_options_storage_StorageManager $tpsm, 
        $postVars)
    {    
        foreach ($postVars as $name => $value) {
            if ($tpsm->exists($name)) {
                $tpsm->set($name, $value);
            }
        }

        $names = $this->_optionsReference->getAllOptionNames();
        foreach ($names as $name) {
            if ($this->_optionsReference->getType($name) != org_tubepress_options_Type::BOOL) {
                continue;
            }
            if (array_key_exists($name, $postVars)) {
                $tpsm->set($name, true);
            } else {
                $tpsm->set($name, false);
            }   
        }
    }
    
    public function setMessageService(org_tubepress_message_MessageService $messageService) { $this->_messageService = $messageService; }
    public function setOptionsReference(org_tubepress_options_reference_OptionsReference $reference) { $this->_optionsReference = $reference; }
}
