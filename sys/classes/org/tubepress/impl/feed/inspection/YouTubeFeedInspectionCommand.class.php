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
 * Examines the feed from YouTube
 *
 */
class org_tubepress_impl_feed_inspection_YouTubeFeedInspectionCommand extends org_tubepress_impl_feed_inspection_AbstractFeedInspectionCommand
{
    const NS_OPENSEARCH = 'http://a9.com/-/spec/opensearch/1.1/';

    protected function _count($rawFeed)
    {
        $dom          = $this->_getDom($rawFeed);
        $totalResults = $dom->getElementsByTagNameNS(self::NS_OPENSEARCH, 'totalResults')->item(0)->nodeValue;

        self::_makeSureNumeric($totalResults);

        return $totalResults;
    }

    protected function _getNameOfHandledProvider()
    {
        return org_tubepress_api_provider_Provider::YOUTUBE;
    }

    private function _getDom($rawFeed)
    {
        if (!class_exists('DOMDocument')) {
            throw new Exception('DOMDocument class not found');
        }
        $dom = new DOMDocument();
        if ($dom->loadXML($rawFeed) === false) {
                throw new Exception('Problem parsing XML from YouTube');
        }
        return $dom;
    }

    private static function _makeSureNumeric($result)
    {
        if (is_numeric($result) === false) {
            throw new Exception("YouTube returned a non-numeric result count: $result");
        }
    }
}
