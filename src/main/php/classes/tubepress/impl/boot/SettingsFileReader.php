<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Retrieves settings from a PHP file.
 */
class tubepress_impl_boot_SettingsFileReader implements tubepress_spi_boot_SettingsFileReaderInterface
{
    private static $_TOP_LEVEL_KEY_SYSTEM = 'system';
    
    private static $_2ND_LEVEL_KEY_CLASSLOADER = 'classloader';
    private static $_2ND_LEVEL_KEY_CACHE       = 'cache';
    private static $_2ND_LEVEL_KEY_ADDONS      = 'add-ons';

    private static $_3RD_LEVEL_KEY_CLASSLOADER_ENABLED = 'enabled';
    private static $_3RD_LEVEL_KEY_CACHE_KILLERKEY     = 'killerKey';
    private static $_3RD_LEVEL_KEY_CACHE_ENABLED       = 'enabled';
    private static $_3RD_LEVEL_KEY_CACHE_CSP           = 'containerStoragePath';
    private static $_3RD_LEVEL_KEY_ADDONS_BLACKLIST    = 'blacklist';

    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_hasInitialized = false;

    /**
     * @var bool
     */
    private $_shouldLog = false;

    /**
     * @var tubepress_spi_environment_EnvironmentDetector
     */
    private $_environmentDetector;

    /**
     * @var array
     */
    private $_addonBlacklistArray = array();

    /**
     * @var boolean
     */
    private $_isClassLoaderEnabled;

    /**
     * @var boolean
     */
    private $_isCacheEnabled;

    /**
     * @var string
     */
    private $_systemCacheKillerKey;

    /**
     * @var string
     */
    private $_containerStoragePath;

    /**
     * @return bool True if the cache killer key has been set by the user.
     */
    public function shouldClearCache()
    {
        $this->_init();

        return isset($_GET[$this->_systemCacheKillerKey]) && $_GET[$this->_systemCacheKillerKey] === 'true';
    }

    /**
     * @return array An array of names of add-ons that have been blacklisted.
     */
    public function getAddonBlacklistArray()
    {
        $this->_init();

        return $this->_addonBlacklistArray;
    }

    /**
     * @return bool True if classloader registration is enabled.
     */
    public function isClassLoaderEnabled()
    {
        $this->_init();

        return $this->_isClassLoaderEnabled;
    }

    /**
     * @return bool True if the container cache is enabled. False otherwise.
     */
    public function isContainerCacheEnabled()
    {
        $this->_init();

        return $this->_isCacheEnabled;
    }

    /**
     * @return string An absolute path on the filesystem where TubePress can store
     *                the compiled service container.
     */
    public function getCachedContainerStoragePath()
    {
        $this->_init();

        return $this->_containerStoragePath;
    }

    /**
     * This function should NOT be use outside of testing.
     *
     * @param tubepress_spi_environment_EnvironmentDetector $environmentDetector
     */
    public function __setEnvironmentDetector(tubepress_spi_environment_EnvironmentDetector $environmentDetector)
    {
        $this->_environmentDetector = $environmentDetector;
    }

    private function _init()
    {
        if ($this->_hasInitialized) {

            return;
        }

        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Settings File Service');

        $this->_shouldLog = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        $this->_readConfig();

        $this->_hasInitialized = true;

        $this->_clearCacheIfRequested();
    }

    private function _readConfig()
    {
        if (!isset($this->_environmentDetector)) {

            $this->_environmentDetector = new tubepress_impl_environment_SimpleEnvironmentDetector();
        }

        $userContentDirectory = $this->_environmentDetector->getUserContentDirectory();
        $userSettingsFilePath = $userContentDirectory . '/config/settings.php';
        $configArray          = array();

        /**
         * The user has their own settings.php.
         */
        if (is_readable($userSettingsFilePath)) {

            $configArray = $this->_readUserConfig($userSettingsFilePath);
        }

        $this->_mergeConfig($configArray);
    }

    private function _readUserConfig($settingsFilePath)
    {
        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Candidate settings.php at %s', $settingsFilePath));
        }

        try {

            /**
             * Turn on output buffering to capture any accidental output from the settings file.
             */
            ob_start();

            /** @noinspection PhpIncludeInspection */
            $configArray = include $settingsFilePath;

            ob_end_clean();

            if (!is_array($configArray)) {

                throw new RuntimeException('settings.php did not return an array of config values.');
            }

            return $configArray;

        } catch (Exception $e) {

            if ($this->_shouldLog) {

                $this->_logger->warning(sprintf('Could not read settings.php from %s: %s',
                    $settingsFilePath, $e->getMessage()));
            }
        }

