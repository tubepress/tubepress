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
 * Retrieves options for boot from a JSON file.
 */
class tubepress_impl_boot_DefaultBootConfigService implements tubepress_spi_boot_BootConfigService
{
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
     * @var array
     */
    private $_bootConfig = array();

    /**
     * @var string
     */
    private $_cachedBootCacheDirectory;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Default Boot Config Service');
    }

    /**
     * @return array An array of names of add-ons that have been blacklisted.
     */
    public function getAddonBlacklistArray()
    {
        $this->_init();

        if (!isset($this->_bootConfig['add-ons']['blacklist'])) {

            return array();
        }

        $blackList = $this->_bootConfig['add-ons']['blacklist'];

        if (!is_array($blackList)) {

            return array();
        }

        return $blackList;
    }

    /**
     * @param string $element The element to look up.
     *
     * @return bool True if caching is enabled for this element, false otherwise.
     */
    public function isCacheEnabledForElement($element)
    {
        $this->_init();

        if (!isset($this->_bootConfig['cache'][$element]['enabled']) || !$this->_bootConfig['cache'][$element]['enabled']) {

            $toReturn = false;

        } else {

            $toReturn = true;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('%s caching is%s enabled', $element, $toReturn ? '' : ' not'));
        }

        return $toReturn;
    }

    /**
     * @return bool True if the cache killer is on, false otherwise.
     */
    public function isCacheKillerTurnedOn()
    {
        $this->_init();

        if (isset($this->_bootConfig['cache']['killer-key'])) {

            $key = (string) $this->_bootConfig['cache']['killer-key'];

        } else {

            $key = 'tubepress_boot_cache_kill';
        }

        return isset($_GET[$key]) && $_GET[$key] === 'true';
    }

    /**
     * @param string $element The element to look up.
     *
     * @return string The absolute path of the element's cache file.
     */
    public function getAbsolutePathToCacheFileForElement($element)
    {
        $this->_init();

        switch ($element) {

            case 'ioc-container':

                return $this->_calculateCacheFilePath('cached-ioc-container.php');

            case 'add-ons':

                return $this->_calculateCacheFilePath('serialized-addons.txt');

            case 'classloader':

                return $this->_calculateCacheFilePath('serialized-classloader.txt');

            case 'option-descriptors':

                return $this->_calculateCacheFilePath('serialized-option-descriptors.txt');

            default:

                throw new InvalidArgumentException('Invalid boot config element: ' . $element);
        }
    }

    /**
     * @return bool True if classloader registration is enabled.
     */
    function isClassLoaderEnabled()
    {
        $this->_init();

        if (isset($this->_bootConfig['classloader']['enabled'])) {

            return (bool) $this->_bootConfig['classloader']['enabled'];
        }

        return true;
    }

    private function _init()
    {
        if ($this->_hasInitialized) {

            return;
        }

        $this->_shouldLog = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        $this->_readConfig();

        $this->_hasInitialized = true;
    }

    private function _readConfig()
    {
        $envDetector          = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $userContentDirectory = $envDetector->getUserContentDirectory();
        $configFileLocation   = $userContentDirectory . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'boot.json';

        if (!is_file($configFileLocation) || !is_readable($configFileLocation)) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('No readable config file at %s', $configFileLocation));
            }

            return;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Attempting to read boot config from %s', $configFileLocation));
        }

        $contents = file_get_contents($configFileLocation);

        if ($contents === false) {

            if ($this->_shouldLog) {

                $this->_logger->warn(sprintf('Failed to read file contents of %s', $configFileLocation));
            }

            return;
        }

        $decoded = @json_decode($contents, true);

        if ($decoded === false || !is_array($decoded)) {

            if ($this->_shouldLog) {

                $this->_logger->warn(sprintf('Failed to parse %s', $configFileLocation));
            }
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Successfully read boot config from %s', $configFileLocation));
        }

        $this->_bootConfig = $decoded;
    }

    private function _calculateCacheFilePath($fileName)
    {
        if (!isset($this->_cachedBootCacheDirectory)) {

            $dir = null;

            if (isset($this->_bootConfig['cache']['dir'])) {

                $dir = $this->_bootConfig['cache']['dir'];
            }

            if (!$dir) {

                /**
                 * The md5 stuff ensures that this cache doesn't clobber over other installations present on the server.
                 */
                $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tubepress-boot-cache-' . md5(realpath(dirname(__FILE__)));
            }

            $this->_cachedBootCacheDirectory = $dir;
        }

        return $this->_cachedBootCacheDirectory . DIRECTORY_SEPARATOR . $fileName;
    }
}
