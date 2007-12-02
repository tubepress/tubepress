<?php
/**
 * TubePressOptions.php
 * 
 * Handles printing out the WordPress options page for TubePress
 * 
 * Copyright (C) 2007 Eric D. Hough (http://ehough.com)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class_exists("HTML_Template_IT")
    || require(dirname(__FILE__) . "/../../lib/PEAR/HTML/HTML_Template_IT/IT.php");
class_exists("PEAR")
    || require(dirname(__FILE__) . "/../../lib/PEAR/PEAR.php");
class_exists("WordPressOptionsPage")
    || require("WordPressOptionsPage.php");
    
    /**
     * This is the main method for the TubePress global options page,
     * which is loaded when you're in the wp-admin section of your blog.
     * It basically just loads _tp_executeOptionsPage()
     */
    function tp_executeOptionsPage()
    {
        if (function_exists('add_options_page')) {
            add_options_page("TubePress Options", "TubePress", 9, 
                'TubePressOptions.php', '_tp_executeOptionsPage');
        }
    }
    
    /**
     * The "real" works happens here
     */
    function _tp_executeOptionsPage()
    {
        /* initialize the database if we need to */
        TubePress_WordPress_Environment::initDB();

        /* see what we've got in the db */
        $stored = get_option("tubepress");
        if ($stored == NULL) {
                WordPressOptionsPage::printStatusMsg("Options did not store!",
                TP_CSS_FAILURE);
                return;
        }
    
        $tpl = new HTML_Template_IT(dirname(__FILE__) . "/../../common/templates");
        if (!$tpl->loadTemplatefile("options_page.tpl.html", true, true)) {
            WordPressOptionsPage::printStatusMsg($tpl->message,
                TP_CSS_FAILURE);
            return;
        }
        
        $tpl->setVariable('PAGETITLE', "blabla");
        $tpl->setVariable('INTROTEXT', "blablabla");
        $tpl->setVariable('SAVE', "blablablablablabla");
        
        /* are we updating? */
        if (isset($_POST['tubepress_save'])) {
            
            WordPressOptionsPage::update();
            
            $stored = get_option("tubepress");
            if ($stored == NULL) {
                WordPressOptionsPage::printStatusMsg("Options did not store!",
                TP_CSS_FAILURE);
            }
        
            $valid = $stored->checkValidity();
            if (PEAR::isError($valid)) {
                WordPressOptionsPage::printStatusMsg($valid->message,
                TP_CSS_FAILURE);
            }
        }
    
        WordPressOptionsPage::printHTML_modes($tpl, $stored);
        WordPressOptionsPage::printHTML_display($tpl, $stored);
        WordPressOptionsPage::printHTML_player($tpl, $stored);
        WordPressOptionsPage::printHTML_meta($tpl, $stored);
        WordPressOptionsPage::printHTML_advanced($tpl, $stored);

        $tpl->parse('main');
        $tpl->show();
    }
?>
