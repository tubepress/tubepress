<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_impl_environment_SimpleEnvironmentDetector',
    'org_tubepress_impl_log_Log',
));

/**
 * Class that holds a reference to an IOC container.
 */
class org_tubepress_impl_ioc_IocContainer
{
    private static $_instance = null;

    /**
     * Get the singleton inversion of control container.
     *
     * @return org_tubepress_api_ioc_IocService The singleton IOC container.
     */
    public static function getInstance()
    {
        /* see if we already built one */
        if (isset(self::$_instance)) {
            return self::$_instance;
        }

        $detector = new org_tubepress_impl_environment_SimpleEnvironmentDetector();

        if ($detector->isPro()) {
            if ($detector->isWordPress()) {

                org_tubepress_impl_classloader_ClassLoader::loadClass('org_tubepress_impl_ioc_ProInWordPressIocService');
                self::$_instance = new org_tubepress_impl_ioc_ProInWordPressIocService();

            } else {

                org_tubepress_impl_classloader_ClassLoader::loadClass('org_tubepress_impl_ioc_ProIocService');
                self::$_instance = new org_tubepress_impl_ioc_ProIocService();
            }

        } else {

            org_tubepress_impl_classloader_ClassLoader::loadClass('org_tubepress_impl_ioc_FreeWordPressPluginIocService');
            self::$_instance = new org_tubepress_impl_ioc_FreeWordPressPluginIocService();
        }

        return self::$_instance;
    }

    /**
     * Set the IOC container. This should only be used during testing!
     *
     * @param org_tubepress_api_ioc_IocService $instance The IOC container
     *
     * @return void
     */
    public static function setInstance(org_tubepress_api_ioc_IocService $instance)
    {
        self::$_instance = $instance;
    }
}
