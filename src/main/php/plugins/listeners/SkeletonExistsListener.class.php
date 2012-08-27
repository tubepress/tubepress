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
    'org_tubepress_impl_env_wordpress_OptionsPage',
    'org_tubepress_impl_env_wordpress_Main',
    'org_tubepress_impl_env_wordpress_Widget',
));

/**
 * Loads up a skeleton "content" directory if it doesn't already exist.
 */
class org_tubepress_impl_plugin_listeners_SkeletonExistsListener
{
    /**
     * Perform boot procedure.
     *
     * @return void
     */
    public function on_boot()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $ed  = $ioc->get(org_tubepress_api_environment_Detector::_);
        $fse = $ioc->get(org_tubepress_api_filesystem_Explorer::_);

        if ($ed->isWordPress()) {
        	
	        /* add the content directory if it's not already there */
	        if (!is_dir(ABSPATH . 'wp-content/tubepress-content')) {
	        	
	        	$fse->copyDirectory($fse->getTubePressBaseInstallationPath() . '/sys/skel/tubepress-content', ABSPATH . 'wp-content');
	        }
	        
        } else {
        	
        	$basePath = $fse->getTubePressBaseInstallationPath();
        	
        	/* add the content directory if it's not already there */
        	if (!is_dir($basePath . '/tubepress-content')) {
        		
        		$fse->copyDirectory($basePath . '/sys/skel/tubepress-content', $basePath);
        	}
        }
    }
}
