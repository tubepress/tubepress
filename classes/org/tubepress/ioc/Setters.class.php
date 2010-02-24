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
 * Setter/property names for IOC service. Cuts down on number of magic strings.
 */
class org_tubepress_ioc_Setters
{
    const BROWSER_DETECTOR  = 'browserDetector';
    const CACHE             = 'cacheService';
    const FEED_INSPECTION   = 'feedInspectionService';
    const FEED_RETRIEVAL    = 'feedRetrievalService';
    const INPUT_VALIDATION  = 'inputValidationService';
    const LOG               = 'log';
    const MESSAGE_SERVICE   = 'messageService';
    const OPTIONS_MANAGER   = 'optionsManager';
    const OPTIONS_REFERENCE = 'optionsReference';
    const TEMPLATE          = 'template';
    const PAGINATION        = 'paginationService';
    const PATH              = 'path';
    const PROVIDER          = 'videoProvider';
    const QUERYSTRING       = 'queryStringService';
    const STORAGE_MANAGER   = 'storageManager';
    const URL_BUILDER       = 'urlBuilder';
    const VIDEO_FACTORY     = 'videoFactory';
    const VIMEO_EMBED       = 'vimeoEmbeddedPlayerService';
    const VIMEO_FACTORY     = 'vimeoVideoFactory';
    const VIMEO_INSPECTION  = 'vimeoInspectionService';
    const VIMEO_URL_BUILDER = 'vimeoUrlBuilder';
    const YT_EMBED          = 'youTubeEmbeddedPlayerService';
    const YT_FACTORY        = 'youtubeVideoFactory';
    const YT_INSPECTION     = 'youtubeInspectionService';
    const YT_URL_BUILDER    = 'youtubeUrlBuilder';
}
