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
 * Options for the embedded player
 *
 */
class org_tubepress_options_category_Embedded implements org_tubepress_options_category_Category
{
    const AUTOPLAY        = "autoplay";
    const BORDER          = "border";
    const EMBEDDED_HEIGHT = "embeddedHeight";
    const EMBEDDED_WIDTH  = "embeddedWidth";
    const FULLSCREEN      = "fullscreen";
    const GENIE           = "genie";
    const LOOP            = "loop";
    const PLAYER_COLOR    = "playerColor";
    const QUALITY         = "quality";
    const SHOW_RELATED    = "showRelated";
    
	private $_messageService;
    
	/**
	 * Set the message service
	 *
	 * @param org_tubepress_message_MessageService $messageService
	 * 
	 * @return void
	 */
    public function setMessageService(org_tubepress_message_MessageService $messageService)
    {
    	$this->_messageService = $messageService;
    }
    
    /**
     * Displays the embedded options for the options form
     *
     * @param net_php_pear_HTML_Template_IT        $tpl  The template to write to
     * @param org_tubepress_options_storage_StorageManager $tpsm The TubePress storage manager
     * 
     * @return void
     */
    public function printForOptionsForm(net_php_pear_HTML_Template_IT $tpl, 
        org_tubepress_options_storage_StorageManager $tpsm)
    {
        $title = "embedded";
        
        $tpl->setVariable("OPTION_CATEGORY_TITLE",
            $this->_messageService->_("options-category-title-" . $title));

        $class = new ReflectionClass("org_tubepress_options_category_Embedded");    

        /* go through each option in the category */
        foreach ($class->getConstants() as $constant) {
            $tpl->setVariable("OPTION_TITLE", 
                $this->_messageService->_(sprintf("options-%s-title-%s", 
                    $title, $constant)));
            $tpl->setVariable("OPTION_DESC", 
                $this->_messageService->_(sprintf("options-%s-desc-%s", 
                    $title, $constant)));
            $tpl->setVariable("OPTION_NAME", $constant);
            
            switch ($constant) {
                
            case org_tubepress_options_category_Embedded::AUTOPLAY:
            case org_tubepress_options_category_Embedded::BORDER:
            case org_tubepress_options_category_Embedded::FULLSCREEN:
            case org_tubepress_options_category_Embedded::GENIE:
            case org_tubepress_options_category_Embedded::LOOP:
            case org_tubepress_options_category_Embedded::SHOW_RELATED:
                org_tubepress_options_Form::displayBooleanInput($tpl, 
                    $constant, $tpsm->get($constant));
                break;
            
            case org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT:
            case org_tubepress_options_category_Embedded::EMBEDDED_WIDTH:
                org_tubepress_options_Form::displayTextInput($tpl, 
                    $constant, $tpsm->get($constant));
                break;
             
            case org_tubepress_options_category_Embedded::PLAYER_COLOR:
                $values = array(
                    $this->_messageService->_("color-normal")    => 
                        "/", 
                    $this->_messageService->_("color-darkgrey")  => 
                        "0x3a3a3a/0x999999",
                    $this->_messageService->_("color-darkblue")  => 
                        "0x2b405b/0x6b8ab6", 
                    $this->_messageService->_("color-lightblue") => 
                        "0x006699/0x54abd6",
                    $this->_messageService->_("color-green")     => 
                        "0x234900/0x4e9e00", 
                    $this->_messageService->_("color-orange")    => 
                        "0xe1600f/0xfebd01",
                    $this->_messageService->_("color-pink")      => 
                        "0xcc2550/0xe87a9f", 
                    $this->_messageService->_("color-purple")    => 
                        "0x402061/0x9461ca",
                    $this->_messageService->_("color-red")       => 
                        "0x5d1719/0xcd311b"
                );
                org_tubepress_options_Form::displayMenuInput($tpl, 
                    $constant, $values, $tpsm->get($constant));
                break;
                
            case org_tubepress_options_category_Embedded::QUALITY:
                $values = array(
                	$this->_messageService->_("quality-normal")  => "normal", 
                	$this->_messageService->_("quality-high")    => "high",
                	$this->_messageService->_("quality-higher")  => "higher", 
                	$this->_messageService->_("quality-highest") => "highest"
                );
                org_tubepress_options_Form::displayMenuInput($tpl,
                    $constant, $values, $tpsm->get($constant));
            }
            $tpl->parse("optionRow");
        }
        $tpl->parse("optionCategory");
    }
}
