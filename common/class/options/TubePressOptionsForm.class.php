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
 * Displays a generic options form for TubePress
 *
 */
class TubePressOptionsForm
{
    
    public static final function display(TubePressStorageManager $tpsm)
    {
        /* load up the template */
        $tpl = new HTML_Template_IT(dirname(__FILE__) . "/../../ui");
        if (!$tpl->loadTemplatefile("options_page.tpl.html", true, true)) {
            throw new Exception("Could not load options page template");
        }
        
        $tpl->setVariable("PAGETITLE",  TpMsg::_("options-page-title"));
        $tpl->setVariable("INTROTEXT",  TpMsg::_("options-page-intro-text"));
        $tpl->setVariable("SAVE",         TpMsg::_("options-page-save-button"));

        foreach (TubePressOptionsForm::_getCategoryInstances() as $category) {
            $category->printForOptionsForm(&$tpl, $tpsm);
        }
  
        print $tpl->get();
    }

    public static function displayMenuInput(HTML_Template_IT &$tpl, $name, array $values, $value)
    {    
        foreach($values as $validValueTitle => $validValue) {
            
            if ($validValue === $value) {
                $tpl->setVariable("OPTION_SELECTED", "SELECTED");
            }
            $tpl->setVariable("MENU_ITEM_TITLE", $validValueTitle);
            $tpl->setVariable("MENU_ITEM_VALUE", $validValue);
            $tpl->parse("menuItem");
        }
        $tpl->parse("menu");
    }
    
    public static function displayTextInput(HTML_Template_IT &$tpl, $name, $value)
    {    
        $tpl->setVariable("OPTION_VALUE", $value);
        $tpl->parse("text");
    }
    
    public static function displayBooleanInput(HTML_Template_IT &$tpl, $name, $value)
    {    
        if ($value) {
            $tpl->setVariable("OPTION_SELECTED", "CHECKED");
        }
        $tpl->parse("checkbox");
    }
    
    public static function displayGalleryInput(HTML_Template_IT &$tpl, $name, $value)
    {    
        if ($name === $value) {
            $tpl->setVariable("OPTION_SELECTED", "CHECKED");
        }
        $tpl->parse("galleryType");
    }
    
    /**
     * Updates options from a keyed array
     *
     * @param TubePressStorage_v160 $stored
     * @param array $postVars
     */
    public final function collect(TubePressStorageManager $tpsm, 
        array $postVars)
    {    
        foreach ($postVars as $name => $value) {
            if ($tpsm->exists($name)) {
                $tpsm->set($name, $value);
            }
        }
        
        $class = new ReflectionClass("TubePressMetaOptions");    
        $bools = $class->getConstants();
        $bools = array_merge($bools, array(
            TubePressAdvancedOptions::DEBUG_ON,
            TubePressAdvancedOptions::FILTER,
            TubePressAdvancedOptions::RANDOM_THUMBS,
            TubePressEmbeddedOptions::AUTOPLAY,
            TubePressEmbeddedOptions::BORDER,
            TubePressEmbeddedOptions::GENIE,
            TubePressEmbeddedOptions::LOOP,
            TubePressEmbeddedOptions::SHOW_RELATED
        ));
        foreach ($bools as $bool) {
            if (array_key_exists($bool, $postVars)) {
                $tpsm->set($bool, true);
            } else {
                $tpsm->set($bool, false);
            }
        }
    }
    
    private static function _getCategoryInstances()
    {
        $cats = array();
           
        $categories = array(
            'Gallery', 'Display', 'Embedded', 'Meta', 'Advanced');
        
        foreach ($categories as $category) {
            $ref = new ReflectionClass("TubePress" . $category . "Options");
            $cats[] = $ref->newInstance();
        }
        return $cats;
    }
}
