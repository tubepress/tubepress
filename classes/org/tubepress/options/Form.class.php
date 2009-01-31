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
class org_tubepress_options_Form
{
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
        $tpl = new net_php_pear_HTML_Template_IT(dirname(__FILE__) . "/../../../../ui/options_page");
        if (!$tpl->loadTemplatefile("options_page.tpl.html", true, true)) {
            throw new Exception("Could not load options page template");
        }
        
        $tpl->setVariable("PAGETITLE", $this->_messageService->_("options-page-title"));
        $tpl->setVariable("INTROTEXT", $this->_messageService->_("options-page-intro-text"));
        $tpl->setVariable("DONATION", $this->_messageService->_("options-page-donation"));
        $tpl->setVariable("SAVE", $this->_messageService->_("options-page-save-button"));

        foreach (org_tubepress_options_Form::_getCategoryInstances() as $category) {
            $category->printForOptionsForm($tpl, $tpsm);
        }
  
        print $tpl->get();
    }

    /**
     * Displays a drop-down menu
     *
     * @param net_php_pear_HTML_Template_IT &$tpl   The template to write to
     * @param string           $name   The name of the select input
     * @param array            $values The possible values for the drop-down
     * @param string           $value  The current value
     * 
     * @return void
     */
    public function displayMenuInput(net_php_pear_HTML_Template_IT &$tpl, 
        $name, $values, $value)
    {   
        $tpl->setVariable("OPTION_NAME", $name);
        foreach ($values as $validValueTitle => $validValue) {
            
            if ($validValue === $value) {
                $tpl->setVariable("OPTION_SELECTED", "SELECTED");
            }
            $tpl->setVariable("MENU_ITEM_TITLE", $validValueTitle);
            $tpl->setVariable("MENU_ITEM_VALUE", $validValue);
            $tpl->parse("menuItem");
        }
        $tpl->parse("menu");
    }
    
    /**
     * Displays a text input
     *
     * @param net_php_pear_HTML_Template_IT &$tpl  The template to write to
     * @param string           $name  The name of the input
     * @param string           $value The current value
     * 
     * @return void
     */
    public function displayTextInput(net_php_pear_HTML_Template_IT &$tpl, 
        $name, $value)
    {    
        $tpl->setVariable("OPTION_VALUE", $value);
        $tpl->setVariable("OPTION_NAME", $name);
        $tpl->parse("text");
    }
    
    /**
     * Displays a checkbox input
     *
     * @param net_php_pear_HTML_Template_IT &$tpl  The template to write to
     * @param string           $name  The name of the checkbox input
     * @param boolean          $value The current value
     * 
     * @return void
     */
    public function displayBooleanInput(net_php_pear_HTML_Template_IT &$tpl, 
        $name, $value)
    {    
        if ($value) {
            $tpl->setVariable("OPTION_SELECTED", "CHECKED");
        }
        $tpl->parse("checkbox");
    }
    
    /**
     * Displays a radio button and then an optional additional input
     *
     * @param net_php_pear_HTML_Template_IT &$tpl  The template to write to
     * @param string           $name  The name of the input
     * @param string           $value The current value
     * 
     * @return void
     */
    public function displayGalleryInput(net_php_pear_HTML_Template_IT &$tpl, 
        $name, $value)
    {    
        if ($name === $value) {
            $tpl->setVariable("OPTION_SELECTED", "CHECKED");
        }
        $tpl->parse("galleryType");
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
        
        $class = new ReflectionClass("org_tubepress_options_category_Meta");    
        $bools = $class->getConstants();
        $bools = array_merge($bools, array(
            org_tubepress_options_category_Advanced::DEBUG_ON,
            org_tubepress_options_category_YouTubeFeed::FILTER,
            org_tubepress_options_category_Advanced::RANDOM_THUMBS,
            org_tubepress_options_category_YouTubeFeed::CACHE_ENABLED,
            org_tubepress_options_category_YouTubeFeed::EMBEDDABLE_ONLY,
            org_tubepress_options_category_Advanced::NOFOLLOW_LINKS,
            org_tubepress_options_category_Display::RELATIVE_DATES,
            org_tubepress_options_category_Embedded::AUTOPLAY,
            org_tubepress_options_category_Embedded::BORDER,
            org_tubepress_options_category_Embedded::GENIE,
            org_tubepress_options_category_Embedded::LOOP,
            org_tubepress_options_category_Embedded::SHOW_RELATED,
            org_tubepress_options_category_Embedded::FULLSCREEN
        ));
        
        foreach ($bools as $bool) {
            if (array_key_exists($bool, $postVars)) {
                $tpsm->set($bool, true);
            } else {
                $tpsm->set($bool, false);
            }
        }
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    private function _getCategoryInstances()
    {
        $cats = array();
           
        $categories = array(
            'Gallery', 'Display', 'Embedded', 'Meta', 'YouTubeFeed', 'Advanced');
        
        foreach ($categories as $category) {
            $ref    = new ReflectionClass("org_tubepress_options_category_" . $category);
            $inst = $ref->newInstance();
            $inst->setMessageService($this->_messageService);
            $cats[] = $inst;
        }
        return $cats;
    }
    
    public function setMessageService(org_tubepress_message_MessageService $messageService) { $this->_messageService = $messageService; }
}
