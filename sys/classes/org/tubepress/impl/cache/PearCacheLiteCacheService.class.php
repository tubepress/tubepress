<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_cache_Cache',
    'org_tubepress_api_const_options_names_Cache',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_impl_filesystem_FsExplorer',
    'org_tubepress_impl_log_Log',
));

class org_tubepress_impl_cache_PearCacheLiteCacheService implements org_tubepress_api_cache_Cache
{
    const HASH_DIR_LEVEL = 1;
    const HASH_DIR_UMASK = 0700;

    const LOG_PREFIX = 'Cache_Lite Cache';

    /**
     * Test if a cache is available and (if yes) return it
     *
     * @param string $id cache id
     *
     * @return string data of the cache (else : false)
     */
    public function get($id)
    {
        try {
            return $this->_wrappedGet($id);
        } catch (Exception $e) {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Caught exception when running "get": ' . $e->getMessage());
            return false;
        }
    }

    private function _wrappedGet($id)
    {
        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context     = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $life        = $context->get(org_tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS);
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
     * @param string $id   cache id
     * @param string $data data to put in cache
     *
     * @return boolean true if no problem (else : false)
     */
    public function save($id, $data)
    {
        try {
            return $this->_wrappedSave($id, $data);
        } catch (Exception $e) {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Caught exception when saving: ' . $e->getMessage());
            return false;
        }
    }

    private function _wrappedSave($id, $data)
    {
        if (!is_string($data)) {
            throw new Exception("Cache can only save string data");
        }

        $ioc            = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context        = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $life           = $context->get(org_tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS);
        $cleaningFactor = $context->get(org_tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR);
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

    /**
     * Should only be called during testing!
     *
     * @return void
     */
    public function clean()
    {
        $this->_cleanDir($this->_getCacheDir(org_tubepress_impl_ioc_IocContainer::getInstance()));
    }

    /**
     * Compute & set the refresh time
     *
     * @param int $life The current cache lifetime.
     *
     * @return int The Unix time when a cache item must be refreshed.
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
     *
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
     *
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

                    $fileTwo = $dir . $file;

                    if (is_file($fileTwo)) {

                        if (strpos($fileTwo, $motif) !== false) {
                            $result = ($result and ($this->_unlink($fileTwo)));
                        }
                    }

                    if ((is_dir($fileTwo)) and (self::HASH_DIR_LEVEL > 0)) {
                        $result = ($result and ($this->_cleanDir($fileTwo . '/')));
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

    private function _writeAndControl($id, $data, $life, $file, $ioc)
    {
        $result   = $this->_write($id, $data, $ioc);
        $dataRead = $this->_read($file, $life);

        if ((is_bool($dataRead)) && (!$dataRead)) {
            return false;
        }

        return $dataRead == $data;
    }

    private function _hash($data)
    {
        return sprintf('% 32d', crc32($data));
    }

    private function _getCacheDir($ioc)
    {
        $context  = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $cacheDir = $context->get(org_tubepress_api_const_options_names_Cache::CACHE_DIR);

        if ($cacheDir != '') {
            return $cacheDir;
        }

        $fs = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $tempDir = $fs->getSystemTempDirectory();

        if (!is_dir($tempDir)) {
            throw new Exception('Could not determine location of system temp directory');
        }
        return $tempDir . '/tubepress_cache/';
    }
}
