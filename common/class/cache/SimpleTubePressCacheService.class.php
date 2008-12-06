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

/**
 * General purpose cache for TubePress
 */
class SimpleTubePressCacheService implements TubePressCacheService
{
	private $_cache;
	
	public function __construct()
	{
		/* 
		 * thanks to shickm for this...
		 * http://code.google.com/p/tubepress/issues/detail?id=27
		*/
		function_exists("sys_get_temp_dir")
    		|| require dirname(__FILE__) . "/../../../lib/sys_get_temp_dir.php";
		
		$this->_cache = new Cache_Lite(array("cacheDir" => sys_get_temp_dir()));
	}
	
	public function get($key)
	{
		return $this->_cache->get($key);
	}
	
	public function has($key)
	{
		return $this->_cache->get($key) !== false;
	}
	
	public function save($key, $data)
	{
		$this->_cache->save($data, $key);
	}
}
