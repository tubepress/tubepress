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
    'org_tubepress_theme_ThemeHandler',
    'org_tubepress_video_feed_FeedResult',
    'org_tubepress_api_template_Template',
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

    public static function prepTemplate(org_tubepress_video_feed_FeedResult $feedResult, $galleryId, org_tubepress_api_template_Template $template, org_tubepress_ioc_IocService $ioc)
    {
        $tpom         = $ioc->get('org_tubepress_options_manager_OptionsManager');
        $themeHandler = $ioc->get('org_tubepress_theme_ThemeHandler');
        $provider     = $ioc->get('org_tubepress_video_feed_provider_Provider');
        $playerName   = $tpom->get(org_tubepress_options_category_Display::CURRENT_PLAYER_NAME);
        $videos       = $feedResult->getVideoArray();

        if (is_array($videos) && sizeof($videos) > 0) {

            $videos = self::_prependVideoIfNeeded($videos, $ioc);
            
            org_tubepress_log_Log::log(self::LOG_PREFIX, 'Applying HTML for <tt>%s</tt> player to the template', $playerName);
            
            $player     = $ioc->get('org_tubepress_player_Player');
            $playerHtml = $player->getHtml($videos[0], $galleryId);

            $template->setVariable(org_tubepress_api_template_Template::PLAYER_HTML, $playerHtml);
            $template->setVariable(org_tubepress_api_template_Template::PLAYER_NAME, $playerName);
            $template->setVariable(org_tubepress_api_template_Template::VIDEO_ARRAY, $videos);

            $paginationService = $ioc->get('org_tubepress_api_pagination_Pagination');
            $pagination        = $paginationService->getHtml($feedResult->getEffectiveTotalResultCount(), $ioc);

            if ($tpom->get(org_tubepress_options_category_Display::PAGINATE_ABOVE)) {
                $template->setVariable(org_tubepress_api_template_Template::PAGINATION_TOP, $pagination);
            }
            if ($tpom->get(org_tubepress_options_category_Display::PAGINATE_BELOW)) {
                $template->setVariable(org_tubepress_api_template_Template::PAGINATION_BOTTOM, $pagination);
            }
        } else {
            $template->setVariable(org_tubepress_api_template_Template::PLAYER_HTML, '');
            $template->setVariable(org_tubepress_api_template_Template::VIDEO_ARRAY, array());
	    }

        $currentTheme = $themeHandler->calculateCurrentThemeName();

        $template->setVariable(org_tubepress_api_template_Template::EMBEDDED_IMPL_NAME, self::_getEmbeddedServiceName($tpom, $provider));
        $template->setVariable(org_tubepress_api_template_Template::GALLERY_ID, $galleryId);
        $template->setVariable(org_tubepress_api_template_Template::PLAYER_NAME, $playerName);
        $template->setVariable(org_tubepress_api_template_Template::THUMBNAIL_WIDTH, $tpom->get(org_tubepress_options_category_Display::THUMB_WIDTH));
        $template->setVariable(org_tubepress_api_template_Template::THUMBNAIL_HEIGHT, $tpom->get(org_tubepress_options_category_Display::THUMB_HEIGHT));

        self::_prepMetaInfo($template, $ioc);
    }
    
    public static function getThumbnailGenerationReminder($galleryHtml, org_tubepress_ioc_IocService $ioc)
    {
        if (strpos($galleryHtml, 'missing_thumbnail.png') === false) {
            return '';
        }
        
        global $tubepress_base_url;
        $tpom = $ioc->get('org_tubepress_options_manager_OptionsManager');
        $baseInstallationPath = org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath();
        
        $template = new org_tubepress_template_SimpleTemplate();
        $template->setPath("$baseInstallationPath/ui/lib/gallery_html_snippets/generate_thumbnails.tpl.php");
        $template->setVariable(org_tubepress_api_template_Template::TUBEPRESS_BASE_URL, $tubepress_base_url);
        return $template->toString();
    }
    
    public static function getThemeCss(org_tubepress_ioc_IocService $ioc)
    {
        $themeHandler = $ioc->get('org_tubepress_theme_ThemeHandler');
        $currentTheme = $themeHandler->calculateCurrentThemeName($ioc);

        if ($currentTheme !== 'default') {
            global $tubepress_base_url;
            $cssPath = $themeHandler->getCssPath($currentTheme);
            if (is_readable($cssPath) && strpos($cssPath, 'themes/default') === false) {

                org_tubepress_log_Log::log(self::LOG_PREFIX, 'Theme CSS found at <tt>%s</tt>', $cssPath);
                $cssRelativePath = $themeHandler->getCssPath($currentTheme, true);
                $baseInstallationPath = org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath();
                
                $cssUrl = "$tubepress_base_url/$cssRelativePath";
                org_tubepress_log_Log::log(self::LOG_PREFIX, 'Will inject CSS from <tt>%s</tt>', $cssUrl);
                $template = new org_tubepress_template_SimpleTemplate();
                $template->setPath("$baseInstallationPath/ui/lib/gallery_html_snippets/theme_loader.tpl.php");
                $template->setVariable(org_tubepress_api_template_Template::THEME_CSS, $cssUrl);
                return $template->toString();
            } else {
                org_tubepress_log_Log::log(self::LOG_PREFIX, 'No theme CSS found.');
            }
        } else {
            org_tubepress_log_Log::log(self::LOG_PREFIX, 'Default theme is in use. No need to inject extra CSS.');
        }
        return '';
    }

    public static function getAjaxPagination(org_tubepress_ioc_IocService $ioc, $galleryId)
    {
        $tpom = $ioc->get('org_tubepress_options_manager_OptionsManager');
        
        if ($tpom->get(org_tubepress_options_category_Display::AJAX_PAGINATION)) {
            org_tubepress_log_Log::log(self::LOG_PREFIX, 'Using Ajax pagination');
            $template = new org_tubepress_template_SimpleTemplate();
            $baseInstallationPath = org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath();
             
            $template->setPath("$baseInstallationPath/ui/lib/gallery_html_snippets/ajax_pagination.tpl.php");
            $template->setVariable(org_tubepress_api_template_Template::GALLERY_ID, $galleryId);
            $template->setVariable(org_tubepress_api_template_Template::SHORTCODE, urlencode($tpom->getShortcode()));
            return $template->toString();
        }
        return '';
    }

    private static function _getEmbeddedServiceName(org_tubepress_options_manager_OptionsManager $tpom, org_tubepress_video_feed_provider_Provider $provider)
    {
        $stored = $tpom->get(org_tubepress_options_category_Embedded::PLAYER_IMPL);
        if ($stored === org_tubepress_api_embedded_EmbeddedPlayer::LONGTAIL) {
            return $stored;
        }
        return $provider->calculateCurrentVideoProvider($tpom);
    }

    private static function _prepMetaInfo(org_tubepress_api_template_Template $template, org_tubepress_ioc_IocService $ioc)
    {
        $tpom           = $ioc->get('org_tubepress_options_manager_OptionsManager');
        $messageService = $ioc->get('org_tubepress_api_message_MessageService');

        $metaNames  = org_tubepress_options_reference_OptionsReference::getOptionNamesForCategory(org_tubepress_options_Category::META);
        $shouldShow = array();
        $labels     = array();

        foreach ($metaNames as $metaName) {
            $shouldShow[$metaName] = $tpom->get($metaName);
            $labels[$metaName]     = $messageService->_('video-' . $metaName);
        }
        $template->setVariable(org_tubepress_api_template_Template::META_SHOULD_SHOW, $shouldShow);
        $template->setVariable(org_tubepress_api_template_Template::META_LABELS, $labels);
    }
    
    private static function _prependVideoIfNeeded($videos, org_tubepress_ioc_IocService $ioc)
    {
        $qss = $ioc->get('org_tubepress_querystring_QueryStringService');
        $customVideoId = $qss->getCustomVideo($_GET);
        if ($customVideoId != '') {
            org_tubepress_log_Log::log(self::LOG_PREFIX, 'Prepending video <tt>%s</tt> to the gallery', $customVideoId);
            $provider = $ioc->get('org_tubepress_video_feed_provider_Provider');
            try {
                $video = $provider->getSingleVideo($customVideoId);
                array_unshift($videos, $video);
            } catch (Exception $e) {
                org_tubepress_log_Log::log(self::LOG_PREFIX, 'Could not prepend video <tt>%s</tt> to the gallery: %s', $customVideoId, $e->getMessage());
            }
        }
        return $videos;
    }
}
