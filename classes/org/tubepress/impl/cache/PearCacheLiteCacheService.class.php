<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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
 *
 * Fast, light and safe Cache Class lifted almost entirely from PEAR's Cache_Lite class.
 *
 * Cache_Lite is a fast, light and safe cache system. It's optimized
 * for file containers. It is fast and safe (because it uses file
 * locking and/or anti-corruption tests).
 *
 */

function_exists('tubepress_load_classes')
|| require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_cache_Cache',
    'org_tubepress_impl_log_Log',
    'org_tubepress_impl_filesystem_FsExplorer'));

class org_tubepress_impl_cache_PearCacheLiteCacheService implements org_tubepress_api_cache_Cache
{
    const HASH_DIR_LEVEL = 1;
    const HASH_DIR_UMASK = 0700;

    const LOG_PREFIX = 'Cache_Lite Cache';

    /**
     * Determine if the cache has data for a certain key
     *
     * @param string $key The key for which to look
     * 
     * @return boolean True if the cache has the data, false otherwise
     */
    public function has($key)
    {
        $has = $this->get($key) !== false;
        if ($has) {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Cache hit for %s', $key);
        } else {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Cache miss for %s', $key);
        }
        return $has;
    }

    /**
     * Test if a cache is available and (if yes) return it
     *
     * @param string $id cache id
     * @return string data of the cache (else : false)
     */
    public function get($id)
    {
        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tpom        = $ioc->get('org_tubepress_api_options_OptionsManager');
        $life        = $tpom->get(org_tubepress_api_const_options_Advanced::CACHE_LIFETIME_SECONDS);
        $data        = false;
        $refreshTime = $this->_getRefreshTime($life);
        $file        = $this->_getFileWithPath($id, $ioc);

        clearstatcache();

        if (is_null($refreshTime)) {
            if (file_exists($file)) {
                $data = $this->_read($file, $life);
            }
        } else {
            if ((file_exists($file)) && (@filemtime($file) > $refreshTime)) {
                $data = $this->_read($file, $life);
            }
        }

        return $data;
    }

    /**
     * Save some data in a cache file
     *
     * @param string $data data to put in cache
     * @param string $id cache id
     * @return boolean true if no problem (else : false or throws an Exception)
     */
    public function save($id, $data)
    {
        if (!is_string($data)) {
            throw new Exception("Cache can only save string data");
        }

        $ioc            = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tpom           = $ioc->get('org_tubepress_api_options_OptionsManager');
        $life           = $tpom->get(org_tubepress_api_const_options_Advanced::CACHE_LIFETIME_SECONDS);
        $cleaningFactor = $tpom->get(org_tubepress_api_const_options_Advanced::CACHE_CLEAN_FACTOR);
        $file           = $this->_getFileWithPath($id, $ioc);

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Saving data to key at %s and file %s. Cleaning factor is %d', $id, $file, $cleaningFactor);

        if ($cleaningFactor > 0) {
            $rand = rand(1, $cleaningFactor);
            if ($rand == 1) {

                org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Cleaning out old cache entries');
                $this->_cleanDir($this->_getCacheDir($ioc));
            }
        }

        $res = $this->_writeAndControl($id, $data, $life, $file, $ioc);

        if (is_bool($res)) {
            if ($res) {
                org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Saved data to key at %s', $id);
                return true;
            }
            // if $res if false, we need to invalidate the cache
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Invalidating cache under key at %s', $id);
            @touch($file, time() - 2 * abs($life));
            return false;
        }

        return $res;
    }

    //should only be called during testing...
    public function clean()
    {
        $this->_cleanDir($this->_getCacheDir(org_tubepress_impl_ioc_IocContainer::getInstance()));
    }

    /**
     * Compute & set the refresh time
     *
     */
    private function _getRefreshTime($life)
    {
        if ($life === 0) {
            return null;
        } else {
            return time() - $life;
        }
    }

    /**
     * Remove a file
     *
     * @param string $file complete file path and name
     * @return boolean true if no problem
     */
    private function _unlink($file)
    {
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Removing file at %s', $file);
        if (!@unlink($file)) {
            throw new Exception('Cache_Lite : Unable to remove cache file at ' . $file);
        }
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Removed file at %s', $file);
        return true;
    }

