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
class org_tubepress_options_form_CategoryPrinter
{
    private $_tpl;
    private $_optionsReference;
    private $_messageService;
    private $_tpsm;
    private $_wp;
    
    public function getHtml($optionCategoryName, 
        org_tubepress_options_storage_StorageManager $tpsm,
        org_tubepress_message_MessageService $ms,
        org_tubepress_options_reference_OptionsReference $ref) {

        $this->_init($ref, $tpsm, $ms);
        
        $this->_loadTemplateFile($optionCategoryName);

        $this->_tpl->setVariable("OPTION_CATEGORY_TITLE", 
                $ms->_("options-category-title-$optionCategoryName"));

        $this->_parseOptionCategory($optionCategoryName);        
                
        return $this->_tpl->get();
    }

    private function _init($ref, $tpsm, $ms)
    {
        $this->_wp = new org_tubepress_options_form_WidgetPrinter();
        $this->_wp->setMessageService($ms);
        $this->_wp->setOptionsReference($ref);
        
        $this->_optionsReference = $ref;
        $this->_tpsm = $tpsm;
        $this->_messageService = $ms;
    }
    
    private function _parseOptionCategory($optionCategoryName)
    {
        switch ($optionCategoryName) {
            case org_tubepress_options_Category::META:
                $this->_parseMetaOptionsCategory();
                break;
            case org_tubepress_options_Category::GALLERY:
                $this->_parseGalleryOptionsCategory();
                break;
            default:
                $this->_parseRegularOptionsCategory($optionCategoryName);
        }
    }

    private function _parseGalleryOptionsCategory()
    {
        $modeNames = $this->_optionsReference->getValidEnumValues(org_tubepress_options_category_Gallery::MODE);
        foreach ($modeNames as $modeName) {
            
            $this->_tpl->setVariable("OPTION_TITLE",
	            $this->_messageService->_("options-title-$modeName"));
	        $this->_tpl->setVariable("OPTION_DESC",
	            $this->_messageService->_("options-desc-$modeName"));
	            
	        $html = $this->_wp->getHtmlForRadio($modeName, $this->_tpsm);
	        
	        if ($this->_optionsReference->isOptionName($modeName . "Value")) {
	            $newName = $modeName . "Value";
	            $html .= $this->_wp->getHtml($newName, $this->_tpsm);
	        }
		    $this->_tpl->setVariable("OPTION_WIDGET",
		        $html);
		    $this->_tpl->parse('optionRow');
        }        
    }
    
    private function _parseMetaOptionsCategory()
    {
	    $optionNames = $this->_optionsReference->getOptionNamesForCategory(org_tubepress_options_Category::META);
	    
	    $index = 1;
	    foreach ($optionNames as $optionName) {
	        
	        $this->_parseCommonOptionInfo($optionName);
		        
		    if ($index % 4 == 0) {
		        $this->_tpl->parse('optionRow');
		    } else {
		        $this->_tpl->parse('option');
		    }
		    $index++;
	    }
    }  
    
    private function _parseRegularOptionsCategory($optionCategoryName)
    {
	    $optionNames = $this->_optionsReference->getOptionNamesForCategory($optionCategoryName);
	    
	    foreach ($optionNames as $optionName) {
	        
	        $this->_parseCommonOptionInfo($optionName);
		    $this->_tpl->parse('optionRow');
	    }
    }    
    
    private function _parseCommonOptionInfo($optionName)
    {
        $this->_tpl->setVariable("OPTION_TITLE",
	            $this->_messageService->_("options-title-$optionName"));
	    $this->_tpl->setVariable("OPTION_DESC",
	            $this->_messageService->_("options-desc-$optionName"));
		$this->_tpl->setVariable("OPTION_WIDGET",
		        $this->_wp->getHtml($optionName, $this->_tpsm));
    }
    
    private function _loadTemplateFile($optionCategoryName)
    {
        $this->_tpl = new net_php_pear_HTML_Template_IT(dirname(__FILE__) . "/../../../../../ui/options_page/html_templates");
        
        $name = "options_group_regular";
        
        switch ($optionCategoryName) {
            case org_tubepress_options_Category::META:
               $name = "options_group_meta";
               break;
        }
        
        if (!$this->_tpl->loadTemplatefile("$name.tpl.html", true, true)) {
            throw new Exception("Could not load template for $optionCategoryName category");
        }
    }
}