        return array();
    }

    private function _mergeConfig(array $config)
    {
        $this->_addonBlacklistArray  = $this->_getAddonBlacklistArray($config);
        $this->_isClassLoaderEnabled = $this->_getClassLoaderEnablement($config);
        $this->_systemCacheKillerKey = $this->_getCacheKillerKey($config);
        $this->_containerStoragePath = $this->_getContainerStoragePath($config);
        $this->_isCacheEnabled       = $this->_getContainerCacheEnablement($config);
    }

    private function _getContainerStoragePath(array $config)
    {
        if (!$this->_isAllSet($config, self::$_TOP_LEVEL_KEY_SYSTEM, self::$_2ND_LEVEL_KEY_CACHE,
            self::$_3RD_LEVEL_KEY_CACHE_CSP)) {

            return $this->_getFilesystemCacheDirectory() . 'tubepress-service-container.php';
        }

        $path = $config[self::$_TOP_LEVEL_KEY_SYSTEM][self::$_2ND_LEVEL_KEY_CACHE][self::$_3RD_LEVEL_KEY_CACHE_CSP];

        if (is_dir($path) && is_writable($path)) {

            return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'tubepress-service-container.php';
        }

        $parentDir = dirname($path);

        if ($parentDir === '.' || !is_dir($parentDir) || !is_writable($parentDir)) {

            return $this->_getFilesystemCacheDirectory() . 'tubepress-service-container.php';
        }

        return $path;
    }

    private function _getAddonBlacklistArray(array $config)
    {
        $default = array();

        if (!$this->_isAllSet($config, self::$_TOP_LEVEL_KEY_SYSTEM, self::$_2ND_LEVEL_KEY_ADDONS,
            self::$_3RD_LEVEL_KEY_ADDONS_BLACKLIST)) {

            return $default;
        }

        $blackList = $config[self::$_TOP_LEVEL_KEY_SYSTEM][self::$_2ND_LEVEL_KEY_ADDONS]
            [self::$_3RD_LEVEL_KEY_ADDONS_BLACKLIST];

        if (!is_array($blackList)) {

            return $default;
        }

        return array_values($blackList);
    }

    private function _getClassLoaderEnablement(array $config)
    {
        $default = true;

        if (!$this->_isAllSet($config, self::$_TOP_LEVEL_KEY_SYSTEM, self::$_2ND_LEVEL_KEY_CLASSLOADER,
            self::$_3RD_LEVEL_KEY_CLASSLOADER_ENABLED)) {

            return $default;
        }

        $enabled = $config[self::$_TOP_LEVEL_KEY_SYSTEM][self::$_2ND_LEVEL_KEY_CLASSLOADER]
        [self::$_3RD_LEVEL_KEY_CLASSLOADER_ENABLED];

        if (!is_bool($enabled)) {

            return $default;
        }

        return (boolean) $enabled;
    }

    private function _getContainerCacheEnablement(array $config)
    {
        $default = true;

        if (!$this->_isAllSet($config, self::$_TOP_LEVEL_KEY_SYSTEM, self::$_2ND_LEVEL_KEY_CLASSLOADER,
            self::$_3RD_LEVEL_KEY_CACHE_ENABLED)) {

            return $default;
        }

        $enabled = $config[self::$_TOP_LEVEL_KEY_SYSTEM][self::$_2ND_LEVEL_KEY_CLASSLOADER]
            [self::$_3RD_LEVEL_KEY_CACHE_ENABLED];

        if (!is_bool($enabled)) {

            return $default;
        }

        return (boolean) $enabled;
    }

    private function _getCacheKillerKey(array $config)
    {
        $default = 'tubepress_clear_system_cache';

        if (!$this->_isAllSet($config, self::$_TOP_LEVEL_KEY_SYSTEM, self::$_2ND_LEVEL_KEY_CACHE,
            self::$_3RD_LEVEL_KEY_CACHE_KILLERKEY)) {

            return $default;
        }

        $key = $config[self::$_TOP_LEVEL_KEY_SYSTEM][self::$_2ND_LEVEL_KEY_CACHE]
            [self::$_3RD_LEVEL_KEY_CACHE_KILLERKEY];

        if (!is_string($key) || $key == '') {

            return $default;
        }

        return $key;
    }

    private function _isAllSet(array $arr, $topLevel, $secondLevel, $thirdLevel)
    {
        if (!isset($arr[$topLevel])) {

            return false;
        }

        if (!isset($arr[$topLevel][$secondLevel])) {

            return false;
        }

        if (!isset($arr[$topLevel][$secondLevel][$thirdLevel])) {

            return false;
        }

        return true;
    }

    private function _clearCacheIfRequested()
    {
        if (!$this->shouldClearCache()) {

            return;
        }

        $path = $this->getCachedContainerStoragePath();

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Cache clearing requested. Attempting to delete %s', $path));
        }

        $result = @unlink($path) === true;

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Deleted %s ? %s', $path, $result ? 'yes' : 'no'));
        }
    }

    private function _getFilesystemCacheDirectory()
    {
        if (function_exists('sys_get_temp_dir')) {

            $tmp = rtrim(sys_get_temp_dir(), '/\\') . '/';

        } else {

            $tmp = '/tmp/';
        }

        $baseDir = $tmp . 'tubepress-container-cache/' . md5(dirname(__FILE__)) . '/';

        if (!is_dir($baseDir)) {

            @mkdir($baseDir, 0770, true);
        }

        if (!is_writable($baseDir)) {

            return null;
        }

        return $baseDir;
    }
}
