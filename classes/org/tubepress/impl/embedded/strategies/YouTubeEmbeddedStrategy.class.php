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
    || require dirname(__FILE__) . '/../../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_impl_embedded_strategies_AbstractYouTubeEmbeddedStrategy',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_api_http_AgentDetector',
    'org_tubepress_api_options_OptionsManager'));

/**
 * Embedded player strategy for native YouTube
 */
class org_tubepress_impl_embedded_strategies_YouTubeEmbeddedStrategy extends org_tubepress_impl_embedded_strategies_AbstractYouTubeEmbeddedStrategy
{
    protected function _canHandle($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_options_OptionsManager $tpom)
    {
        return $providerName === org_tubepress_api_provider_Provider::YOUTUBE;
    }

    protected function _getTemplatePath($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_options_OptionsManager $tpom)
    {
        $themeName = $this->_isIos($ioc) ? 'youtube-iphone' : 'youtube';

        return "embedded_flash/$themeName.tpl.php";
    }

    protected function _getEmbeddedDataUrl($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_options_OptionsManager $tpom)
    {   
	if ($this->_isIos($ioc)) {
            return "http://www.youtube.com/v/$videoId";
        }

        return parent::_getEmbeddedDataUrl($providerName, $videoId, $ioc, $tpom);
    }

    protected function _getUrlBaseWithoutTrailingSlash()
    {
        return 'http://www.youtube.com/v';
    }
    
    private function _isIos(org_tubepress_api_ioc_IocService $ioc)
    {
        $bd    = $ioc->get('org_tubepress_api_http_AgentDetector');
        $agent = $_SERVER['HTTP_USER_AGENT'];

        return $bd->isIphoneOrIpod($agent) || $bd->isIpad($agent);
    }
}

?>
