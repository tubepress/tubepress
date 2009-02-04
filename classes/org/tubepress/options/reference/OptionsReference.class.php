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
 * The master reference for TubePress options - their names, deprecated
 * names, default values, types, etc.
 *
 */
interface org_tubepress_options_reference_OptionsReference
{   
    function getAdvancedOptionNames();
    
    function getAllOptionNames();
    
    function getDefaultValue($optionName);
    
    function getDisplayOptionNames();
    
    function getEmbeddedOptionNames();
    
    function getFeedOptionNames();
    
    function getGalleryOptionNames();
    
    function getOptionCategoryNames();
    
    function getMetaOptionNames();
    
    function getType($optionName);
    
    function getValidEnumValues($optionName);
    
    function getWidgetOptionNames();
    
    function isOptionName($candidateOptionName);
}
