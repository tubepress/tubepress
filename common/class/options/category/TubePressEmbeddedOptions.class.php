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
class TubePressEmbeddedOptions implements TubePressOptionsCategory
{
    const AUTOPLAY        = "autoplay";
    const BORDER          = "border";
    const EMBEDDED_HEIGHT = "embeddedHeight";
    const EMBEDDED_WIDTH  = "embeddedWidth";
    const GENIE           = "genie";
    const LOOP            = "loop";
    const PLAYER_COLOR    = "playerColor";
    const QUALITY         = "quality";
    const SHOW_RELATED    = "showRelated";
    
	private $_messageService;
    
	/**
	 * Set the message service
	 *
	 * @param TubePressMessageService $messageService
	 * 
	 * @return void
	 */
    public function setMessageService(TubePressMessageService $messageService)
    {
    	$this->_messageService = $messageService;
    }
    
    /**
     * Displays the embedded options for the options form
     *
     * @param HTML_Template_IT        $tpl  The template to write to
     * @param TubePressStorageManager $tpsm The TubePress storage manager
     * 
     * @return void
     */
    public function printForOptionsForm(HTML_Template_IT $tpl, 
        TubePressStorageManager $tpsm)
    {
        $title = "embedded";
        
        $tpl->setVariable("OPTION_CATEGORY_TITLE",
            $this->_messageService->_("options-category-title-" . $title));

        $class = new ReflectionClass("TubePressEmbeddedOptions");    

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
                
            case TubePressEmbeddedOptions::AUTOPLAY:
            case TubePressEmbeddedOptions::BORDER:
            case TubePressEmbeddedOptions::GENIE:
            case TubePressEmbeddedOptions::LOOP:
            case TubePressEmbeddedOptions::SHOW_RELATED:
                TubePressOptionsForm::displayBooleanInput($tpl, 
                    $constant, $tpsm->get($constant));
                break;
            
            case TubePressEmbeddedOptions::EMBEDDED_HEIGHT:
            case TubePressEmbeddedOptions::EMBEDDED_WIDTH:
                TubePressOptionsForm::displayTextInput($tpl, 
                    $constant, $tpsm->get($constant));
                break;
             
            case TubePressEmbeddedOptions::PLAYER_COLOR:
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
                TubePressOptionsForm::displayMenuInput($tpl, 
                    $constant, $values, $tpsm->get($constant));
                break;
                
            case TubePressEmbeddedOptions::QUALITY:
                $values = array(
                	$this->_messageService->_("quality-normal")  => "normal", 
                	$this->_messageService->_("quality-high")    => "high",
                	$this->_messageService->_("quality-higher")  => "higher", 
                	$this->_messageService->_("quality-highest") => "highest"
                );
                TubePressOptionsForm::displayMenuInput($tpl,
                    $constant, $values, $tpsm->get($constant));
            }
            $tpl->parse("optionRow");
        }
        $tpl->parse("optionCategory");
    }
}
