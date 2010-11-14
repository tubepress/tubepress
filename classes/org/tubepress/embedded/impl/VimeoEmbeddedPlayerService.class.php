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
tubepress_load_classes(array(
    'org_tubepress_embedded_EmbeddedPlayerService',
    'org_tubepress_ioc_IocContainer'));

/**
 * An HTML-embeddable player for Vimeo.
 */
class org_tubepress_embedded_impl_VimeoEmbeddedPlayerService implements org_tubepress_embedded_EmbeddedPlayerService
{
    const VIMEO_EMBEDDED_PLAYER_URL = 'http://player.vimeo.com/';
    const VIMEO_QUERYPARAM_AUTOPLAY = 'autoplay';
    const VIMEO_QUERYPARAM_TITLE    = 'title';
    const VIMEO_QUERYPARAM_BYLINE   = 'byline';
    const VIMEO_QUERYPARAM_COLOR    = 'color';
    const VIMEO_QUERYPARAM_LOOP     = 'loop';
    const VIMEO_QUERYPARAM_PORTRAIT = 'portrait';

    /**
     * Spits back the text for this embedded player
     *
     * @param string                       $videoId The video ID to display
     *
     * @return string The text for this embedded player
     */
    public function toString($videoId)
    {
        /* collect the embedded options we're interested in */
        $ioc   = org_tubepress_ioc_IocContainer::getInstance();
        $tpom     = $ioc->get('org_tubepress_options_manager_OptionsManager');
        $theme    = $ioc->get('org_tubepress_theme_ThemeHandler');
        $template = $theme->getTemplateInstance('embedded_flash/vimeo.tpl.php');
        
        $width      = $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_WIDTH);
        $height     = $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT);
        $fullscreen = $tpom->get(org_tubepress_options_category_Embedded::FULLSCREEN);
        $autoPlay   = $tpom->get(org_tubepress_options_category_Embedded::AUTOPLAY);
        $color      = $tpom->get(org_tubepress_options_category_Embedded::PLAYER_COLOR);
        $showInfo   = $tpom->get(org_tubepress_options_category_Embedded::SHOW_INFO);
        $loop       = $tpom->get(org_tubepress_options_category_Embedded::LOOP);

        /* build the data URL based on these options */
        $link = new net_php_pear_Net_URL2(self::VIMEO_EMBEDDED_PLAYER_URL . "video/$videoId");
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_AUTOPLAY, org_tubepress_embedded_impl_EmbeddedPlayerUtils::booleanToOneOrZero($autoPlay));
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_COLOR, $color);
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_LOOP, org_tubepress_embedded_impl_EmbeddedPlayerUtils::booleanToOneOrZero($loop));
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_TITLE, org_tubepress_embedded_impl_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_BYLINE, org_tubepress_embedded_impl_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));
        $link->setQueryVariable(self::VIMEO_QUERYPARAM_PORTRAIT, org_tubepress_embedded_impl_EmbeddedPlayerUtils::booleanToOneOrZero($showInfo));

        $link = $link->getURL(true);

        /* prep the template and we're done */
        $template->setVariable(org_tubepress_template_Template::EMBEDDED_DATA_URL, $link);
        $template->setVariable(org_tubepress_template_Template::EMBEDDED_WIDTH, $width);
        $template->setVariable(org_tubepress_template_Template::EMBEDDED_HEIGHT, $height);
        $template->setVariable(org_tubepress_template_Template::EMBEDDED_FULLSCREEN, org_tubepress_embedded_impl_EmbeddedPlayerUtils::booleanToOneOrZero($fullscreen));
        return $template->toString();
    }
}
