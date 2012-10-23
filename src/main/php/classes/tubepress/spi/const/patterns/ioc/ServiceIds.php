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
 * TubePressGallery() constants.
 */
class tubepress_spi_const_patterns_ioc_ServiceIds
{
    const AJAX_HANDLER                 = 'ajaxHandler';
    const CACHE                        = 'cacheService';
    const EMBEDDED_HTML_GENERATOR      = 'embeddedHtmlGenerator';
    const ENVIRONMENT_DETECTOR         = 'environmentDetector';
    const EVENT_DISPATCHER             = 'eventDispatcher';
    const EXECUTION_CONTEXT            = 'executionContext';
    const FEED_FETCHER                 = 'feedFetcher';
    const FILESYSTEM                   = 'fileSystem';
    const FILESYSTEM_FINDER_FACTORY    = 'fileSystemFinderFactory';
    const HEAD_HTML_GENERATOR          = 'headHtmlGenerator';
    const HTTP_CLIENT                  = 'httpClient';
    const HTTP_RESPONSE_HANDLER        = 'httpResponseHandler';
    const HTTP_REQUEST_PARAMS          = 'httpRequestParameterService';
    const MESSAGE                      = 'messageService';
    const OPTION_DESCRIPTOR_REFERENCE  = 'optionDescriptorReference';
    const OPTION_STORAGE_MANAGER       = 'optionStorageManager';
    const OPTION_VALIDATOR             = 'optionValidator';
    const OPTIONS_UI_FIELDBUILDER      = 'optionsUiFieldBuilder';
    const OPTIONS_UI_FORMHANDLER       = 'optionsUiFormHandler';
    const PLAYER_HTML_GENERATOR        = 'playerHtmlGenerator';
    const PLUGIN_DISCOVER              = 'pluginDiscoverer';
    const PLUGIN_REGISTRY              = 'pluginRegistry';
    const QUERY_STRING_SERVICE         = 'queryStringService';
    const SERVICE_COLLECTIONS_REGISTRY = 'serviceCollectionsRegistry';
    const SHORTCODE_HTML_GENERATOR     = 'shortcodeHtmlGenerator';
    const SHORTCODE_PARSER             = 'shortcodeParser';
    const TEMPLATE_BUILDER             = 'templateBuilder';
    const THEME_HANDLER                = 'themeHandler';
    const VIDEO_COLLECTOR              = 'videoCollector';
}
