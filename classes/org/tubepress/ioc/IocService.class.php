<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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
    const CACHE_SERVICE                 = 'cacheService';
    const FEED_INSPECTION_SERVICE       = 'feedInspectionService';
    const FEED_RETRIEVAL_SERVICE        = 'feedRetrievalService';
    const GALLERY                       = 'gallery';
    const GALLERY_TEMPLATE              = 'galleryTemplate';
    const HTML_WIDGET_TEMPLATE          = 'htmlWidgetTemplate';
    const LOG                           = 'log';
    const LONGTAIL_EMBEDDED_TEMPLATE    = 'longtailEmbeddedTemplate';
    const MESSAGE_SERVICE               = 'messageService';
    const OPTIONS_MANAGER               = 'optionsManager';
    const OPTIONS_FORM_CATEGORY_PRINTER = 'categoryPrinter';
    const OPTIONS_FORM_HANDLER          = 'formHandler';
    const OPTIONS_FORM_WIDGET_PRINTER   = 'widgetPrinter';
    const OPTIONS_REFERENCE             = 'optionsReference';
    const PAGINATION_SERVICE            = 'paginationService';
    const QUERY_STRING_SERVICE          = 'queryStringService';
    const SHORTCODE_SERVICE             = 'shortcodeService';
    const STORAGE_MANAGER               = 'storageManager';
    const THUMB                         = 'thumbnailService';
    const THUMB_TEMPLATE                = 'thumbTemplate';
    const URL_BUILDER                   = 'ulrBuilderService';
    const VALIDATION_SERVICE            = 'validationService';
    const VIDEO_FACTORY                 = 'videoFactory';
    const VIDEO_PROVIDER                = 'videoProvider';
    const WIDGET_GALLERY                = 'widgetGallery';
    const WIDGET_THUMBNAIL_SERVICE      = 'widgetThumbService';
    const W_TEMPLATE                    = 'widgetTemplate';
    const W_THUMB_TEMPLATE              = 'widgetThumbTemplate';
    const YOUTUBE_EMBEDDED_TEMPLATE     = 'youtubeEmbeddedTemplate';
    

    public function get($className);
    
    public function safeGet($firstChoice, $safeChoice);
}