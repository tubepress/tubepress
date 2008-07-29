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
 * Display options for the plugin
 *
 */
class TubePressDisplayOptions
{
    const CURRENT_PLAYER_NAME = "playerLocation";
    const DESC_LIMIT          = "descriptionLimit";
    const ORDER_BY            = "orderBy";
    const RELATIVE_DATES      = "relativeDates";
    const RESULTS_PER_PAGE    = "resultsPerPage";
    const THUMB_HEIGHT        = "thumbHeight";
    const THUMB_WIDTH         = "thumbWidth";
    
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
            TpMsg::_("options-category-title-" . $title));

        $class = new ReflectionClass("TubePressDisplayOptions");    

        /* go through each option in the category */
        foreach ($class->getConstants() as $constant) {
            $tpl->setVariable("OPTION_TITLE", 
                TpMsg::_(sprintf("options-%s-title-%s", $title, $constant)));
            $tpl->setVariable("OPTION_DESC", 
                TpMsg::_(sprintf("options-%s-desc-%s", $title, $constant)));
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
                    TpMsg::_("player-" . TubePressPlayer::NORMAL . "-desc") 
                        => TubePressPlayer::NORMAL,
                    TpMsg::_("player-" . TubePressPlayer::POPUP . "-desc")        
                        => TubePressPlayer::POPUP,
                    TpMsg::_("player-" . TubePressPlayer::YOUTUBE . "-desc")        
                        => TubePressPlayer::YOUTUBE,
                    TpMsg::_("player-" . TubePressPlayer::LIGHTWINDOW . "-desc")     
                        => TubePressPlayer::LIGHTWINDOW,
                    TpMsg::_("player-" . TubePressPlayer::GREYBOX . "-desc")        
                        => TubePressPlayer::GREYBOX,
                    TpMsg::_("player-" . TubePressPlayer::SHADOWBOX . "-desc")     
                        => TubePressPlayer::SHADOWBOX);

                TubePressOptionsForm::displayMenuInput($tpl, $constant, 
                    $values, $tpsm->get($constant));
                break;
            
            case TubePressDisplayOptions::ORDER_BY:
                $values = array(
                    TpMsg::_("order-relevance") => "relevance",
                    TpMsg::_("order-views") => "viewCount",
                    TpMsg::_("order-rating") => "rating",
                    TpMsg::_("order-updated") => "updated"
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