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
|| require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_cache_CacheService',
    'net_php_pear_Cache_Lite',
    'org_tubepress_log_Log'));

/*
 * thanks to shickm for this...
 * http://code.google.com/p/tubepress/issues/detail?id=27
 */
if (!function_exists("sys_get_temp_dir")) {

    // Based on http://www.phpit.net/
    // article/creating-zip-tar-archives-dynamically-php/2/
    function sys_get_temp_dir()
    {
        // Try to get from environment variable
        if ( !empty($_ENV['TMP']) )
        {
            return realpath( $_ENV['TMP'] );
        }
        else if ( !empty($_ENV['TMPDIR']) )
        {
            return realpath( $_ENV['TMPDIR'] );
        }
        else if ( !empty($_ENV['TEMP']) )
        {
            return realpath( $_ENV['TEMP'] );
        }

        // Detect by creating a temporary file
        else
        {
            // Try to use system's temporary directory
            // as random name shouldn't exist
            $temp_file = tempnam( md5(uniqid(rand(), TRUE)), '' );
            if ( $temp_file )
            {
                $temp_dir = realpath( dirname($temp_file) );
                unlink( $temp_file );
                return $temp_dir;
            }
            else
            {
                return FALSE;
            }
        }
    }
}

/**
 * General purpose cache for TubePress
 */
class org_tubepress_cache_SimpleCacheService implements org_tubepress_cache_CacheService
{
    private $_cache;
    private $_cachePath;
    private $_log;
    private $_logPrefix;

    /**
     * Simple constructor
     *
     */
    public function __construct()
    {
        $this->_logPrefix = "Cache Service";

        $this->_cache = new net_php_pear_Cache_Lite(array("cacheDir" => sys_get_temp_dir() . '/'));
        $this->_cachePath = $this->_cache->_cacheDir;
    }

    /**
     * @see org_tubepress_cache_CacheService::get($key)
     */
    public function get($key)
    {
        return $this->_cache->get($key);
    }

    /**
     * @see org_tubepress_cache_CacheService::has($key)
     */
    public function has($key)
    {
        $has = $this->_cache->get($key) !== false;

        if ($has) {
            $this->_log->log($this->_logPrefix, 'Cache hit for %s in directory %s', $key, $this->_cachePath);
        } else {
            $this->_log->log($this->_logPrefix, 'Cache miss for %s in directory %s', $key, $this->_cachePath);
        }

        return $has;
    }

    /**
     * @see org_tubepress_cache_CacheService::save($key, $data)
     */
    public function save($key, $data)
    {
        if (!is_string($data)) {
            throw new Exception("Cache can only save string data");
        }
        $this->_log->log($this->_logPrefix, 'Saving data to key at %s', $key);
        $this->_cache->save($data, $key);
    }

    public function setLog(org_tubepress_log_Log $log)
    {
        $this->_log = $log;
    }
}
