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
    const CACHE       = 'cacheService';
    const CAT_PRINTER = 'categoryPrinter';
    const EMBED       = 'embeddedPlayerService';
    const FEED_INSP   = 'feedInspectionService';
    const FEED_RET    = 'feedRetrievalService';
    const FORM_HNDLER = 'formHandler';
    const GALLERY     = 'gallery';
    const MESSAGE     = 'messageService';
    const OPTIONS_MGR = 'optionsManager';
    const PAGINATION  = 'paginationService';
    const PLAYER_FACT = 'playerFactory';
    const QUERY_STR   = 'queryStringService';
    const REFERENCE   = 'optionsReference';
    const SHORTCODE   = 'shortcodeService';
    const STORAGE     = 'storageManager';
    const THUMB       = 'thumbnailService';
    const URL_BUILDER = 'ulrBuilderService';
    const VALIDATION  = 'validationService';
    const VID_FACT    = 'videoFactory';
    const WIDGET_GALL = 'widgetGallery';
    const W_PRINTER   = 'widgetPrinter';

    public function get($className);
}
?>
