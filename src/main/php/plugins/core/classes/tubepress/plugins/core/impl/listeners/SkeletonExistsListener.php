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
class tubepress_plugins_core_impl_listeners_SkeletonExistsListener
{
    public function onBoot(ehough_tickertape_api_Event $bootEvent)
    {
        $ed = tubepress_impl_patterns_ioc_KernelServiceLocator::getEnvironmentDetector();


        if ($ed->isWordPress()) {

            /* add the content directory if it's not already there */
            if (!is_dir(ABSPATH . 'wp-content/tubepress-content')) {

                $this->_tryToMirror(
                    TUBEPRESS_ROOT . '/src/main/resources/user-content-skeleton/tubepress-content',
                    ABSPATH . 'wp-content');
            }

        } else {

            $basePath = TUBEPRESS_ROOT;

            /* add the content directory if it's not already there */
            if (!is_dir($basePath . '/tubepress-content')) {

                $this->_tryToMirror(

                    $basePath . '/src/main/resources/user-content-skeleton/tubepress-content',
                    $basePath
                );
            }
        }
    }

    private function _tryToMirror($source, $dest)
    {
        $fs = tubepress_impl_patterns_ioc_KernelServiceLocator::getFileSystem();

        try {

            $fs->mirrorDirectoryPreventFileOverwrite($source, $dest);

        } catch (Exception $e) {

            //ignore
        }

    }
}