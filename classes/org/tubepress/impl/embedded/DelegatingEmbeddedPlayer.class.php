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
tubepress_load_classes(array('org_tubepress_api_provider_Provider',
    'org_tubepress_api_embedded_EmbeddedPlayer',
    'org_tubepress_ioc_IocContainer',
    'org_tubepress_util_ProviderCalculator'));

/**
 * An HTML-embeddable player
 *
 */
class org_tubepress_impl_embedded_DelegatingEmbeddedPlayer implements org_tubepress_api_embedded_EmbeddedPlayer
{
    /**
     * Spits back the text for this embedded player
     *
     * @param string $videoId The video ID to display
     *
     * @return string The text for this embedded player
     */
    public function toString($videoId)
    {
        $ioc          = org_tubepress_ioc_IocContainer::getInstance();
	$providerName = org_tubepress_util_ProviderCalculator::calculateProviderOfVideoId($videoId);
    
        /** The user wants to use JW FLV Player to show YouTube videos. */   
        if ($providerName === org_tubepress_api_provider_Provider::YOUTUBE 
            && $tpom->get(org_tubepress_options_category_Embedded::PLAYER_IMPL) === org_tubepress_api_embedded_EmbeddedPlayer::LONGTAIL) {
            return $ioc->get('org_tubepress_api_embedded_EmbeddedPlayer', org_tubepress_api_embedded_EmbeddedPlayer::LONGTAIL)->toString($videoId);    
        }
        
        $service = $ioc->get('org_tubepress_api_embedded_EmbeddedPlayer', $providerName);
        return $service->toString($videoId);
    }
}
