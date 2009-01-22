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
 * Display options for the plugin
 *
 */
class org_tubepress_options_category_Display implements org_tubepress_options_category_Category
{
    const CURRENT_PLAYER_NAME = "playerLocation";
    const DESC_LIMIT          = "descriptionLimit";
    const ORDER_BY            = "orderBy";
    const RELATIVE_DATES      = "relativeDates";
    const RESULTS_PER_PAGE    = "resultsPerPage";
    const THUMB_HEIGHT        = "thumbHeight";
    const THUMB_WIDTH         = "thumbWidth";
    
	private $_messageService;
    
    public function setMessageService(org_tubepress_message_MessageService $messageService)
    {
    	$this->_messageService = $messageService;
    }
    
    /**
     * Displays the display options for the options form
     *
     * @param net_php_pear_HTML_Template_IT        &$tpl The template to write to
     * @param org_tubepress_options_storage_StorageManager $tpsm The TubePress storage manager
     * 
     * @return void
     */
    public function printForOptionsForm(net_php_pear_HTML_Template_IT &$tpl, 
        org_tubepress_options_storage_StorageManager $tpsm)
    {
        $title = "display";
        
        $tpl->setVariable("OPTION_CATEGORY_TITLE",
            $this->_messageService->_("options-category-title-" . $title));

        $class = new ReflectionClass("org_tubepress_options_category_Display");    

        /* go through each option in the category */
        foreach ($class->getConstants() as $constant) {
            $tpl->setVariable("OPTION_TITLE", 
                $this->_messageService->_(sprintf("options-%s-title-%s", $title, $constant)));
            $tpl->setVariable("OPTION_DESC", 
                $this->_messageService->_(sprintf("options-%s-desc-%s", $title, $constant)));
            $tpl->setVariable("OPTION_NAME", $constant);
            
            switch ($constant) {
                
            case org_tubepress_options_category_Display::DESC_LIMIT;
            case org_tubepress_options_category_Display::RESULTS_PER_PAGE:
            case org_tubepress_options_category_Display::THUMB_HEIGHT:
            case org_tubepress_options_category_Display::THUMB_WIDTH:
                org_tubepress_options_Form::displayTextInput($tpl, 
                    $constant, $tpsm->get($constant));
                break;
            
            case org_tubepress_options_category_Display::CURRENT_PLAYER_NAME:
                $values = array(
                    $this->_messageService->_("player-" . org_tubepress_player_Player::NORMAL . "-desc") 
                        => org_tubepress_player_Player::NORMAL,
                    $this->_messageService->_("player-" . org_tubepress_player_Player::POPUP . "-desc")        
                        => org_tubepress_player_Player::POPUP,
                    $this->_messageService->_("player-" . org_tubepress_player_Player::YOUTUBE . "-desc")        
                        => org_tubepress_player_Player::YOUTUBE,
                    $this->_messageService->_("player-" . org_tubepress_player_Player::LIGHTWINDOW . "-desc")     
                        => org_tubepress_player_Player::LIGHTWINDOW,
                    $this->_messageService->_("player-" . org_tubepress_player_Player::GREYBOX . "-desc")        
                        => org_tubepress_player_Player::GREYBOX,
                    $this->_messageService->_("player-" . org_tubepress_player_Player::SHADOWBOX . "-desc")     
                        => org_tubepress_player_Player::SHADOWBOX);

                org_tubepress_options_Form::displayMenuInput($tpl, $constant, 
                    $values, $tpsm->get($constant));
                break;
            
            case org_tubepress_options_category_Display::ORDER_BY:
                $values = array(
                    $this->_messageService->_("order-relevance") => "relevance",
                    $this->_messageService->_("order-views") => "viewCount",
                    $this->_messageService->_("order-rating") => "rating",
                    $this->_messageService->_("order-published") => "published",
                    $this->_messageService->_("order-random") => "random"
                );
                org_tubepress_options_Form::displayMenuInput($tpl, 
                    $constant, $values, $tpsm->get($constant));
                break;
            
            case org_tubepress_options_category_Display::RELATIVE_DATES:
            	org_tubepress_options_Form::displayBooleanInput($tpl, $constant, $tpsm->get($constant));
            	break;
            }	
            	
            $tpl->parse("optionRow");
        }
        $tpl->parse("optionCategory");
    }
}