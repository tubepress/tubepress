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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_api_embedded_EmbeddedHtmlGenerator',
    'org_tubepress_spi_patterns_cor_Chain',
    'org_tubepress_api_plugin_PluginManager',
    'org_tubepress_api_provider_ProviderCalculator',
    'org_tubepress_impl_ioc_IocContainer',
));

/**
 * An HTML-embeddable video player.
 */
class org_tubepress_impl_embedded_EmbeddedPlayerChain implements org_tubepress_api_embedded_EmbeddedHtmlGenerator
{
    /**
     * Spits back the HTML for this embedded player
     *
     * @param string $videoId The video ID to display
     *
     * @return string The HTML for this embedded player
     */
    public function getHtml($videoId)
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $pc           = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
        $chain        = $ioc->get(org_tubepress_spi_patterns_cor_Chain::_);
        $pm           = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
        $providerName = $pc->calculateProviderOfVideoId($videoId);
        $context      = $chain->createContextInstance();

        $context->providerName = $providerName;
        $context->videoId      = $videoId;

        /* let the commands do the heavy lifting */
        $status = $chain->execute($context, array(
             'org_tubepress_impl_embedded_commands_JwFlvCommand',
             'org_tubepress_impl_embedded_commands_EmbedPlusCommand',
             'org_tubepress_impl_embedded_commands_YouTubeIframeCommand',
             'org_tubepress_impl_embedded_commands_VimeoCommand',
        ));

        /* if nobody can handle it, there's really nothing else to do but bail */
        if ($status === false) {
            throw new Exception('No commands could produce the embedded HTML');
        }

        /* pull out the relevant stuff from the context */
        $template = $context->template;
        $dataUrl  = $context->dataUrl;
        $implName = $context->embeddedImplementationName;

        $template = $pm->runFilters(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_EMBEDDED,
            $template, $videoId, $providerName, $dataUrl, $implName);

        return $pm->runFilters(org_tubepress_api_const_plugin_FilterPoint::HTML_EMBEDDED,
            $template->toString(), $videoId, $providerName, $implName);
    }
}
