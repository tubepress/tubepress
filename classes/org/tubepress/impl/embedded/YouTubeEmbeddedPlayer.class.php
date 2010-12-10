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
tubepress_load_classes(array('org_tubepress_api_embedded_EmbeddedPlayer',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_impl_embedded_EmbeddedPlayerUtils',
    'org_tubepress_api_const_options_Embedded',
    'org_tubepress_api_theme_ThemeHandler',
    'org_tubepress_api_template_Template',
    'net_php_pear_Net_URL2'));

/**
 * An HTML-embeddable YouTube player
 *
 */
class org_tubepress_impl_embedded_YouTubeEmbeddedPlayer implements org_tubepress_api_embedded_EmbeddedPlayer
{
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
        $link  = new net_php_pear_Net_URL2(sprintf('http://www.youtube.com/v/%s', $videoId));
        $ioc   = org_tubepress_ioc_IocContainer::getInstance();
        $tpom  = $ioc->get('org_tubepress_api_options_OptionsManager');
        $theme = $ioc->get('org_tubepress_api_theme_ThemeHandler');
        $bd    = $ioc->get('org_tubepress_api_http_AgentDetector');

        $playerColor     = org_tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue($tpom->get(org_tubepress_api_const_options_Embedded::PLAYER_COLOR), '999999');
        $playerHighlight = org_tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue($tpom->get(org_tubepress_api_const_options_Embedded::PLAYER_HIGHLIGHT), 'FFFFFF');
        $showRelated     = $tpom->get(org_tubepress_api_const_options_Embedded::SHOW_RELATED);
        $autoPlay        = $tpom->get(org_tubepress_api_const_options_Embedded::AUTOPLAY);
        $loop            = $tpom->get(org_tubepress_api_const_options_Embedded::LOOP);
        $genie           = $tpom->get(org_tubepress_api_const_options_Embedded::GENIE);
        $border          = $tpom->get(org_tubepress_api_const_options_Embedded::BORDER);
        $width           = $tpom->get(org_tubepress_api_const_options_Embedded::EMBEDDED_WIDTH);
        $height          = $tpom->get(org_tubepress_api_const_options_Embedded::EMBEDDED_HEIGHT);
        $hq              = $tpom->get(org_tubepress_api_const_options_Embedded::HIGH_QUALITY);
        $fullscreen      = $tpom->get(org_tubepress_api_const_options_Embedded::FULLSCREEN);
        $showInfo        = $tpom->get(org_tubepress_api_const_options_Embedded::SHOW_INFO);

        if (!($playerColor == '999999' && $playerHighlight == 'FFFFFF')) {
            $link->setQueryVariable('color2', '0x' . $playerColor);
            $link->setQueryVariable('color1', '0x' . $playerHighlight);
        }
        $link->setQueryVariable('rel', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showRelated));
        $link->setQueryVariable('autoplay', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($autoPlay));
        $link->setQueryVariable('loop', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($loop));
        $link->setQueryVariable('egm', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($genie));
        $link->setQueryVariable('border', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($border));
        $link->setQueryVariable('fs', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($fullscreen));
        $link->setQueryVariable('showinfo', org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));

        if ($hq) {
            $link->setQueryVariable('hd', '1');
        }

        $link = $link->getURL(true);
        $themeName = 'youtube';
        if ($this->isIsomething($bd)) {
            $themeName = 'youtube-iphone';
            $link = "http://www.youtube.com/v/$videoId";
        }
        
        $template = $theme->getTemplateInstance("embedded_flash/$themeName.tpl.php");
        $template->setVariable(org_tubepress_api_template_Template::EMBEDDED_DATA_URL, $link);
        $template->setVariable(org_tubepress_api_template_Template::EMBEDDED_WIDTH, $width);
        $template->setVariable(org_tubepress_api_template_Template::EMBEDDED_HEIGHT, $height);
        $template->setVariable(org_tubepress_api_template_Template::EMBEDDED_FULLSCREEN, org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToString($fullscreen));

        $embedSrc = $template->toString();

        return $embedSrc;
    }
    
    private function isIsomething(org_tubepress_api_http_AgentDetector $bd)
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        return $bd->isIphoneOrIpod($agent) || $bd->isIpad($agent);
    }
}
