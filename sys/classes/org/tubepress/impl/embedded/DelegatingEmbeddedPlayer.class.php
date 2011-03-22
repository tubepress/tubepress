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

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_patterns_StrategyManager',
    'org_tubepress_api_embedded_EmbeddedPlayer',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_api_provider_ProviderCalculator'));

/**
 * An HTML-embeddable video player.
 */
class org_tubepress_impl_embedded_DelegatingEmbeddedPlayer implements org_tubepress_api_embedded_EmbeddedPlayer
{
    /**
     * Spits back the HTML for this embedded player
     *
     * @param string $videoId The video ID to display
     *
     * @return string The HTML for this embedded player
     */
    public function toString($videoId)
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $pc           = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        $sm           = $ioc->get('org_tubepress_api_patterns_StrategyManager');
        
        //TODO: what if this bails?
        $providerName = $pc->calculateProviderOfVideoId($videoId);

        /* let the strategies do the heavy lifting */
        //TODO: what if this bails?
        return $sm->executeStrategy(array(
            'org_tubepress_impl_embedded_strategies_JwFlvEmbeddedStrategy',
            'org_tubepress_impl_embedded_strategies_YouTubeIframeEmbeddedStrategy',
            'org_tubepress_impl_embedded_strategies_VimeoEmbeddedStrategy'
        ), $providerName, $videoId);
    }
}
