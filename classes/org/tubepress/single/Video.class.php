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
    'org_tubepress_theme_Theme',
    'org_tubepress_options_reference_OptionsReference'));

/**
 * Handles requests for a single video (for embedding)
 */
class org_tubepress_single_Video
{
    const LOG_PREFIX = 'Single video';
    
    /**
     * Get the HTML for a single video display.
     *
     * @param string $videoId The ID of the video to display.
     *
     * @return string The HTML for the single video display.
     */
    public static function getSingleVideoHtml($videoId, org_tubepress_ioc_IocService $ioc)
    {
        try {
            return self::_getSingleVideoHtml($videoId, $ioc);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get the HTML for a single video display.
     *
     * @param string $videoId The ID of the video to display.
     *
     * @return string The HTML for the single video display.
     */
    private static function _getSingleVideoHtml($videoId, org_tubepress_ioc_IocService $ioc)
    {
        /* grab the video from the provider */
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Asking provider for video with ID %s', $videoId);
        $video = org_tubepress_video_feed_provider_Provider::getSingleVideo($videoId, $ioc);

        $template = self::_prepTemplate($ioc, $video);

        /* staples - that was easy */
        return $template->toString();
    }

    /**
     * Prep the template for display.
     *
     * @param org_tubepress_video_Video $video The video to display.
     *
     * @return void
     */
    private static function _prepTemplate(org_tubepress_ioc_IocService $ioc, $video)
    {
        $template = org_tubepress_theme_Theme::getTemplateInstance($ioc, 'single_video.tpl.php');
        
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        $messageService = $ioc->get(org_tubepress_ioc_IocService::MESSAGE_SERVICE);
        
        $metaNames = org_tubepress_options_reference_OptionsReference::getOptionNamesForCategory(org_tubepress_options_Category::META);

        $shouldShow = array();
        $labels     = array();
        foreach ($metaNames as $metaName) {
            $shouldShow[$metaName] = $tpom->get($metaName);
            $labels[$metaName]     = $messageService->_('video-' . $metaName);
        }
        $template->setVariable(org_tubepress_template_Template::META_SHOULD_SHOW, $shouldShow);
        $template->setVariable(org_tubepress_template_Template::META_LABELS, $labels);

        /* apply it to the template */
        $template->setVariable(org_tubepress_template_Template::EMBEDDED_SOURCE, 
            org_tubepress_embedded_DelegatingEmbeddedPlayerService::toString($ioc, $video->getId()));
        $template->setVariable(org_tubepress_template_Template::VIDEO, $video);
        $template->setVariable(org_tubepress_template_Template::EMBEDDED_WIDTH, $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_WIDTH));
        self::_prepUrlPrefixes($template, $tpom);
       
        return $template;
    }

    private static function _prepUrlPrefixes($template, $tpom)
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
}

