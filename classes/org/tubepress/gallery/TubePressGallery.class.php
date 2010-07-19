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
    || require dirname(__FILE__) . '/../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_ioc_IocService',
    'org_tubepress_browser_BrowserDetector',
    'org_tubepress_shortcode_ShortcodeParser',
    'org_tubepress_options_category_Gallery',
    'org_tubepress_log_Log',
    'org_tubepress_player_Player',
    'org_tubepress_querystring_QueryStringService',
    'org_tubepress_video_feed_provider_Provider',
    'org_tubepress_options_Category'));

/**
 * TubePress gallery. This class gets one or more videos from a provider and applies them to the template.
 */
class org_tubepress_gallery_TubePressGallery
{
    const LOG_PREFIX = 'Gallery';

    const DIRECTORY        = 'directory';
    const FAVORITES        = 'favorites';
    const FEATURED         = 'recently_featured';
    const MOBILE           = 'mobile';
    const MOST_DISCUSSED   = 'most_discussed';
    const MOST_LINKED      = 'most_linked';
    const MOST_RECENT      = 'most_recent';
    const MOST_RESPONDED   = 'most_responded';
    const PLAYLIST         = 'playlist';
    const POPULAR          = 'most_viewed';
    const TAG              = 'tag';
    const TOP_RATED        = 'top_rated';
    const USER             = 'user';
    const VIMEO_UPLOADEDBY = 'vimeoUploadedBy';
    const VIMEO_LIKES      = 'vimeoLikes';
    const VIMEO_APPEARS_IN = 'vimeoAppearsIn';
    const VIMEO_SEARCH     = 'vimeoSearch';
    const VIMEO_CREDITED   = 'vimeoCreditedTo';
    const VIMEO_CHANNEL    = 'vimeoChannel';
    const VIMEO_ALBUM      = 'vimeoAlbum';
    const VIMEO_GROUP      = 'vimeoGroup';

    /**
     * Generates the HTML for TubePress. Could be a gallery or single video.
     *
     * @param org_tubepress_ioc_IocService $iocService       The IOC container.
     * @param string                       $shortCodeContent The optional shortcode content
     *
     * @return The HTML for TubePress.
     */
    public static function getHtml(org_tubepress_ioc_IocService $iocService, $shortCodeContent = '')
    {
        $tpom = $iocService->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);

        /* parse the shortcode if we need to */
        if ($shortCodeContent != '') {
            org_tubepress_shortcode_ShortcodeParser::parse($shortCodeContent, $iocService);
        }

