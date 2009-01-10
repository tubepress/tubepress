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
class TubePressDisplayOptions implements TubePressOptionsCategory
{
    const CURRENT_PLAYER_NAME = "playerLocation";
    const DESC_LIMIT          = "descriptionLimit";
    const ORDER_BY            = "orderBy";
    const RELATIVE_DATES      = "relativeDates";
    const RESULTS_PER_PAGE    = "resultsPerPage";
    const THUMB_HEIGHT        = "thumbHeight";
    const THUMB_WIDTH         = "thumbWidth";
    
	private $_messageService;
    
    public function setMessageService(TubePressMessageService $messageService)
    {
    	$this->_messageService = $messageService;
    }
    
    /**
     * Displays the display options for the options form
     *
     * @param HTML_Template_IT        &$tpl The template to write to
     * @param TubePressStorageManager $tpsm The TubePress storage manager
     * 
     * @return void
     */
    public function printForOptionsForm(HTML_Template_IT &$tpl, 
        TubePressStorageManager $tpsm)
    {
        $title = "display";
        
        $tpl->setVariable("OPTION_CATEGORY_TITLE",
            $this->_messageService->_("options-category-title-" . $title));

        $class = new ReflectionClass("TubePressDisplayOptions");    

        /* go through each option in the category */
        foreach ($class->getConstants() as $constant) {
            $tpl->setVariable("OPTION_TITLE", 
                $this->_messageService->_(sprintf("options-%s-title-%s", $title, $constant)));
            $tpl->setVariable("OPTION_DESC", 
                $this->_messageService->_(sprintf("options-%s-desc-%s", $title, $constant)));
            $tpl->setVariable("OPTION_NAME", $constant);
            
            switch ($constant) {
                
            case TubePressDisplayOptions::DESC_LIMIT;
            case TubePressDisplayOptions::RESULTS_PER_PAGE:
            case TubePressDisplayOptions::THUMB_HEIGHT:
            case TubePressDisplayOptions::THUMB_WIDTH:
                TubePressOptionsForm::displayTextInput($tpl, 
                    $constant, $tpsm->get($constant));
                break;
            
            case TubePressDisplayOptions::CURRENT_PLAYER_NAME:
                $values = array(
                    $this->_messageService->_("player-" . TubePressPlayer::NORMAL . "-desc") 
                        => TubePressPlayer::NORMAL,
                    $this->_messageService->_("player-" . TubePressPlayer::POPUP . "-desc")        
                        => TubePressPlayer::POPUP,
                    $this->_messageService->_("player-" . TubePressPlayer::YOUTUBE . "-desc")        
                        => TubePressPlayer::YOUTUBE,
                    $this->_messageService->_("player-" . TubePressPlayer::LIGHTWINDOW . "-desc")     
                        => TubePressPlayer::LIGHTWINDOW,
                    $this->_messageService->_("player-" . TubePressPlayer::GREYBOX . "-desc")        
                        => TubePressPlayer::GREYBOX,
                    $this->_messageService->_("player-" . TubePressPlayer::SHADOWBOX . "-desc")     
                        => TubePressPlayer::SHADOWBOX);

                TubePressOptionsForm::displayMenuInput($tpl, $constant, 
                    $values, $tpsm->get($constant));
                break;
            
            case TubePressDisplayOptions::ORDER_BY:
                $values = array(
                    $this->_messageService->_("order-relevance") => "relevance",
                    $this->_messageService->_("order-views") => "viewCount",
                    $this->_messageService->_("order-rating") => "rating",
                    $this->_messageService->_("order-published") => "published",
                    $this->_messageService->_("order-random") => "random"
                );
                TubePressOptionsForm::displayMenuInput($tpl, 
                    $constant, $values, $tpsm->get($constant));
                break;
            
            case TubePressDisplayOptions::RELATIVE_DATES:
            	TubePressOptionsForm::displayBooleanInput($tpl, $constant, $tpsm->get($constant));
            	break;
            }	
            	
            $tpl->parse("optionRow");
        }
        $tpl->parse("optionCategory");
    }
}