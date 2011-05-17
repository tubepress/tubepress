<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
    'org_tubepress_api_patterns_cor_Chain',
    'org_tubepress_api_plugin_PluginManager',
    'org_tubepress_api_provider_ProviderCalculator',
    'org_tubepress_impl_embedded_EmbeddedPlayerChainContext',
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
        $pc           = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        $chain        = $ioc->get('org_tubepress_api_patterns_cor_Chain');
        $pm           = $ioc->get('org_tubepress_api_plugin_PluginManager');
        $providerName = $pc->calculateProviderOfVideoId($videoId);
        $context      = new org_tubepress_impl_embedded_EmbeddedPlayerChainContext($providerName, $videoId);
        
        /* let the commands do the heavy lifting */
        $status = $chain->execute(
            $context,
            array(
                'org_tubepress_impl_embedded_commands_JwFlvCommand',
                'org_tubepress_impl_embedded_commands_YouTubeIframeCommand',
                'org_tubepress_impl_embedded_commands_VimeoCommand',
            )
        );
        
        if ($status === false) {
            throw new Exception('No commands could produce the embedded HTML');
        }
        
        return $pm->runFilters(org_tubepress_api_const_plugin_FilterPoint::HTML_EMBEDDED, $context->getReturnValue());
    }
}