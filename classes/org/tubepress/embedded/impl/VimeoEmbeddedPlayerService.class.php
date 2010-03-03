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
    || require(dirname(__FILE__) . '/../../../../tubepress_classloader.php');
tubepress_load_classes(array(
    'org_tubepress_embedded_impl_AbstractEmbeddedPlayerService'));

/**
 * An HTML-embeddable player for Vimeo
 *
 */
class org_tubepress_embedded_impl_VimeoEmbeddedPlayerService extends org_tubepress_embedded_impl_AbstractEmbeddedPlayerService
{
    const VIMEO_EMBEDDED_PLAYER_URL = 'http://vimeo.com/moogaloop.swf';
    const VIMEO_QUERYPARAM_CLIPID   = 'clip_id';
    const VIMEO_QUERYPARAM_FS       = 'fullscreen';
    const VIMEO_QUERYPARAM_AUTOPLAY = 'autoplay';
    const VIMEO_QUERYPARAM_TITLE    = 'show_title';
    const VIMEO_QUERYPARAM_BYLINE   = 'show_byline';
    const VIMEO_QUERYPARAM_COLOR    = 'color';

    /**
     * Spits back the text for this embedded player
     *
     * @param $videoId The video ID to display
     *
     * @return string The text for this embedded player
     */
    public function toString($videoId)
    {   
        /* collect the embedded options we're interested in */
        $tpom = $this->getOptionsManager();
        $width       = $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_WIDTH);
        $height      = $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT);
        $fullscreen  = $tpom->get(org_tubepress_options_category_Embedded::FULLSCREEN);
        $autoPlay    = $tpom->get(org_tubepress_options_category_Embedded::AUTOPLAY);
        $color       = $tpom->get(org_tubepress_options_category_Embedded::PLAYER_HIGHLIGHT);
        $showInfo    = $tpom->get(org_tubepress_options_category_Embedded::SHOW_INFO);

        /* build the data URL based on these options */
        $link = new net_php_pear_Net_URL2(org_tubepress_embedded_impl_VimeoEmbeddedPlayerService::VIMEO_EMBEDDED_PLAYER_URL);
        $link->setQueryVariable(org_tubepress_embedded_impl_VimeoEmbeddedPlayerService::VIMEO_QUERYPARAM_CLIPID,   $videoId);
        $link->setQueryVariable(org_tubepress_embedded_impl_VimeoEmbeddedPlayerService::VIMEO_QUERYPARAM_FS,       $this->booleanToOneOrZero($fullscreen));
        $link->setQueryVariable(org_tubepress_embedded_impl_VimeoEmbeddedPlayerService::VIMEO_QUERYPARAM_AUTOPLAY, $this->booleanToOneOrZero($autoPlay));
        $link->setQueryVariable(org_tubepress_embedded_impl_VimeoEmbeddedPlayerService::VIMEO_QUERYPARAM_COLOR,    $color);
        if ($showInfo) {
            $link->setQueryVariable(org_tubepress_embedded_impl_VimeoEmbeddedPlayerService::VIMEO_QUERYPARAM_TITLE,  '1');
            $link->setQueryVariable(org_tubepress_embedded_impl_VimeoEmbeddedPlayerService::VIMEO_QUERYPARAM_BYLINE, '1');
        }
        $link = $link->getURL(true);

        /* prep the template and we're done */
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_DATA_URL,   $link);
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_WIDTH,      $width);
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_HEIGHT,     $height);
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_FULLSCREEN, $this->booleanToString($fullscreen));
        return $this->_template->toString();
    }
}
