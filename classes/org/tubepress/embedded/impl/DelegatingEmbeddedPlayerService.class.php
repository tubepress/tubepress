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
    || require dirname(__FILE__) . '/../../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_provider_Provider',
    'org_tubepress_api_embedded_EmbeddedPlayer',
    'org_tubepress_embedded_impl_VimeoEmbeddedPlayerService',
    'org_tubepress_ioc_IocDelegateUtils'));

/**
 * An HTML-embeddable player
 *
 */
class org_tubepress_embedded_impl_DelegatingEmbeddedPlayerService implements org_tubepress_api_embedded_EmbeddedPlayer
{
    private static $_providerToBeanNameMap = array(
        org_tubepress_api_provider_Provider::VIMEO => 'org_tubepress_embedded_impl_VimeoEmbeddedPlayerService',
        org_tubepress_api_provider_Provider::DIRECTORY => 'org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService'
    );

    private static $_defaultDelegateBeanName = 'org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService';

    /**
     * Spits back the text for this embedded player
     *
     * @param org_tubepress_api_ioc_IocService $ioc     The IOC container
     * @param string                       $videoId The video ID to display
     *
     * @return string The text for this embedded player
     */
    public function toString($videoId)
    {
        $ioc          = org_tubepress_ioc_IocContainer::getInstance();
        $tpom         = $ioc->get('org_tubepress_api_options_OptionsManager');
        $provider     = $ioc->get('org_tubepress_api_provider_Provider');
        $providerName = $provider->calculateProviderOfVideoId($videoId);
        
        /** The user wants to use JW FLV Player to show YouTube videos. */
        if ($providerName === org_tubepress_api_provider_Provider::YOUTUBE
            && $tpom->get(org_tubepress_options_category_Embedded::PLAYER_IMPL) === org_tubepress_api_embedded_EmbeddedPlayer::LONGTAIL) {
            return $ioc->get('org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService')->toString($videoId);    
        }
        
        return org_tubepress_ioc_IocDelegateUtils::getDelegate(self::$_providerToBeanNameMap,
           self::$_defaultDelegateBeanName)->toString($videoId);
    }
}
