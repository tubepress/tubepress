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
tubepress_load_classes(array('org_tubepress_ioc_IocContainer',
    'org_tubepress_api_theme_ThemeHandler',
    'org_tubepress_util_OptionsReference',
    'org_tubepress_api_single_SingleVideo',
    'org_tubepress_api_const_options_OptionCategory',
    'org_tubepress_api_message_MessageService'));

/**
 * Handles requests for a single video (for embedding)
 */
class org_tubepress_single_SimpleSingleVideo implements org_tubepress_api_single_SingleVideo
{
    const LOG_PREFIX = 'Single video';
    
    /**
     * Get the HTML for a single video display.
     *
     * @param string $videoId The ID of the video to display.
     *
     * @return string The HTML for the single video display.
     */
    public function getSingleVideoHtml($videoId)
    {
        $ioc = org_tubepress_ioc_IocContainer::getInstance();
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
    private static function _getSingleVideoHtml($videoId, org_tubepress_api_ioc_IocService $ioc)
    {
        /* grab the video from the provider */
        org_tubepress_util_Log::log(self::LOG_PREFIX, 'Asking provider for video with ID %s', $videoId);
        $provider = $ioc->get('org_tubepress_api_provider_Provider');
        $video = $provider->getSingleVideo($videoId);

        $template = self::_prepTemplate($ioc, $video, $provider);
        $result   = $template->toString();
        $result .= org_tubepress_gallery_GalleryTemplateUtils::getThemeCss($ioc);

	$tpom = $ioc->get('org_tubepress_api_options_OptionsManager');
        $tpom->setCustomOptions(array());
        
        /* staples - that was easy */
        return $result;
    }

    /**
     * Prep the template for display.
     *
     * @param org_tubepress_api_video_Video $video The video to display.
     *
     * @return void
     */
    private static function _prepTemplate(org_tubepress_api_ioc_IocService $ioc, $video, org_tubepress_api_provider_Provider $provider)
    {
        $themeHandler   = $ioc->get('org_tubepress_api_theme_ThemeHandler');
        $template       = $themeHandler->getTemplateInstance('single_video.tpl.php');
        $tpom           = $ioc->get('org_tubepress_api_options_OptionsManager');
        $messageService = $ioc->get('org_tubepress_api_message_MessageService');
        $metaNames      = org_tubepress_util_OptionsReference::getOptionNamesForCategory(org_tubepress_api_const_options_OptionCategory::META);
        $shouldShow     = array();
        $labels         = array();
        $eps            = $ioc->get('org_tubepress_api_embedded_EmbeddedPlayer');

        foreach ($metaNames as $metaName) {
            $shouldShow[$metaName] = $tpom->get($metaName);
            $labels[$metaName]     = $messageService->_('video-' . $metaName);
        }
        $template->setVariable(org_tubepress_api_template_Template::META_SHOULD_SHOW, $shouldShow);
        $template->setVariable(org_tubepress_api_template_Template::META_LABELS, $labels);

        /* apply it to the template */
        $template->setVariable(org_tubepress_api_template_Template::EMBEDDED_SOURCE, $eps->toString($video->getId()));
        $template->setVariable(org_tubepress_api_template_Template::VIDEO, $video);
        $template->setVariable(org_tubepress_api_template_Template::EMBEDDED_WIDTH, $tpom->get(org_tubepress_api_const_options_Embedded::EMBEDDED_WIDTH));
       
        return $template;
    }
}

