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

/**
 * Loads up a skeleton "content" directory if it doesn't already exist.
 */
class tubepress_plugins_core_listeners_SkeletonExistsListener
{
    public function onBoot(ehough_tickertape_api_Event $bootEvent)
    {
        $ed = tubepress_impl_patterns_ioc_KernelServiceLocator::getEnvironmentDetector();
        $fs = tubepress_impl_patterns_ioc_KernelServiceLocator::getFileSystem();

        if ($ed->isWordPress()) {
        	
	        /* add the content directory if it's not already there */
	        if (!is_dir(ABSPATH . 'wp-content/tubepress-content')) {
	        	
	        	$fs->mirrorDirectoryPreventFileOverwrite(

                    $ed->getTubePressBaseInstallationPath() . '/sys/skel/tubepress-content',
                    ABSPATH . 'wp-content'
                );
	        }
	        
        } else {
        	
        	$basePath = $ed->getTubePressBaseInstallationPath();
        	
        	/* add the content directory if it's not already there */
        	if (!is_dir($basePath . '/tubepress-content')) {
        		
        		$fs->mirrorDirectoryPreventFileOverwrite(

                    $basePath . '/sys/skel/tubepress-content',
                    $basePath
                );
        	}
        }
    }
}
