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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../impl/classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_impl_feed_inspection_AbstractFeedInspectionCommand'
));

/**
 * Examines the feed from Vimeo
 */
class org_tubepress_impl_feed_inspection_VimeoFeedInspectionCommand extends org_tubepress_impl_feed_inspection_AbstractFeedInspectionCommand
{
    protected function _getNameOfHandledProvider()
    {
        return org_tubepress_api_provider_Provider::VIMEO;
    }

    protected function _count($rawFeed)
    {
        $feed = @unserialize($rawFeed);

        if ($feed === false) {

            throw new Exception('Could not unserialize data from Vimeo');
        }

        if (isset($feed->stat) && $feed->stat === 'fail') {

            $message = isset($feed->err->expl) ? $feed->err->expl : $feed->err->msg;

            throw new Exception('Problem communicating with Vimeo: ' . $message);
        }

        return isset($feed->videos->total) ? $feed->videos->total : 0;
    }

}
