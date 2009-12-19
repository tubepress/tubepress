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
tubepress_load_classes(array('org_tubepress_browser_BrowserDetector'));

/**
 * TubePress template
 */
class org_tubepress_browser_BrowserDetectorImpl implements org_tubepress_browser_BrowserDetector
{
    public function detectBrowser($serverVars)
    {
        if (!is_array($serverVars) || !isset($serverVars['HTTP_USER_AGENT'])) {
            return org_tubepress_browser_BrowserDetector::UNKNOWN;
        }
        if (strstr($serverVars['HTTP_USER_AGENT'],'iPhone')) {
            return org_tubepress_browser_BrowserDetector::IPHONE;
        }
        if (strstr($serverVars['HTTP_USER_AGENT'],'iPod')) {
            return org_tubepress_browser_BrowserDetector::IPOD;
        }
        return org_tubepress_browser_BrowserDetector::UNKNOWN; 
    }
}
