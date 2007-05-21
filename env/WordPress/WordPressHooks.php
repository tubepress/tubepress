<?php
/**
 * WordPressHooks.php
 * 
 * The four (so far) hooks that we implement
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
    || require(ABSPATH . 
    "wp-content/plugins/tubepress/env/WordPress/WordPressOptionsPage.php");

    /**
     * This is the main method for the TubePress global options page,
     * which is loaded when you're in the wp-admin section of your blog
     */
    function tp_executeOptionsPage()
    {
    	if (function_exists('add_options_page')) {
			add_options_page(_tpMsg("OPTPANELTITLE"), _tpMsg("OPTPANELMENU"), 9, 
				'WordPressHooks.php', '_tp_executeOptionsPage');
    	}
    }
    function _tp_executeOptionsPage()
    {
    	/* initialize the database if we need to */
    	WordPressOptionsPackage::initDB();
    	
    	/* see what we've got in the db */
        $dbOptions = new WordPressOptionsPackage();
    
        if (PEAR::isError($dbOptions->error)) {
            $css = new TubePressCSS();
            WordPressOptionsPage::printStatusMsg($dbOptions->error->msg,
                $css->failure_class);
        }
    
        $pageTitle = _tpMsg("OPTPANELTITLE");
    
        /* are we updating? */
        if (isset($_POST['tubepress_save'])) {
            
            WordPressOptionsPage::update();
            
            $dbOptions = new WordPressOptionsPackage();
        }
    
        print <<<EOT
        <div class="wrap">
              <form method="post">
            <h2>$pageTitle</h2>Set default options for the plugin. 
            Each option here can be overridden 
            on any page that has your TubePress trigger tag.
            <br /><br />
EOT;
    
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
    
    /**
     * Spits out the CSS and JS files that we always need for TubePress
     */
    function tp_insertCSSJS()
    {
        $url = get_settings('siteurl') . "/wp-content/plugins/tubepress";
        print<<<GBS
            <script type="text/javascript" src="{$url}/tubepress.js"></script>
            <link rel="stylesheet" href="{$url}/tubepress.css" 
                type="text/css" />
GBS;
    }

    /**
     * Spits out the CSS and JS files that we need for LightWindow
     */
    function tp_insertLightWindow()
    {
        $url = get_settings('siteurl') .
            "/wp-content/plugins/tubepress/lib/lightWindow";
        print<<<GBS
            <script type="text/javascript" 
                src="{$url}/javascript/prototype.js"></script>
            <script type="text/javascript" 
                src="{$url}/javascript/effects.js"></script>
            <script type="text/javascript" 
                src="{$url}/javascript/lightWindow.js"></script>
            <link rel="stylesheet" 
                href="{$url}/css/lightWindow.css" 
                media="screen" type="text/css" />
GBS;
    }

    /**
     * Spits out the CSS and JS files that we need for ThickBox
     */
    function tp_insertThickBox()
    {
        $url = get_settings('siteurl') .
            "/wp-content/plugins/tubepress/lib/thickbox";
        print<<<GBS
            <script type="text/javascript" 
                src="{$url}/jquery.js"></script>
            <script type="text/javascript"
                src="{$url}/thickbox.js"></script>
            <link rel="stylesheet"
                href="{$url}/thickbox.css" media="screen" type="text/css" />
GBS;
    }
?>
