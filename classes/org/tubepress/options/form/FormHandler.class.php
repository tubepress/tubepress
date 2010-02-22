<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_options_Category',
    'org_tubepress_options_Type',
    'org_tubepress_message_MessageService',
    'org_tubepress_options_reference_OptionsReference',
    'org_tubepress_options_storage_StorageManager',
    'org_tubepress_template_Template'));

/**
 * Displays a generic options form for TubePress
 *
 */
class org_tubepress_options_form_FormHandler
{
    private $_optionsReference;
    private $_messageService;
    private $_storageManager;
    private $_template;
    
    /**
     * Displays all the TubePress options in HTML
     *
     * @param org_tubepress_options_storage_StorageManager $tpsm The TubePress storage manager
     * 
     * @return void
     */
    public final function display()
    {   
        global $tubepress_base_url;
        
        /* set the surrounding text */
        $this->_template->setVariable(org_tubepress_template_Template::OPTIONS_PAGE_TITLE,      $this->_messageService->_('options-page-title'));
        $this->_template->setVariable(org_tubepress_template_Template::OPTIONS_PAGE_INTRO,      $this->_messageService->_('options-page-intro-text'));
        $this->_template->setVariable(org_tubepress_template_Template::OPTIONS_PAGE_DONATION,   $this->_messageService->_('options-page-donation'));
        $this->_template->setVariable(org_tubepress_template_Template::OPTIONS_PAGE_SAVE,       $this->_messageService->_('options-page-save-button'));
        $this->_template->setVariable(org_tubepress_template_Template::TUBEPRESS_BASE_URL,      $tubepress_base_url);

        $categories = array();
        
        /* now parse each option category */
        $optionCategoryNames = $this->_optionsReference->getOptionCategoryNames();
        foreach ($optionCategoryNames as $optionCategoryName) {

            /* don't display the widget options on this page */
            if (!$this->_optionsReference->isOptionCategoryApplicableToOptionsForm($optionCategoryName)) {
                continue;
            }
            
            $categories[$optionCategoryName] = $this->_createCategoryMetaArray($optionCategoryName);
        }
        $this->_template->setVariable(org_tubepress_template_Template::OPTIONS_PAGE_CATEGORIES, $categories);
        print $this->_template->toString();
    }
    
    /**
     * Updates options from a keyed array
     *
     * @param org_tubepress_options_storage_StorageManager $tpsm     The TubePress storage manager
     * @param array                                        $postVars The POST variables
     * 
     * @return void
     */
    public final function collect($postVars)
    {   
        /* this loop will collect everything except checkboxes */
        foreach ($postVars as $name => $value) {
            if ($this->_optionsReference->getType($name) === org_tubepress_options_Type::BOOL) {
                continue;
            }

            if ($this->_storageManager->exists($name)) {
                $this->_storageManager->set($name, $value);
            }
        }

        /* this loop will handle the checkboxes */
        $names = $this->_optionsReference->getAllOptionNames();
        foreach ($names as $name) {

            /* ignore non-bools */
            if ($this->_optionsReference->getType($name) != org_tubepress_options_Type::BOOL) {
                continue;
            }

            /* if the user checked the box, the option name will appear in the POST vars */
            $this->_storageManager->set($name, array_key_exists($name, $postVars));         
        }
    }
    
    private function _createCategoryMetaArray($optionCategoryName)
    {
        $results = array();
        $results[org_tubepress_template_Template::OPTIONS_PAGE_CATEGORY_TITLE] = $this->_messageService->_("options-category-title-$optionCategoryName");
        $results[org_tubepress_template_Template::OPTIONS_PAGE_CATEGORY_OPTIONS] = $optionCategoryName == org_tubepress_options_Category::GALLERY ?
            $this->_createCategoryMetaArrayForGalleryOptions() : $this->_createCategoryOptionsMetaArray($optionCategoryName);
        return $results;
    }
    
    private function _createCategoryOptionsMetaArray($optionCategoryName)
    {
        $optionNames = $this->_optionsReference->getOptionNamesForCategory($optionCategoryName);
        $optionsMetaArray = array();
        foreach ($optionNames as $optionName) {
            $metaArray = array();
            $metaArray[org_tubepress_template_Template::OPTIONS_PAGE_OPTIONS_TITLE]     = $this->_messageService->_("options-title-$optionName");
            $metaArray[org_tubepress_template_Template::OPTIONS_PAGE_OPTIONS_PRO_ONLY]  = $this->_optionsReference->isOptionProOnly($optionName) ? '*' : '';
            $metaArray[org_tubepress_template_Template::OPTIONS_PAGE_OPTIONS_WIDGET]    = $this->_getWidgetHtml($optionName);
            $metaArray[org_tubepress_template_Template::OPTIONS_PAGE_OPTIONS_DESC]      = $this->_messageService->_("options-desc-$optionName");
            $metaArray[org_tubepress_template_Template::OPTIONS_PAGE_YOUTUBE_OPTION]    = $this->_optionsReference->appliesToYouTube($optionName);
            $metaArray[org_tubepress_template_Template::OPTIONS_PAGE_VIMEO_OPTION]      = $this->_optionsReference->appliesToVimeo($optionName);
            
            $optionsMetaArray[] = $metaArray;
        }
        return $optionsMetaArray;
    }
    
