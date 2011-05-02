<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
 * Official filter points around the TubePress core.
 */
interface org_tubepress_api_const_plugin_FilterPoint
{
    /**
     * Filters the TubePress provider result.
     *
     * function getFilteredFeedResult(org_tubepress_api_provider_ProviderResult $rawFeedResult, $galleryId);
     */
    const PROVIDER_RESULT = 'providerResult';

    /**
     * Filters any HTML that TubePress generates.
     *
     * function getFilteredHtml($rawHtml);
     */
    const HTML_ANY = 'html';

    /**
     * function getFilteredGalleryHtml($rawHtml, $galleryId);
     */
    const HTML_GALLERY = 'galleryHtml';

    /**
     * function getFilteredSingleVideoHtml($rawHtml);
     */
    const HTML_SINGLEVIDEO = 'singleVideoHtml';

    /**
     * function getFilteredGalleryTemplate(org_tubepress_api_template_Template $template, org_tubepress_api_provider_ProviderResult $feedResult, $galleryId);
     */
    const TEMPLATE_GALLERY = 'galleryTemplate';

    /**
     * function getFilteredSingleVideoTemplate(org_tubepress_api_template_Template $rawSingleVideoTemplate);
     */
    const TEMPLATE_SINGLEVIDEO = 'singleVideoTemplate';
}

