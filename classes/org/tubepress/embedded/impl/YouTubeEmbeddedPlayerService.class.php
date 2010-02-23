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
    'org_tubepress_embedded_impl_AbstractEmbeddedPlayerService',
    'net_php_pear_Net_URL2',
    'org_tubepress_options_category_Embedded',
    'org_tubepress_template_Template'));

/**
 * An HTML-embeddable YouTube player
 *
 */
class org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService extends org_tubepress_embedded_impl_AbstractEmbeddedPlayerService
{
    /**
     * Spits back the text for this embedded player
     *
     * @param $videoId The video ID to display
     *
     * @return string The text for this embedded player
     */
    public function toString($videoId)
    {   
        $link = new net_php_pear_Net_URL2(sprintf('http://www.youtube.com/v/%s', $videoId));
        
        $tpom = $this->getOptionsManager();
        
        $color1      = $this->_safeColorValue($tpom->get(org_tubepress_options_category_Embedded::PLAYER_COLOR), '999999');
        $color2      = $this->_safeColorValue($tpom->get(org_tubepress_options_category_Embedded::PLAYER_HIGHLIGHT), 'FFFFFF');
        $showRelated = $tpom->get(org_tubepress_options_category_Embedded::SHOW_RELATED);
        $autoPlay    = $tpom->get(org_tubepress_options_category_Embedded::AUTOPLAY);
        $loop        = $tpom->get(org_tubepress_options_category_Embedded::LOOP);
        $genie       = $tpom->get(org_tubepress_options_category_Embedded::GENIE);
        $border      = $tpom->get(org_tubepress_options_category_Embedded::BORDER);
        $width       = $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_WIDTH);
        $height      = $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT);
        $hq          = $tpom->get(org_tubepress_options_category_Embedded::HIGH_QUALITY);
        $fullscreen  = $tpom->get(org_tubepress_options_category_Embedded::FULLSCREEN);
        $showInfo    = $tpom->get(org_tubepress_options_category_Embedded::SHOW_INFO);
   
        if (!($color1 == '999999' && $color2 == 'FFFFFF')) {
            $link->setQueryVariable('color2', '0x' . $color1);
            $link->setQueryVariable('color1', '0x' . $color2);
        }
        $link->setQueryVariable('rel',      $this->booleanToOneOrZero($showRelated));
        $link->setQueryVariable('autoplay', $this->booleanToOneOrZero($autoPlay));
        $link->setQueryVariable('loop',     $this->booleanToOneOrZero($loop));
        $link->setQueryVariable('egm',      $this->booleanToOneOrZero($genie));
        $link->setQueryVariable('border',   $this->booleanToOneOrZero($border));
        $link->setQueryVariable('fs',       $this->booleanToOneOrZero($fullscreen));
        $link->setQueryVariable('showinfo', $this->booleanToOneOrZero($showInfo));
        
        if ($hq) {
            $link->setQueryVariable('hd', '1');
        }
        
        $link = $link->getURL(true);

        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_DATA_URL,   $link);
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_WIDTH,      $width);
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_HEIGHT,     $height);
        $this->_template->setVariable(org_tubepress_template_Template::EMBEDDED_FULLSCREEN, $this->booleanToString($fullscreen));
        
        $embedSrc = $this->_template->toString();
     
        return $embedSrc;
    }
}
