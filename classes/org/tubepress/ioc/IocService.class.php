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

/**
 * Dependency injector for TubePress
 */
interface org_tubepress_ioc_IocService
{
    const CACHE_SERVICE              = 'cacheService';
    
    const EMBEDDED_IMPL_LONGTAIL     = 'embeddedImplLongtail';
    const EMBEDDED_IMPL_VIMEO        = 'embeddedImplVimeo';
    const EMBEDDED_IMPL_YOUTUBE      = 'embeddedImplYouTube';
    
    const MESSAGE_SERVICE            = 'messageService';
    
    const OPTIONS_MANAGER            = 'optionsManager';
    const OPTIONS_STORAGE_MANAGER    = 'storageManager';
    
    const PAGINATION_SERVICE         = 'paginationService';
    
    const URL_BUILDER_YOUTUBE        = 'youtubeUrlBuilder';
    const URL_BUILDER_VIMEO          = 'vimeoUrlBuilder';
    
    const VIDEO_FACTORY_LOCAL        = 'localVideoFactory';
    const VIDEO_FACTORY_VIMEO        = 'vimeoVideoFactory';
    const VIDEO_FACTORY_YOUTUBE      = 'youtubeVideoFactory';
    
    const FEED_INSPECTION_LOCAL      = 'localFeedInspection';
    const FEED_INSPECTION_VIMEO      = 'vimeoFeedInspection';
    const FEED_INSPECTION_YOUTUBE    = 'youtubeFeedInspectionService';
    
    const FEED_RETRIEVAL_SERVICE     = 'feedRetrievalService';

    /**
     * Get an object from the container by name.
     *
     * @param string $className The name of the object to retrieve.
     *
     * @return object The object with the given name.
     */
    public function get($className);
}
