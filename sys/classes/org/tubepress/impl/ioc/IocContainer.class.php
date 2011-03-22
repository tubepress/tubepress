<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
tubepress_load_classes(array('org_tubepress_impl_environment_SimpleEnvironmentDetector',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_impl_log_Log'));

/**
 * Class that holds a reference to an IOC container.
 */
class org_tubepress_impl_ioc_IocContainer
{
    private static $_instance = null;
    
    public static function getInstance()
    {
        /* see if we already built one */
        if (isset(self::$_instance)) {
            return self::$_instance;        
        }
        
        $detector = new org_tubepress_impl_environment_SimpleEnvironmentDetector();
        
        if ($detector->isPro()) {
            if ($detector->isWordPress()) {
                
                tubepress_classloader('org_tubepress_impl_ioc_ProInWordPressIocService');
                self::$_instance = new org_tubepress_impl_ioc_ProInWordPressIocService();
                
            } else {
                
                tubepress_classloader('org_tubepress_impl_ioc_ProIocService');
                self::$_instance = new org_tubepress_impl_ioc_ProIocService();
            }
            
        } else {
            
            tubepress_classloader('org_tubepress_impl_ioc_FreeWordPressPluginIocService');
            self::$_instance = new org_tubepress_impl_ioc_FreeWordPressPluginIocService();
        }
        
        return self::$_instance;
    }
    
    public static function setInstance(org_tubepress_api_ioc_IocService $instance)
    {
        self::$_instance = $instance;
    }
}
