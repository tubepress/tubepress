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
            add_options_page(_tpMsg("OPTPANELTITLE"), _tpMsg("OPTPANELMENU"), 9, 
                'TubePressOptions.php', '_tp_executeOptionsPage');
        }
    }
    
    /**
     * The "real" works happens here
     */
    function _tp_executeOptionsPage()
    {
        /* initialize the database if we need to */
        WordPressOptionsPackage::initDB();

        /* see what we've got in the db */
        $dbOptions = new WordPressOptionsPackage();
    
        /* any db failures? */
        if (PEAR::isError($dbOptions->checkValidity())) {
            WordPressOptionsPage::printStatusMsg($dbOptions->error->msg,
                TP_CSS_FAILURE);
        }
    
        $tpl = new HTML_Template_IT(dirname(__FILE__) . "/../../common/templates");
        $tpl->loadTemplatefile("options_page.tpl.html", true, true);
        if (PEAR::isError($tpl)) {
        	return $tpl;
        }
        
        $tpl->setCurrentBlock("main");
        $tpl->setVariable('TITLE', _tpMsg("OPTPANELTITLE"));
    	$tpl->setVariable('INTROTEXT', _tpMsg("OPTPAGEDESC"));
        $tpl->setVariable('SAVE', _tpMsg("SAVE"));
    	
        /* are we updating? */
        if (isset($_POST['tubepress_save'])) {
            
            WordPressOptionsPage::update();
            
            $dbOptions = new WordPressOptionsPackage();
        }
    
        WordPressOptionsPage::printHTML_modes($tpl, $dbOptions);
        WordPressOptionsPage::printHTML_display($tpl, $dbOptions);
        WordPressOptionsPage::printHTML_player($tpl, $dbOptions);
        WordPressOptionsPage::printHTML_meta($tpl, $dbOptions);
        WordPressOptionsPage::printHTML_advanced($tpl, $dbOptions);

        $tpl->parse('main');
        $tpl->show();
    }
?>