    /**
     * Recursive function for cleaning cache file in the given directory
     *
     * @param string $dir directory complete path (with a trailing slash)
     * @return boolean true if no problem
     */
    private function _cleanDir($dir)
    {
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Removing cache files under directory at %s', $dir);
        $motif = 'cache_';

        if (!($dh = @opendir($dir))) {
            return;
        }

        $result = true;

        while ($file = readdir($dh)) {

            if (($file != '.') && ($file != '..')) {

                if (substr($file, 0, 6) == 'cache_') {

                    $file2 = $dir . $file;

                    if (is_file($file2)) {

                        if (strpos($file2, $motif) !== false) {
                            $result = ($result and ($this->_unlink($file2)));
                        }
                    }

                    if ((is_dir($file2)) and (self::HASH_DIR_LEVEL > 0)) {
                        $result = ($result and ($this->_cleanDir($file2 . '/')));
                    }
                }
            }
        }

        return $result;
    }

    private function _getFileWithoutPath($id)
    {
        return 'cache_' . md5($id);
    }

    /**
     * Make a file name (with path)
     *
     * @param string $id cache id
     */
    private function _getFileWithPath($id, $ioc)
    {
        $suffix = $this->_getFileWithoutPath($id);
        $root   = $this->_getCacheDir($ioc);

        if (self::HASH_DIR_LEVEL > 0) {

            $hash = md5($suffix);

            for ($i=0 ; $i < self::HASH_DIR_LEVEL ; $i++) {
                $root = $root . 'cache_' . substr($hash, 0, $i + 1) . '/';
            }
        }

        return $root . $suffix;
    }

    /**
     * Read the cache file and return the content
     *
     * @return string content of the cache file (else : false)
     */
    private function _read($file, $life)
    {
        $fp = @fopen($file, "rb");
        @flock($fp, LOCK_SH);
        if (!$fp) {
            throw new Exception('Unable to read cache file at ' . $file);
        }

        clearstatcache();

        $length      = @filesize($file);
        $hashControl = @fread($fp, 32);
        $length      = $length - 32;

        if ($length) {
            $data = @fread($fp, $length);
        } else {
            $data = '';
        }

        @flock($fp, LOCK_UN);
        @fclose($fp);

        $hashData = $this->_hash($data);

        if ($hashData != $hashControl) {

            if (!(is_null($life))) {
                @touch($file, time() - 2*abs($life));
            } else {
                @unlink($file);
            }
            return false;
        }
        return $data;
    }

    /**
     * Write the given data in the cache file
     *
     * @param string $data data to put in cache
     * @return boolean true if ok (a PEAR_Error object else)
     */
    private function _write($id, $data, $ioc)
    {
        $file     = $this->_getFileWithPath($id, $ioc);
        $cacheDir = $this->_getCacheDir($ioc);

        if (self::HASH_DIR_LEVEL > 0) {

            $hash = md5($file);
            $root = $cacheDir;

            for ($i=0 ; $i < self::HASH_DIR_LEVEL ; $i++) {

                $root = $root . 'cache_' . substr($hash, 0, $i + 1) . '/';

                if (!(@is_dir($root))) {
                    @mkdir($root, self::HASH_DIR_UMASK, true);
                }
            }
        }

        $dir = dirname($file);
        if (!@is_dir($dir)) {
            @mkdir($dir, self::HASH_DIR_UMASK, true);
        }

        $fp = @fopen($file, "wb");

        if (!$fp) {
            throw new Exception('Unable to write cache file : ' . $file);
        }

        @flock($fp, LOCK_EX);
        @fwrite($fp, $this->_hash($data), 32);
        @fwrite($fp, $data);
        @flock($fp, LOCK_UN);
        @fclose($fp);

        return true;
    }


    /**
     * Write the given data in the cache file and control it just after to avoir corrupted cache entries
     *
     * @param string $data data to put in cache
     * @return boolean true if the test is ok (else : false or a PEAR_Error object)
     */
    private function _writeAndControl($id, $data, $life, $file, $ioc)
    {
        $result   = $this->_write($id, $data, $ioc);
        $dataRead = $this->_read($file, $life);

        if ((is_bool($dataRead)) && (!$dataRead)) {
            return false;
        }

        return $dataRead == $data;
    }

    /**
     * Make a control key with the string containing datas
     *
     * @param string $data data
     *
     * @return string control key
     */
    private function _hash($data)
    {
        return sprintf('% 32d', crc32($data));
    }

    private function _getCacheDir($ioc)
    {
        $tpom     = $ioc->get('org_tubepress_api_options_OptionsManager');
        $cacheDir = $tpom->get(org_tubepress_api_const_options_Advanced::CACHE_DIR);

        if ($cacheDir != '') {
            return $cacheDir;
        }

        $fs = $ioc->get('org_tubepress_api_filesystem_Explorer');
        return $fs->getSystemTempDirectory() . '/tubepress_cache/';
    }
}
