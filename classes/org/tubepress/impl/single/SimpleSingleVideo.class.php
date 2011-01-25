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
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_api_theme_ThemeHandler',
    'org_tubepress_impl_options_OptionsReference',
    'org_tubepress_api_single_SingleVideo',
    'org_tubepress_api_const_options_OptionCategory',
    'org_tubepress_api_message_MessageService',
    'org_tubepress_api_provider_Provider',
    'org_tubepress_api_patterns_FilterManager',
    'org_tubepress_api_const_FilterExecutionPoint'));

/**
 * Handles requests for a single video (for embedding)
 */
class org_tubepress_impl_single_SimpleSingleVideo implements org_tubepress_api_single_SingleVideo
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
        $ioc           = org_tubepress_impl_ioc_IocContainer::getInstance();
        $filterManager = $ioc->get('org_tubepress_api_patterns_FilterManager');
        $provider      = $ioc->get('org_tubepress_api_provider_Provider');
        $themeHandler  = $ioc->get('org_tubepress_api_theme_ThemeHandler');
        $template      = $themeHandler->getTemplateInstance('single_video.tpl.php');

        /* grab the video from the provider */
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Asking provider for video with ID %s', $videoId);
        $video = $provider->getSingleVideo($videoId);

        /* add some core template variables */
        $template->setVariable(org_tubepress_api_template_Template::VIDEO, $video);
        
        /* send the template through the filters */
        $filteredTemplate = $filterManager->runFilters(org_tubepress_api_const_FilterExecutionPoint::SINGLE_VIDEO_TEMPLATE, $template);

        /* send video HTML through the filters */
        $filteredHtml = $filterManager->runFilters(org_tubepress_api_const_FilterExecutionPoint::SINGLE_VIDEO_HTML, $filteredTemplate->toString());

        /* we're done. tie up. */
	    $tpom = $ioc->get('org_tubepress_api_options_OptionsManager');
        $tpom->setCustomOptions(array());
        
        /* staples - that was easy */
        return $filteredHtml;
    }
}

