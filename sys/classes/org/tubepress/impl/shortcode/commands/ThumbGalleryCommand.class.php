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
    'org_tubepress_api_const_http_ParamName',
    'org_tubepress_api_const_options_names_Advanced',
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_api_const_template_Variable',
    'org_tubepress_api_http_HttpRequestParameterService',
    'org_tubepress_spi_patterns_cor_Command',
    'org_tubepress_api_plugin_PluginManager',
    'org_tubepress_api_provider_Provider',
    'org_tubepress_api_provider_ProviderResult',
    'org_tubepress_impl_log_Log',
));

class org_tubepress_impl_shortcode_commands_ThumbGalleryCommand implements org_tubepress_spi_patterns_cor_Command
{
    const LOG_PREFIX = 'Thumb Gallery Command';

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
        $galleryId   = $execContext->get(org_tubepress_api_const_options_names_Advanced::GALLERY_ID);

        if ($galleryId == '') {

            $galleryId = mt_rand();

            $result = $execContext->set(org_tubepress_api_const_options_names_Advanced::GALLERY_ID, $galleryId);

            if ($result !== true) {

                return false;
            }
        }

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Starting to build thumbnail gallery %s', $galleryId);

        $provider      = $ioc->get(org_tubepress_api_provider_Provider::_);
        $pluginManager = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
        $themeHandler  = $ioc->get(org_tubepress_api_theme_ThemeHandler::_);
        $ms            = $ioc->get(org_tubepress_api_message_MessageService::_);
        $pc            = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
        $qss           = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);
        $template      = $themeHandler->getTemplateInstance('gallery.tpl.php');
        $page          = $qss->getParamValueAsInt(org_tubepress_api_const_http_ParamName::PAGE, 1);
        $providerName  = $pc->calculateCurrentVideoProvider();

        /* first grab the videos */
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Asking provider for videos');
        $feedResult = $provider->getMultipleVideos();
        $numVideos  = sizeof($feedResult->getVideoArray());
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Provider has delivered %d videos', $numVideos);

        if ($numVideos == 0) {
            $context->returnValue = $ms->_('no-videos-found');
            return true;
        }

        /* send the template through the plugins */
        $template = $pluginManager->runFilters(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY, $template, $feedResult, $page, $providerName);

        /* send gallery HTML through the plugins */
        $filteredHtml = $pluginManager->runFilters(org_tubepress_api_const_plugin_FilterPoint::HTML_GALLERY, $template->toString(), $feedResult, $page, $providerName);

        /* we're done. tie up */
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Done assembling gallery %d', $galleryId);

        $context->returnValue = $filteredHtml;

        return true;
    }
}
