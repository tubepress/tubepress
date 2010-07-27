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
    'org_tubepress_options_category_Display',
    'org_tubepress_theme_Theme',
    'org_tubepress_video_feed_FeedResult',
    'org_tubepress_template_Template',
    'org_tubepress_player_Player',
    'org_tubepress_template_SimpleTemplate',
    'org_tubepress_querystring_QueryStringService',
    'org_tubepress_video_feed_provider_Provider',
    'org_tubepress_util_FilesystemUtils'));

/**
 * 
 */
class org_tubepress_gallery_GalleryTemplateUtils
{
    const LOG_PREFIX = 'Gallery Template Utils';

    public static function prepTemplate(org_tubepress_video_feed_FeedResult $feedResult, $galleryId,
        org_tubepress_template_Template $template, org_tubepress_ioc_IocService $ioc)
    {
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);

        $videos = $feedResult->getVideoArray();
        if (is_array($videos) && sizeof($videos) > 0) {

            $videos = self::_prependVideoIfNeeded($videos, $ioc);
            
            $playerHtml = org_tubepress_player_Player::getHtml($ioc, $videos[0], $galleryId);
            $template->setVariable(org_tubepress_template_Template::PLAYER_HTML, $playerHtml);
            $playerName = $tpom->get(org_tubepress_options_category_Display::CURRENT_PLAYER_NAME);
            $template->setVariable(org_tubepress_template_Template::PLAYER_NAME, $playerName);

            $template->setVariable(org_tubepress_template_Template::VIDEO_ARRAY, $videos);

            $paginationService = $ioc->get(org_tubepress_ioc_IocService::PAGINATION_SERVICE);
            $pagination        = $paginationService->getHtml($feedResult->getEffectiveTotalResultCount(), $ioc);

            if ($tpom->get(org_tubepress_options_category_Display::PAGINATE_ABOVE)) {
                $template->setVariable(org_tubepress_template_Template::PAGINATION_TOP, $pagination);
            }
            if ($tpom->get(org_tubepress_options_category_Display::PAGINATE_BELOW)) {
                $template->setVariable(org_tubepress_template_Template::PAGINATION_BOTTOM, $pagination);
            }
            
            $template->setVariable(org_tubepress_template_Template::EMBEDDED_SOURCE,
                org_tubepress_embedded_DelegatingEmbeddedPlayerService::toString($ioc, $videos[0]->getId()));
        }

        $currentTheme = org_tubepress_theme_Theme::calculateCurrentThemeName($ioc);

        $template->setVariable(org_tubepress_template_Template::EMBEDDED_IMPL_NAME, self::_getEmbeddedServiceName($tpom));
        $template->setVariable(org_tubepress_template_Template::GALLERY_ID, $galleryId);
        $template->setVariable(org_tubepress_template_Template::PLAYER_NAME, $playerName);
        $template->setVariable(org_tubepress_template_Template::THUMBNAIL_WIDTH, $tpom->get(org_tubepress_options_category_Display::THUMB_WIDTH));
        $template->setVariable(org_tubepress_template_Template::THUMBNAIL_HEIGHT, $tpom->get(org_tubepress_options_category_Display::THUMB_HEIGHT));

        self::_prepMetaInfo($template, $ioc);
        self::_prepUrlPrefixes($tpom, $template);
    }
    
    public static function getThumbnailGenerationReminder($galleryHtml, org_tubepress_ioc_IocService $ioc)
    {
        if (strpos($galleryHtml, 'missing_thumbnail.png') === false) {
            return '';
        }
        
        global $tubepress_base_url;
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        $baseInstallationPath = org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath();
        
        $template = new org_tubepress_template_SimpleTemplate();
        $template->setPath("$baseInstallationPath/ui/lib/gallery_html_snippets/generate_thumbnails.tpl.php");
        $template->setVariable(org_tubepress_template_Template::TUBEPRESS_BASE_URL, $tubepress_base_url);
        return $template->toString();
    }
    
    public static function getThemeCss(org_tubepress_ioc_IocService $ioc)
    {
        $currentTheme = org_tubepress_theme_Theme::calculateCurrentThemeName($ioc);

        if ($currentTheme != 'default') {
            global $tubepress_base_url;
            $cssPath = org_tubepress_theme_Theme::getCssPath($currentTheme);
            if (is_readable($cssPath)) {

                org_tubepress_log_Log::log(self::LOG_PREFIX, 'Theme CSS found at %s', $cssPath);
                $cssRelativePath = org_tubepress_theme_Theme::getCssPath($currentTheme, true);
                $baseInstallationPath = org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath();
                
                $cssUrl = "$tubepress_base_url/$cssRelativePath";
                org_tubepress_log_Log::log(self::LOG_PREFIX, 'Will inject CSS from %s', $cssUrl);
                $template = new org_tubepress_template_SimpleTemplate();
                $template->setPath("$baseInstallationPath/ui/lib/gallery_html_snippets/theme_loader.tpl.php");
                $template->setVariable(org_tubepress_template_Template::THEME_CSS, $cssUrl);
                return $template->toString();
            } else {
                org_tubepress_log_Log::log(self::LOG_PREFIX, 'No theme CSS found.', $cssPath);
            }
        } else {
            org_tubepress_log_Log::log(self::LOG_PREFIX, 'Default theme is in use. No need to inject extra CSS.');
        }
        return '';
    }

    public static function getAjaxPagination(org_tubepress_ioc_IocService $ioc)
    {
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
        if ($tpom->get(org_tubepress_options_category_Display::AJAX_PAGINATION)) {
            org_tubepress_log_Log::log($this->_logPrefix, 'Using Ajax pagination');
            $template = new org_tubepress_template_SimpleTemplate();
            $baseInstallationPath = org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath();
             
            $template->setPath("$baseInstallationPath/ui/lib/gallery_html_snippets/ajax_pagination.tpl.php");
            $template->setVariable(org_tubepress_template_Template::SHORTCODE, urlencode($tpom->getShortcode()));
            return $template->toString();
        }
        return '';
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
            org_tubepress_log_Log::log(self::LOG_PREFIX, 'Prepending video %s to the gallery', $customVideoId);
            $video = org_tubepress_video_feed_provider_Provider::getSingleVideo($customVideoId, $ioc);
            array_unshift($videos, $video);
        }
        return $videos;
    }
}
