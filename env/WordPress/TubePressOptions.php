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
    
        $pageTitle = _tpMsg("OPTPANELTITLE");
    
        /* are we updating? */
        if (isset($_POST['tubepress_save'])) {
            
            WordPressOptionsPage::update();
            
            $dbOptions = new WordPressOptionsPackage();
        }
    
        printf('<div class="wrap"><form method="post"><h2>%s</h2><br/><br/>%s',
            $pageTitle, _tpMsg("OPTPAGEDESC"));
    
        WordPressOptionsPage::printHTML_modes($dbOptions);
        WordPressOptionsPage::printHTML_display($dbOptions);
        WordPressOptionsPage::printHTML_player($dbOptions);
        WordPressOptionsPage::printHTML_meta($dbOptions);
        WordPressOptionsPage::printHTML_advanced($dbOptions);
    
        $saveValue = _tpMsg("SAVE");
        
        print <<<EOT
            <input type="submit" name="tubepress_save" 
                value="$saveValue" />
              </form>
         </div>
EOT;
    
    }
?>
