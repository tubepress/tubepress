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
 * Performs various debugging functions
 *
 */
class TubePressDebug
{
    /**
     * Executes the debugging. Amazing!
     *
     * @param TubePressOptionsManager $tpom The TubePress options manager
     * @param TubePressStorageManager $tpsm The TubePress storage manager
     * 
     * @return void
     */
    public static final function execute(TubePressGallery $gallery, TubePressOptionsManager $tpom)
    {
    	global $tubepress_base_url;
    
        /* load up the debug template */
        $tpl = new HTML_Template_IT(dirname(__FILE__) . "/../../ui");
        if (!$tpl->loadTemplatefile("debug_blurb.tpl.html", true, true)) {
           print "Could not load debug template!";
           return;
        }

        $builder = new SimpleTubePressUrlBuilder();
        $builder->setOptionsManager($tpom);
        $builder->setQueryStringService(new SimpleTubePressQueryStringService());
        $debugStuff = array("tubepress_base_url" => $tubepress_base_url,
    	    "Gallery" => print_r($gallery, true),
            "YouTube connection test" => "Click <a href=\"" . $tubepress_base_url . 
                "/common/class/gdata/retrieval/TubePressConnectionTest.php\">here</a> to view results",
            "Request URL" => $builder->buildGalleryUrl());
        
        foreach ($debugStuff as $key => $val) {
        	$tpl->setVariable("ELEMENT_TITLE", $key);
        	$tpl->setVariable("ELEMENT_VALUE", $val);
        	$tpl->parse("debugElement");
        }
        
        $tpl->show();
    }
    
    /**
     * Determines if we are in debug mode
     *
     * @param TubePressOptionsManager $tpom The TubePress options manager
     * 
     * @return boolean True if we're in debug mode, false otherwise
     */
    public static final function areWeDebugging(TubePressOptionsManager $tpom)
    {
        $enabled = $tpom->get(TubePressAdvancedOptions::DEBUG_ON);
        return $enabled
            && isset($_GET['tubepress_debug'])
            && ($_GET['tubepress_debug'] == 'true');
    }
    
}