    private function _createCategoryMetaArrayForGalleryOptions()
    {
        $modeNames = $this->_optionsReference->getValidEnumValues(org_tubepress_options_category_Gallery::MODE);
        $modesMetaArray = array();
        foreach ($modeNames as $modeName) {
            $modeMetaArray = array();
            $modeMetaArray[org_tubepress_template_Template::OPTIONS_PAGE_OPTIONS_TITLE] = $this->_messageService->_("options-title-$modeName");
            $modeMetaArray[org_tubepress_template_Template::OPTIONS_PAGE_OPTIONS_PRO_ONLY] = "";
            $html = $this->_getHtmlForRadio($modeName);
            if ($this->_optionsReference->isOptionName($modeName . 'Value')) {
                $newName = $modeName . 'Value';
                $html .= $this->_getWidgetHtml($newName);
            }
            $modeMetaArray[org_tubepress_template_Template::OPTIONS_PAGE_OPTIONS_WIDGET] = $html;
            $modeMetaArray[org_tubepress_template_Template::OPTIONS_PAGE_OPTIONS_DESC] = $this->_messageService->_("options-desc-$modeName");
            $modeMetaArray[org_tubepress_template_Template::OPTIONS_PAGE_YOUTUBE_OPTION]    = $this->_optionsReference->appliesToYouTube($modeName);
            $modeMetaArray[org_tubepress_template_Template::OPTIONS_PAGE_VIMEO_OPTION]      = $this->_optionsReference->appliesToVimeo($modeName);
            
            $modesMetaArray[] = $modeMetaArray;
        }
        return $modesMetaArray;            
    }
    
    private function _getWidgetHtml($optionName)
    {
        $type = $this->_optionsReference->getType($optionName);
        $value = $this->_storageManager->get($optionName);
        
        switch ($type) {
            case org_tubepress_options_Type::BOOL:
                $checked = $value ? 'CHECKED' : '';
                return "<input type=\"checkbox\" name=\"$optionName\" value=\"$optionName\" $checked />";
            case org_tubepress_options_Type::TEXT:
            case org_tubepress_options_Type::INTEGRAL:
                return "<input type=\"text\" name=\"$optionName\" size=\"20\" value=\"$value\" />";
            case org_tubepress_options_Type::COLOR:
                return "<input type=\"text\" name=\"$optionName\" size=\"6\" class=\"color\" value=\"$value\" />";
            case org_tubepress_options_Type::ORDER:
            case org_tubepress_options_Type::PLAYER:
            case org_tubepress_options_Type::TIME_FRAME:
            case org_tubepress_options_Type::SAFE_SEARCH:
            case org_tubepress_options_Type::PLAYER_IMPL:
                $validValues = $this->_optionsReference->getValidEnumValues($type);
                $result = "<select name=\"$optionName\">";
                
                foreach ($validValues as $validValue) {
                    $validValueTitle = $this->_messageService->_("$type-$validValue");
                    $selected = $validValue === $value ? 'SELECTED' : '';
                    $result .= "<option value=\"$validValue\" $selected>$validValueTitle</option>";
                }
                $result .= '</select>';
                return $result;    
        }
    }
    
    private function _getHtmlForRadio($optionName)
    {
        $value = $this->_storageManager->get(org_tubepress_options_category_Gallery::MODE);
        $checked = $optionName === $value ? 'CHECKED' : '';
        return "<input type=\"radio\" name=\"mode\" id=\"$optionName\" value=\"$optionName\" $checked />";
    }
    
    public function setMessageService(org_tubepress_message_MessageService $messageService) { $this->_messageService = $messageService; }
    public function setOptionsReference(org_tubepress_options_reference_OptionsReference $reference) { $this->_optionsReference = $reference; }
    public function setStorageManager(org_tubepress_options_storage_StorageManager $storageManager) { $this->_storageManager = $storageManager; }
    public function setTemplate(org_tubepress_template_Template $template) { $this->_template = $template; }
}
