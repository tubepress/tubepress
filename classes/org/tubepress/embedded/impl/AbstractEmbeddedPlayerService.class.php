<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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

/**
 * Represents an HTML-embeddable YouTube player
 *
 */
abstract class org_tubepress_embedded_impl_AbstractEmbeddedPlayerService implements org_tubepress_embedded_impl_EmbeddedPlayerService
{
    protected $_color1      = "";
    protected $_color2      = "";
    protected $_showRelated = false;
    protected $_showInfo    = false;
    protected $_autoPlay    = false;
    protected $_fullscreen  = true;
    protected $_loop        = false;
    protected $_genie       = false;
    protected $_border      = false;
    protected $_id          = "";
    protected $_width       = 425;
    protected $_height      = 355;
    protected $_quality     = "normal";
    
    /**
     * Applies options from a org_tubepress_options_manager_OptionsManager
     *
     * @param org_tubepress_video_Video          $vid  The video that this embedded player 
     *                                       will show
     * @param org_tubepress_options_manager_OptionsManager $tpom The options manager
     * 
     * @return void
     */
    public function applyOptions(org_tubepress_video_Video $vid, org_tubepress_options_manager_OptionsManager $tpom)
    {
        $this->_color1 =     $this->_safeColorValue($tpom->get(org_tubepress_options_category_Embedded::PLAYER_COLOR), "999999");
        $this->_color2 =     $this->_safeColorValue($tpom->get(org_tubepress_options_category_Embedded::PLAYER_HIGHLIGHT), "FFFFFF");
        $this->_showRelated = $tpom->get(org_tubepress_options_category_Embedded::SHOW_RELATED);
        $this->_autoPlay    = $tpom->get(org_tubepress_options_category_Embedded::AUTOPLAY);
        $this->_loop        = $tpom->get(org_tubepress_options_category_Embedded::LOOP);
        $this->_genie       = $tpom->get(org_tubepress_options_category_Embedded::GENIE);
        $this->_border      = $tpom->get(org_tubepress_options_category_Embedded::BORDER);
        $this->_id          = $vid->getId();
        $this->_width       = $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_WIDTH);
        $this->_height      = $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT);
        $this->_quality     = $tpom->get(org_tubepress_options_category_Embedded::QUALITY);
        $this->_fullscreen  = $tpom->get(org_tubepress_options_category_Embedded::FULLSCREEN);
        $this->_showInfo    = $tpom->get(org_tubepress_options_category_Embedded::SHOW_INFO);
    }    
    
    /**
     * Applies options from a string
     *
     * @param string $packed The string containing the options
     * 
     * @return void
     */    
    public function applyOptionsFromPackedString($packed)
    {
        $broken = split(";", $packed);
        
        foreach ($broken as $pair) {
            
            $keyValue = split("=", $pair);
            $value    = $keyValue[1];
            if ($value == "") {
                continue;
            }
            
            switch($keyValue[0]) {
                
            case "c1":
                $this->_color1 = $this->_safeColorValue($value, "999999");
                break;
            case "c2":
                $this->_color2 = $this->_safeColorValue($value, "FFFFFF");
                break;
            case "r":
                $this->_showRelated = $value;
                break;
            case "a":
                $this->_autoPlay = $value;
                break;
            case "l":
                $this->_loop = $value;
                break;
            case "g":
                $this->_genie = $value;
                break;
            case "b":
                $this->_border = $value;
                break;
            case "id":
                $this->_id = $value;
                break;
            case "w":
                $this->_width = $value;
                break;
            case "h":
                $this->_height = $value;
                break;                
            case "q":
                $this->_quality = $value;
                break;
            case "f":
                $this->_fullscreen = $value;
                break;
            case "si":
                $this->_showInfo = $value;
                break;
            }
        }
    }    
    
    /**
     * Packs options from a org_tubepress_options_manager_OptionsManager to a string
     *
     * @param org_tubepress_video_Video          $vid  The video that this embedded 
     *                                       player will show
     * @param org_tubepress_options_manager_OptionsManager $tpom The options manager that will 
     *                                       be packed to a string
     * 
     * @return void
     */    
    public function packOptionsToString(org_tubepress_video_Video $vid, 
        org_tubepress_options_manager_OptionsManager $tpom)
    {
        $opts = array(
            "r" => $tpom->get(org_tubepress_options_category_Embedded::SHOW_RELATED),
            "a" => $tpom->get(org_tubepress_options_category_Embedded::AUTOPLAY),
            "l" => $tpom->get(org_tubepress_options_category_Embedded::LOOP),
            "g" => $tpom->get(org_tubepress_options_category_Embedded::GENIE),
            "b" => $tpom->get(org_tubepress_options_category_Embedded::BORDER),
            "id" => $vid->getId(),
            "w" => $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_WIDTH),
            "h" => $tpom->get(org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT),
            "q" => $tpom->get(org_tubepress_options_category_Embedded::QUALITY),
            "f" => $tpom->get(org_tubepress_options_category_Embedded::FULLSCREEN),
            "c1" => $this->_safeColorValue($tpom->get(org_tubepress_options_category_Embedded::PLAYER_COLOR), "999999"),
            "c2" => $this->_safeColorValue($tpom->get(org_tubepress_options_category_Embedded::PLAYER_HIGHLIGHT), "FFFFFF"),
            "si" => $tpom->get(org_tubepress_options_category_Embedded::SHOW_INFO)
        );
        
        $result = array();
        foreach ($opts as $key => $value) {
            $result[] = $key . "=" . $value;
        }
        return implode(";", $result);
    }
    
    protected function _safeColorValue($candidate, $default)
    {
        $pattern = '/^[0-9a-fA-F]{6}$/';
        if (preg_match($pattern, $candidate) === 1) {
            return $candidate;
        }
        return $default;
    }
}

?>
