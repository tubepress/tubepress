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
tubepress_load_classes(array('org_tubepress_impl_embedded_strategies_AbstractEmbeddedStrategy',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_impl_embedded_EmbeddedPlayerUtils',
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_options_OptionsManager',
    'org_tubepress_api_url_Url'));

/**
 * Embedded player strategy for YouTube embeds 
 */
abstract class org_tubepress_impl_embedded_strategies_AbstractYouTubeEmbeddedStrategy extends org_tubepress_impl_embedded_strategies_AbstractEmbeddedStrategy
{
    protected function _getEmbeddedDataUrl($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_options_OptionsManager $tpom)
    {    
        $link  = new org_tubepress_api_url_Url($this->_getUrlBaseWithoutTrailingSlash() . '/' . $videoId);

        $showRelated     = $tpom->get(org_tubepress_api_const_options_names_Embedded::SHOW_RELATED);
        $autoPlay        = $tpom->get(org_tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $loop            = $tpom->get(org_tubepress_api_const_options_names_Embedded::LOOP);
        $fullscreen      = $tpom->get(org_tubepress_api_const_options_names_Embedded::FULLSCREEN);
        $playerColor     = org_tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue($tpom->get(org_tubepress_api_const_options_names_Embedded::PLAYER_COLOR), '999999');
        $playerHighlight = org_tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue($tpom->get(org_tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT), 'FFFFFF');
        $showInfo        = $tpom->get(org_tubepress_api_const_options_names_Embedded::SHOW_INFO);

        if (!($playerColor == '999999' && $playerHighlight == 'FFFFFF')) {
            $link->setQueryVariable('color2', '0x' . $playerColor);
            $link->setQueryVariable('color1', '0x' . $playerHighlight);
        }

        $link->setQueryVariable('rel', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showRelated));
        $link->setQueryVariable('autoplay', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($autoPlay));
        $link->setQueryVariable('loop', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($loop));
        $link->setQueryVariable('fs', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($fullscreen));
        $link->setQueryVariable('showinfo', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));

        if ($tpom->get(org_tubepress_api_const_options_names_Embedded::HIGH_QUALITY)) {
            $link->setQueryVariable('hd', '1');
        }

        return $link->toString(true);
    }

    protected abstract function _getUrlBaseWithoutTrailingSlash();
}
