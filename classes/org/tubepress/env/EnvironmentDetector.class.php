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
    || require dirname(__FILE__) . '/../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_ioc_IocService'));

/**
 * Detects TubePress's environment
 */
class org_tubepress_env_EnvironmentDetector
{
    /**
     * Detects if the user is running TubePress Pro.
     *
     * @return boolean True is the user is running TubePress Pro. False otherwise.
     */
    public static function isPro()
    {
        return class_exists('org_tubepress_ioc_ProIocService');
    }

    /**
     * Detects if the user is running within WordPress
     *
     * @return boolean True is the user is running within WordPress. False otherwise.
     */
    public static function isWordPress()
    {
        return strpos($_SERVER['PHP_SELF'], 'wp-content/plugins') !== false;
    }
}
