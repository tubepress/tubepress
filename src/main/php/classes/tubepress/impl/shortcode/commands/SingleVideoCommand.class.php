<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
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

org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_spi_patterns_cor_Command',
    'org_tubepress_api_provider_ProviderCalculator',
    'org_tubepress_api_message_MessageService',
));

/**
 * HTML generation command that generates HTML for a single video + meta info.
 */
class org_tubepress_impl_shortcode_commands_SingleVideoCommand implements org_tubepress_spi_patterns_cor_Command
{
    const LOG_PREFIX = 'Single Video Command';

    /**
     * Execute the command.
     *
     * @param array $context An array of context elements (may be empty).
     *
     * @return boolean True if this command was able to handle the execution. False otherwise.
     */
    public function execute($context)
    {
        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $videoId     = $execContext->get(org_tubepress_api_const_options_names_Output::VIDEO);

        if ($videoId == '') {
            return false;
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Building single video with ID %s', $videoId);

        $context->returnValue = $this->_getSingleVideoHtml($videoId, $ioc);

        return true;
    }

    private function _getSingleVideoHtml($videoId, $ioc)
    {
        $ms            = $ioc->get(org_tubepress_api_message_MessageService::_);
        $pluginManager = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
        $provider      = $ioc->get(org_tubepress_api_provider_Provider::_);
        $themeHandler  = $ioc->get(org_tubepress_api_theme_ThemeHandler::_);
        $pc            = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
        $template      = $themeHandler->getTemplateInstance('single_video.tpl.php');
        $providerName  = $pc->calculateProviderOfVideoId($videoId);

        /* grab the video from the provider */
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Asking provider for video with ID %s', $videoId);
        $video = $provider->getSingleVideo($videoId);

        /* send the template through the filters */
        $template = $pluginManager->runFilters(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_SINGLEVIDEO, $template, $video, $providerName);

        /* send video HTML through the filters */
        return $pluginManager->runFilters(org_tubepress_api_const_plugin_FilterPoint::HTML_SINGLEVIDEO,
                            $template->toString(), $video, $providerName);
    }
}