        /* user wants to display a single video with meta info */
        $videoId = $tpom->get(org_tubepress_options_category_Gallery::VIDEO);
        if ($videoId != '') {

            org_tubepress_log_Log::log(self::LOG_PREFIX, 'Building single video with ID %s', $videoId);

            $singleVideoGenerator = $iocService->get(org_tubepress_ioc_IocService::SINGLE_VIDEO);

            return $singleVideoGenerator->getSingleVideoHtml($videoId);
        }
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'No video ID set in shortcode.');

        /* see if the users wants to display just the video in the query string */
        $playerName = $tpom->get(org_tubepress_options_category_Display::CURRENT_PLAYER_NAME);

        if ($playerName == org_tubepress_player_Player::SOLO) {

            org_tubepress_log_Log::log(self::LOG_PREFIX, 'Solo player detected. Checking query string for video ID');

            $videoId = org_tubepress_querystring_QueryStringService::getCustomVideo($_GET);

            if ($videoId != '') {
                org_tubepress_log_Log::log(self::LOG_PREFIX, 'Building single video with ID %s', $videoId);

                $singleVideoGenerator = $iocService->get(org_tubepress_ioc_IocService::SINGLE_VIDEO);

                return $singleVideoGenerator->getSingleVideoHtml($videoId);
            }

            org_tubepress_log_Log::log(self::LOG_PREFIX, 'Solo player in use, but no video ID set in URL. Will display a gallery instead.', $videoId);
        }

        $galleryId = org_tubepress_querystring_QueryStringService::getGalleryId($_GET);

        if ($galleryId == '') {
            $galleryId = mt_rand();
        }

        /* normal gallery */
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Starting to build thumbnail gallery %s', $galleryId);
        return self::_getThumbnailGallery($galleryId, $iocService);
    }

    /**
     * Generates the HTML for a gallery with the given ID.
     *
     * @param integer                      $galleryId The unique identifier of the gallery.
     * @param org_tubepress_ioc_IocService $ioc       The IOC container
     *
     * @return string The HTML contents of the gallery/video.
     */
    private static function _getThumbnailGallery($galleryId, org_tubepress_ioc_IocService $ioc)
    {
        try {
            return self::_getHtml($galleryId, $ioc);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Generates the content of this gallery
     * 
     * @param integer                      $galleryId The unique identifier of the gallery.
     * @param org_tubepress_ioc_IocService $ioc       The IOC container
     *
     * @return The HTML content for this gallery
     */
    private static function _getHtml($galleryId, org_tubepress_ioc_IocService $ioc)
    {
        /* first grab the videos */
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Asking provider for videos');
        $feedResult = org_tubepress_video_feed_provider_Provider::getFeedResult($ioc);

        /* prep template */
        $template = self::_getTemplate($ioc);
        self::_prepTemplate($feedResult, $galleryId, $template, $ioc);

        /* we're done. tie up */
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Done assembling gallery %d', $galleryId);
        return $template->toString();
    }

    private static function _getTemplate(org_tubepress_ioc_IocService $ioc)
    {
        $browser = org_tubepress_browser_BrowserDetector::detectBrowser($_SERVER);

        if ($browser === org_tubepress_browser_BrowserDetector::IPHONE || $browser === org_tubepress_browser_BrowserDetector::IPOD) {
            org_tubepress_log_Log::log($this->_logPrefix, 'iPhone/iPod detected.');
            $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
            $tpom->set(org_tubepress_options_category_Display::THEME, 'iphone');
        }
        return org_tubepress_theme_Theme::getTemplateInstance($ioc, 'gallery.tpl.php');
    }

    private static function _prepTemplate(org_tubepress_video_feed_FeedResult $feedResult, $galleryId,
        org_tubepress_template_Template $template, org_tubepress_ioc_IocService $ioc)
    {
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);

        /* first apply the video array */
        $videos = $feedResult->getVideoArray();
        if (is_array($videos) && sizeof($videos) > 0) {

            $videos = self::_prependVideoIfNeeded($videos, $ioc);

            $template->setVariable(org_tubepress_template_Template::VIDEO_ARRAY, $videos);

            $paginationService = $ioc->get(org_tubepress_ioc_IocService::PAGINATION_SERVICE);
            $pagination        = $paginationService->getHtml($feedResult->getEffectiveTotalResultCount(), $ioc);

            if ($tpom->get(org_tubepress_options_category_Display::PAGINATE_ABOVE)) {
                $template->setVariable(org_tubepress_template_Template::PAGINATION_TOP, $pagination);
            }
            if ($tpom->get(org_tubepress_options_category_Display::PAGINATE_BELOW)) {
                $template->setVariable(org_tubepress_template_Template::PAGINATION_BOTTOM, $pagination);
            }
        }

        $template->setVariable(org_tubepress_template_Template::EMBEDDED_IMPL_NAME, self::_getEmbeddedServiceName($tpom));
        $template->setVariable(org_tubepress_template_Template::GALLERY_ID, $galleryId);
        $template->setVariable(org_tubepress_template_Template::PLAYER_NAME, $playerName);
        $template->setVariable(org_tubepress_template_Template::THUMBNAIL_WIDTH, $tpom->get(org_tubepress_options_category_Display::THUMB_WIDTH));
        $template->setVariable(org_tubepress_template_Template::THUMBNAIL_HEIGHT, $tpom->get(org_tubepress_options_category_Display::THUMB_HEIGHT));

        self::_prepMetaInfo($template, $ioc);
        self::_prepUrlPrefixes($tpom, $template);

        /* Ajax pagination? */
        if ($tpom->get(org_tubepress_options_category_Display::AJAX_PAGINATION)) {
            org_tubepress_log_Log::log($this->_logPrefix, 'Using Ajax pagination');
            $template->setVariable(org_tubepress_template_Template::SHORTCODE, urlencode($tpom->getShortcode()));
        }
    }

    private static function _prepUrlPrefixes(org_tubepress_options_manager_OptionsManager $tpom, org_tubepress_template_Template $template)
    {
        $provider = org_tubepress_video_feed_provider_Provider::calculateCurrentVideoProvider($tpom);
        if ($provider === org_tubepress_video_feed_provider_Provider::YOUTUBE) {
            $template->setVariable(org_tubepress_template_Template::AUTHOR_URL_PREFIX, 'http://www.youtube.com/profile?user=');
            $template->setVariable(org_tubepress_template_Template::VIDEO_SEARCH_PREFIX, 'http://www.youtube.com/results?search_query=');
        } else {
            $template->setVariable(org_tubepress_template_Template::AUTHOR_URL_PREFIX, 'http://vimeo.com/');
            $template->setVariable(org_tubepress_template_Template::VIDEO_SEARCH_PREFIX, 'http://vimeo.com/videos/search:');
        }
    }

    private static function _getEmbeddedServiceName(org_tubepress_options_manager_OptionsManager $tpom)
    {
        $stored = $tpom->get(org_tubepress_options_category_Embedded::PLAYER_IMPL);
        if ($stored === org_tubepress_embedded_EmbeddedPlayerService::LONGTAIL) {
            return $stored;
        }
        return org_tubepress_video_feed_provider_Provider::calculateCurrentVideoProvider($tpom);
    }

    private static function _prepMetaInfo(org_tubepress_template_Template $template, org_tubepress_ioc_IocService $ioc)
    {
        $tpom           = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        $messageService = $ioc->get(org_tubepress_ioc_IocService::MESSAGE_SERVICE);

        $metaNames  = org_tubepress_options_reference_OptionsReference::getOptionNamesForCategory(org_tubepress_options_Category::META);
        $shouldShow = array();
        $labels     = array();

        foreach ($metaNames as $metaName) {
            $shouldShow[$metaName] = $tpom->get($metaName);
            $labels[$metaName]     = $messageService->_('video-' . $metaName);
        }
        $template->setVariable(org_tubepress_template_Template::META_SHOULD_SHOW, $shouldShow);
        $template->setVariable(org_tubepress_template_Template::META_LABELS, $labels);
    }

    private static function _prependVideoIfNeeded($videos, org_tubepress_ioc_IocService $ioc)
    {
        $customVideoId = org_tubepress_querystring_QueryStringService::getCustomVideo($_GET);
        if ($customVideoId != '') {
            $video = org_tubepress_video_feed_provider_Provider::getSingleVideo($customVideoId, $ioc);
            array_unshift($videos, $video);
        }
        return $videos;
    }
}
