<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_options_manager_OptionsManager',
	'org_tubepress_ioc_IocService'));

/**
 * Handles some tasks related to TubePress shortcodes
 */
interface org_tubepress_shortcode_ShortcodeService
{
   /**
     * This function is used to parse a shortcode into options that TubePress can use.
     *
     * @param string                  $content The haystack in which to search
     * @param org_tubepress_options_manager_OptionsManager &$tpom   The TubePress options manager
     * 
     * @return void
     */
    public function parse($content, org_tubepress_options_manager_OptionsManager $tpom);

    public function somethingToParse($content, $trigger = "tubepress");
    
    public function getHtml($shortCodeContent, org_tubepress_ioc_IocService $iocService);
}

