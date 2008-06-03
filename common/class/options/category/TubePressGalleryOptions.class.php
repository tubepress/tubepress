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
 * Options that let you choose which videos to show
 *
 */
class TubePressGalleryOptions
{
    const MODE = "mode";
    
    const FAVORITES_VALUE   = "favoritesValue";
    const MOST_VIEWED_VALUE = "most_viewedValue";
    const PLAYLIST_VALUE    = "playlistValue";
    const TAG_VALUE         = "tagValue";
    const TOP_RATED_VALUE   = "top_ratedValue";
    const USER_VALUE        = "userValue";
    
    /**
     * Displays the gallery options for the options form
     *
     * @param HTML_Template_IT        &$tpl The template to write to
     * @param TubePressStorageManager $tpsm The TubePress storage manager
     * 
     * @return void
     */
    public function printForOptionsForm(HTML_Template_IT &$tpl, 
        TubePressStorageManager $tpsm)
    {

        $title = "gallery";
        
        $tpl->setVariable("OPTION_CATEGORY_TITLE",
            TpMsg::_("options-category-title-" . $title));

        $class = new ReflectionClass("TubePressGallery");    

        /* go through each option in the category */
        foreach ($class->getConstants() as $constant) {
            $tpl->setVariable("OPTION_TITLE", 
                TpMsg::_(sprintf("options-%s-title-%s", $title, $constant)));
            $tpl->setVariable("OPTION_DESC", 
                TpMsg::_(sprintf("options-%s-desc-%s", $title, $constant)));
            $tpl->setVariable("OPTION_NAME", $constant);
            
            TubePressOptionsForm::displayGalleryInput($tpl, $constant, 
                $tpsm->get(TubePressGalleryOptions::MODE));

            switch ($constant) {
                
            case TubePressGallery::FAVORITES:
                TubePressOptionsForm::displayTextInput($tpl, 
                    TubePressGalleryOptions::FAVORITES_VALUE,
                    $tpsm->get(TubePressGalleryOptions::FAVORITES_VALUE));
                break;

            case TubePressGallery::PLAYLIST:
                TubePressOptionsForm::displayTextInput($tpl, 
                    TubePressGalleryOptions::PLAYLIST_VALUE,
                    $tpsm->get(TubePressGalleryOptions::PLAYLIST_VALUE));
                break;
            
            case TubePressGallery::TAG:
                TubePressOptionsForm::displayTextInput($tpl, 
                    TubePressGalleryOptions::TAG_VALUE,
                    $tpsm->get(TubePressGalleryOptions::TAG_VALUE));
                break;
            
            case TubePressGallery::USER:
                TubePressOptionsForm::displayTextInput($tpl, 
                    TubePressGalleryOptions::USER_VALUE,
                    $tpsm->get(TubePressGalleryOptions::USER_VALUE));
                break;
            
            case TubePressGallery::POPULAR:
            case TubePressGallery::TOP_RATED:
                $values = array(
                    TpMsg::_("timeframe-today")   => "today",
                    TpMsg::_("timeframe-week")    => "this_week",
                    TpMsg::_("timeframe-month")   => "this_month",
                    TpMsg::_("timeframe-alltime") => "all_time"
                );
                $tpl->setVariable("OPTION_NAME", $constant);
                TubePressOptionsForm::displayMenuInput($tpl, 
                    $constant . "Value",
                    $values, $tpsm->get($constant . "Value"));
            }
            $tpl->parse("optionRow");
        }
        $tpl->parse("optionCategory");
    }
}
