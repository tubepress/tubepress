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

tubepress_load_classes(array('org_tubepress_api_patterns_Strategy',
    'org_tubepress_api_template_Template',
    'org_tubepress_impl_log_Log',
    'org_tubepress_api_const_FilterExecutionPoint',
    'org_tubepress_api_provider_Provider',
    'org_tubepress_api_feed_FeedResult',
    'org_tubepress_api_patterns_FilterManager'));

class org_tubepress_impl_gallery_strategies_ThumbGalleryStrategy implements org_tubepress_api_patterns_Strategy
{
    const LOG_PREFIX = 'Thumb Gallery Strategy';
    
    private $_ioc;

    public function start()
    {
        $this->_ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();
    }

    public function stop()
    {
        unset($this->_ioc);
    }

    public function canHandle()
    {
        return true;
    }

    public function execute()
    {
        $qss       = $this->_ioc->get('org_tubepress_api_querystring_QueryStringService');
        $galleryId = $qss->getGalleryId($_GET);

        if ($galleryId == '') {
            $galleryId = mt_rand();
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Starting to build thumbnail gallery <tt>%s</tt>', $galleryId);

        return $this->_getHtml($galleryId);
    }

    /**
     * Generates the content of this gallery
     *
     * @param integer                      $galleryId The unique identifier of the gallery.
     *
     * @return The HTML content for this gallery
     */
    private function _getHtml($galleryId)
    {
        $provider      = $this->_ioc->get('org_tubepress_api_provider_Provider');
        $tpom          = $this->_ioc->get('org_tubepress_api_options_OptionsManager');
        $filterManager = $this->_ioc->get('org_tubepress_api_patterns_FilterManager');
        $themeHandler  = $this->_ioc->get('org_tubepress_api_theme_ThemeHandler');
        $template      = $themeHandler->getTemplateInstance('gallery.tpl.php');

        /* first grab the videos */
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Asking provider for videos');
        $feedResult = $provider->getMultipleVideos();
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Provider has delivered %d videos', sizeof($feedResult->getVideoArray()));

        /* send the videos through the filters */
        $filteredVideos = $filterManager->runFilters(org_tubepress_api_const_FilterExecutionPoint::VIDEOS_DELIVERY, $feedResult->getVideoArray(), $galleryId);

        /* modify the feed result to reflect the filtration (if any) of the videos */
        $feedResult->setEffectiveDisplayCount(abs($feedResult->getEffectiveDisplayCount() - abs(count($filteredVideos) - count($feedResult->getVideoArray()))));
        $feedResult->setVideoArray($filteredVideos);

        /* add some core template variables */
        $template->setVariable(org_tubepress_api_template_Template::VIDEO_ARRAY, $filteredVideos);
        $template->setVariable(org_tubepress_api_template_Template::GALLERY_ID, $galleryId);
        $template->setVariable(org_tubepress_api_template_Template::THUMBNAIL_WIDTH, $tpom->get(org_tubepress_api_const_options_Display::THUMB_WIDTH));
        $template->setVariable(org_tubepress_api_template_Template::THUMBNAIL_HEIGHT, $tpom->get(org_tubepress_api_const_options_Display::THUMB_HEIGHT));

        /* send the template through the filters */
        $filteredTemplate = $filterManager->runFilters(org_tubepress_api_const_FilterExecutionPoint::GALLERY_TEMPLATE, $template, $feedResult, $galleryId);

        /* send gallery HTML through the filters */
        $filteredHtml = $filterManager->runFilters(org_tubepress_api_const_FilterExecutionPoint::GALLERY_HTML, $filteredTemplate->toString(), $galleryId);

        /* we're done. tie up */
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Done assembling gallery <tt>%d</tt>', $galleryId);
        $tpom = $this->_ioc->get('org_tubepress_api_options_OptionsManager');
        $tpom->setCustomOptions(array());

        return $filteredHtml;
    }

}

?>
