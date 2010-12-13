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
tubepress_load_classes(array('org_tubepress_api_cache_Cache',
    'net_php_pear_Cache_Lite',
    'org_tubepress_util_Log',
    'org_tubepress_impl_filesystem_FsExplorer'));

/**
 * General purpose cache for TubePress
 */
class org_tubepress_impl_cache_PearCacheLiteCacheService implements org_tubepress_api_cache_Cache
{
    private $_cache;
    private $_cachePath;
    private $_logPrefix;

    /**
     * Simple constructor
     */
    public function __construct()
    {
        $this->_logPrefix = "Cache Service";
        $this->_cache     = new net_php_pear_Cache_Lite(array("cacheDir" => sys_get_temp_dir() . '/'));
        $this->_cachePath = $this->_cache->_cacheDir;
    }

    /**
     * Get a value from the cache
     *
     * @param string $key The key of the data to retrieve
     * 
     * @return string The data at the given key, or null if not there
     */
    public function get($key)
    {
        return $this->_cache->get($key);
    }

    /**
     * Determine if the cache has data for a certain key
     *
     * @param string $key The key for which to look
     * 
     * @return boolean True if the cache has the data, false otherwise
     */
    public function has($key)
    {
        $has = $this->_cache->get($key) !== false;
        if ($has) {
            org_tubepress_util_Log::log($this->_logPrefix, 'Cache hit for %s in directory %s', $key, $this->_cachePath);
        } else {
            org_tubepress_util_Log::log($this->_logPrefix, 'Cache miss for %s in directory %s', $key, $this->_cachePath);
        }
        return $has;
    }

    /**
     * Save the given data with the given key
     *
     * @param string $key  The key at which to save the data
     * @param string $data The data to save at the key
     * 
     * @return void
     */
    public function save($key, $data)
    {
        if (!is_string($data)) {
            throw new Exception("Cache can only save string data");
        }
        org_tubepress_util_Log::log($this->_logPrefix, 'Saving data to key at %s', $key);
        $this->_cache->save($data, $key);
    }
}
