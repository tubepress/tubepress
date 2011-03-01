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

tubepress_load_classes(array('org_tubepress_api_patterns_Strategy',
    'org_tubepress_api_const_template_Variable',
    'org_tubepress_impl_log_Log',
    'org_tubepress_api_const_filters_ExecutionPoint',
    'org_tubepress_api_provider_Provider',
    'org_tubepress_api_feed_FeedResult',
    'org_tubepress_api_patterns_FilterManager'));

class org_tubepress_impl_html_strategies_ThumbGalleryStrategy implements org_tubepress_api_patterns_Strategy
{
    const LOG_PREFIX = 'Thumb Gallery Strategy';

    private $_ioc;

    /**
     * Called *before* canHandle() and execute() to allow the strategy
     *  to initialize itself.
     *
     * @return void
     */
    public function start()
    {
        $this->_ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
    }

    /**
     * Called *after* canHandle() and execute() to allow the strategy
     *  to tear itself down.
     *
     * @return void
     */
    public function stop()
    {
        unset($this->_ioc);
    }

    /**
     * Returns true if this strategy is able to handle
     *  the request.
     *
     * @return boolean True if the strategy can handle the request, false otherwise.
     */
    public function canHandle()
    {
        return true;
    }

    /**
     * Execute the strategy.
     *
     * @return unknown The result of this strategy execution.
     */
    public function execute()
    {
        $qss       = $this->_ioc->get('org_tubepress_api_querystring_QueryStringService');
        $galleryId = $qss->getGalleryId($_GET);

        if ($galleryId == '') {
            $galleryId = mt_rand();
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Starting to build thumbnail gallery %s', $galleryId);

        return $this->_getHtml($galleryId);
    }

    /**
     * Generates the content of this gallery
     *
     * @param integer $galleryId The unique identifier of the gallery.
     *
     * @return The HTML content for this gallery
     */
    private function _getHtml($galleryId)
    {
        $provider      = $this->_ioc->get('org_tubepress_api_provider_Provider');
        $tpom          = $this->_ioc->get('org_tubepress_api_options_OptionsManager');
        $filterManager = $this->_ioc->get('org_tubepress_api_patterns_FilterManager');
        $themeHandler  = $this->_ioc->get('org_tubepress_api_theme_ThemeHandler');
        $ms            = $this->_ioc->get('org_tubepress_api_message_MessageService');
        $template      = $themeHandler->getTemplateInstance('gallery.tpl.php');

        /* first grab the videos */
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Asking provider for videos');
        $feedResult = $provider->getMultipleVideos();
        $numVideos  = sizeof($feedResult->getVideoArray());
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Provider has delivered %d videos', $numVideos);
        
        if ($numVideos == 0) {
            return $ms->_('no-videos-found');
        }

        /* send the feed result through the filters */
        $feedResult = $filterManager->runFilters(org_tubepress_api_const_filters_ExecutionPoint::VIDEOS_DELIVERY, $feedResult, $galleryId);

        /* add some core template variables */
        $template->setVariable(org_tubepress_api_const_template_Variable::VIDEO_ARRAY, $feedResult->getVideoArray());
        $template->setVariable(org_tubepress_api_const_template_Variable::GALLERY_ID, $galleryId);
        $template->setVariable(org_tubepress_api_const_template_Variable::THUMBNAIL_WIDTH, $tpom->get(org_tubepress_api_const_options_names_Display::THUMB_WIDTH));
        $template->setVariable(org_tubepress_api_const_template_Variable::THUMBNAIL_HEIGHT, $tpom->get(org_tubepress_api_const_options_names_Display::THUMB_HEIGHT));

        /* send the template through the filters */
        $filteredTemplate = $filterManager->runFilters(org_tubepress_api_const_filters_ExecutionPoint::GALLERY_TEMPLATE, $template, $feedResult, $galleryId);

        /* send gallery HTML through the filters */
        $filteredHtml = $filterManager->runFilters(org_tubepress_api_const_filters_ExecutionPoint::GALLERY_HTML, $filteredTemplate->toString(), $galleryId);

        /* we're done. tie up */
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Done assembling gallery %d', $galleryId);
        $tpom = $this->_ioc->get('org_tubepress_api_options_OptionsManager');
        $tpom->setCustomOptions(array());

        return $filteredHtml;
    }
}
