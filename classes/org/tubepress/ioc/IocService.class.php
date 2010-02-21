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
    const BROWSER_DETECTOR              = 'browserDetector';
    const CACHE_SERVICE                 = 'cacheService';
    const FEED_INSPECTION_SERVICE       = 'feedInspectionService';
    const FEED_RETRIEVAL_SERVICE        = 'feedRetrievalService';
    const GALLERY                       = 'gallery';
    const GALLERY_TEMPLATE              = 'galleryTemplate';
    const LOG                           = 'log';
    const LONGTAIL_EMBEDDED_TEMPLATE    = 'longtailEmbeddedTemplate';
    const MESSAGE_SERVICE               = 'messageService';
    const MODAL_PLAYER_TEMPLATE         = 'modalPlayerTemplate';
    const NORMAL_PLAYER_TEMPLATE        = 'normalPlayerTemplate';
    const OPTIONS_MANAGER               = 'optionsManager';
    const OPTIONS_FORM_HANDLER          = 'formHandler';
    const OPTIONS_REFERENCE             = 'optionsReference';
    const OPTIONS_FORM_TEMPLATE         = 'optionsFormTemplate';
    const PAGINATION_SERVICE            = 'paginationService';
    const QUERY_STRING_SERVICE          = 'queryStringService';
    const SHORTCODE_SERVICE             = 'shortcodeService';
    const SINGLE_VIDEO                  = 'singleVideo';
    const SINGLE_VIDEO_TEMPLATE         = 'singleVideoTemplate';
    const STORAGE_MANAGER               = 'storageManager';
    const THUMB_TEMPLATE                = 'thumbTemplate';
    const URL_BUILDER                   = 'ulrBuilderService';
    const VALIDATION_SERVICE            = 'validationService';
    const VIMEO_VIDEO_FACTORY           = 'vimeoVideoFactory';
    const VIMEO_EMBEDDED_TEMPLATE       = 'vimeoEmbeddedTemplate';
    const VIMEO_EMBEDDED_PLAYER         = 'vimeoEmbeddedPlayer';
    const VIMEO_FEED_INSPECTION         = 'vimeoFeedInspection';
    const VIMEO_URL_BUILDER             = 'vimeoUrlBuilder';
    const VIDEO_FACTORY                 = 'videoFactory';
    const VIDEO_PROVIDER                = 'videoProvider';
    const YOUTUBE_EMBEDDED_TEMPLATE     = 'youtubeEmbeddedTemplate';
    const YOUTUBE_EMBEDDED_PLAYER       = 'youtubeEmbeddedPlayerService';
    const YOUTUBE_FEED_INSPECTION       = 'youtubeFeedInspectionService';
    const YOUTUBE_URL_BUILDER           = 'youtubeUrlBuilder';
    const YOUTUBE_VIDEO_FACTORY         = 'youtubeVideoFactory';

    public function get($className);
    
    public function safeGet($firstChoice, $safeChoice);
}