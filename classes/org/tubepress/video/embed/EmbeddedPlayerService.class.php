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
interface org_tubepress_video_embed_EmbeddedPlayerService
{
    /**
     * Applies options from a org_tubepress_options_manager_OptionsManager
     *
     * @param org_tubepress_video_Video          $vid  The video that this embedded player 
     *                                       will show
     * @param org_tubepress_options_manager_OptionsManager $tpom The options manager
     * 
     * @return void
     */
    public function applyOptions(org_tubepress_video_Video $vid, 
        org_tubepress_options_manager_OptionsManager $tpom);
    
    /**
     * Applies options from a string
     *
     * @param string $packed The string containing the options
     * 
     * @return void
     */
    public function applyOptionsFromPackedString($packed);
    
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
        org_tubepress_options_manager_OptionsManager $tpom);
    
    /**
     * Spits back the text for this embedded player
     *
     * @return string The text for this embedded player
     */
    public function toString();
}
?>
