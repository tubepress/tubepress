<?php
/**
 * Copyright 2006, 2007, 2008 Eric D. Hough (http://ehough.com)
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


class WordPressStorageManager extends TubePressStorageManager
{
    protected function setOption($optionName, $optionValue)
    {
        update_option("tubepress-$optionName", $optionValue);
    }
    
    public function get($optionName)
    {
        return get_option("tubepress-$optionName");
    }
    
    protected function delete($optionName)
    {
        delete_option($optionName);
    }
    
    protected function create($optionName, $optionValue)
    {
        add_option("tubepress-$optionName", $optionValue);
    }
    
    public function exists($optionName)
    {
        return get_option("tubepress-$optionName") !== FALSE;
    }
}
?>
