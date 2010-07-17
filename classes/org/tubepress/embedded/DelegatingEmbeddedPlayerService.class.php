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
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array(
    'org_tubepress_embedded_impl_AbstractEmbeddedPlayerService',
    'org_tubepress_video_feed_provider_Provider'));

/**
 * An HTML-embeddable player
 *
 */
class org_tubepress_embedded_DelegatingEmbeddedPlayerService
{
    private static $_providerToBeanNameMap = array(
        org_tubepress_video_feed_provider_Provider::VIMEO => org_tubepress_ioc_IocService::VIMEO_EMBEDDED_PLAYER,
        org_tubepress_video_feed_provider_Provider::DIRECTORY => org_tubepress_ioc_IocService::LONGTAIL_EMBEDDED_PLAYER
    );
    
    private static $_defaultDelegateBeanName = org_tubepress_ioc_IocService::YOUTUBE_EMBEDDED_PLAYER;
    
    /**
     * Spits back the text for this embedded player
     *
     * @param string $videoId The video ID to display
     *
     * @return string The text for this embedded player
     */
    public static function toString(org_tubepress_ioc_IocService $ioc, $videoId)
    {
        return org_tubepress_ioc_DelegateUtils::getDelegate($ioc,
           self::$_providerToBeanNameMap,
           self::$_defaultDelegateBeanName)->toString($ioc, $videoId);
    }
}
