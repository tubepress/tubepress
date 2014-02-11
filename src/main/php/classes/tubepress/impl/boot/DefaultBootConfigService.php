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
 * Retrieves options for boot from a PHP file.
 */
class tubepress_impl_boot_DefaultBootConfigService implements tubepress_spi_boot_BootConfigService
{
    private static $_TOP_LEVEL_KEY_CLASSLOADER = 'classloader';
    private static $_TOP_LEVEL_KEY_CACHE       = 'cache';
    private static $_TOP_LEVEL_KEY_ADDONS      = 'add-ons';

    private static $_2ND_LEVEL_KEY_CLASSLOADER_ENABLED = 'enabled';
    private static $_2ND_LEVEL_KEY_CACHE_INSTANCE      = 'instance';
    private static $_2ND_LEVEL_KEY_CACHE_KILLERKEY     = 'killerKey';
    private static $_2ND_LEVEL_KEY_ADDONS_BLACKLIST    = 'blacklist';

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
    private $_bootConfig = array();

    /**
     * @return array An array of names of add-ons that have been blacklisted.
     */
    public function getAddonBlacklistArray()
    {
        $this->_init();

        return $this->_bootConfig[self::$_TOP_LEVEL_KEY_ADDONS][self::$_2ND_LEVEL_KEY_ADDONS_BLACKLIST];
    }

    /**
     * @return bool True if classloader registration is enabled.
     */
    public function isClassLoaderEnabled()
    {
        $this->_init();

        return $this->_bootConfig[self::$_TOP_LEVEL_KEY_CLASSLOADER][self::$_2ND_LEVEL_KEY_CLASSLOADER_ENABLED];
    }

    /**
     * @return ehough_stash_interfaces_PoolInterface A functioning boot cache.
     */
    public function getBootCache()
    {
        $this->_init();

        return $this->_bootConfig[self::$_TOP_LEVEL_KEY_CACHE][self::$_2ND_LEVEL_KEY_CACHE_INSTANCE];
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

        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Default Boot Config Service');

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
        $bootConfigFile       = $userContentDirectory . '/config/boot.php';

        /**
         * The user has their own boot config file.
         */
        if (is_readable($bootConfigFile)) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Candidate boot config file at %s', $bootConfigFile));
            }

            try {

                /**
                 * Turn on output buffering to capture any accidental output from the boot file.
                 */
                ob_start();
                /** @noinspection PhpIncludeInspection */
                $config = include $bootConfigFile;
                $output = ob_get_clean();

                $this->_validateConfig($config, $output);
                $this->_bootConfig = $config;
                return;

            } catch (Exception $e) {

                if ($this->_shouldLog) {

                    $this->_logger->warning(sprintf('Could not read boot config file from %s: %s',
                        $bootConfigFile, $e->getMessage()));
                }
            }
        }

        $defaultBootConfigFile = TUBEPRESS_ROOT . '/src/main/resources/user-content-skeleton/tubepress-content/config/boot.php';

        if ($this->_shouldLog) {

            $this->_logger->warning(sprintf('Falling back to default boot config file at %s', $defaultBootConfigFile));
        }

        /** @noinspection PhpIncludeInspection */
        $this->_bootConfig = include $defaultBootConfigFile;
    }

    private function _validateConfig($bootConfig, $output)
    {
        if (trim($output) != '') {

            throw new RuntimeException('Boot config produced printable output.');
        }

        if (!is_array($bootConfig)) {

            throw new RuntimeException('Boot config is not an array. Did you forget to add a return statement to your boot.php?');
        }

        $expectedKeys = array(

            self::$_TOP_LEVEL_KEY_ADDONS      => array(self::$_2ND_LEVEL_KEY_ADDONS_BLACKLIST),
            self::$_TOP_LEVEL_KEY_CACHE       => array(self::$_2ND_LEVEL_KEY_CACHE_INSTANCE, self::$_2ND_LEVEL_KEY_CACHE_KILLERKEY),
            self::$_TOP_LEVEL_KEY_CLASSLOADER => array(self::$_2ND_LEVEL_KEY_CLASSLOADER_ENABLED),
        );

        foreach ($expectedKeys as $topLevel => $keys) {

            if (!isset($bootConfig[$topLevel])) {

                throw new RuntimeException(sprintf('Boot config is missing "%s" configuration.', $topLevel));
            }

            if (!is_array($bootConfig[$topLevel])) {

                throw new RuntimeException(sprintf('Boot config "%s" configuration is not an array', $topLevel));
            }

            foreach ($expectedKeys[$topLevel] as $key) {

                if (!isset($bootConfig[$topLevel][$key])) {

                    throw new RuntimeException(sprintf('Boot config "%s" configuration is missing "%s" key', $topLevel, $key));
                }
            }
        }

        $killerKey = $bootConfig[self::$_TOP_LEVEL_KEY_CACHE][self::$_2ND_LEVEL_KEY_CACHE_KILLERKEY];
        if (!is_string($killerKey)) {

            throw new RuntimeException('Boot config cache killer key is not a string');
        }

        $cache = $bootConfig[self::$_TOP_LEVEL_KEY_CACHE][self::$_2ND_LEVEL_KEY_CACHE_INSTANCE];
        if (!($cache instanceof ehough_stash_interfaces_PoolInterface)) {

            throw new RuntimeException('Boot config did not provide an instance of ehough_stash_interfaces_PoolInterface');
        }

        $blacklist = $bootConfig[self::$_TOP_LEVEL_KEY_ADDONS][self::$_2ND_LEVEL_KEY_ADDONS_BLACKLIST];
        if (!is_array($blacklist)) {

            throw new RuntimeException('Add-ons blacklist is not an array');
        }

        if (!is_bool($bootConfig[self::$_TOP_LEVEL_KEY_CLASSLOADER][self::$_2ND_LEVEL_KEY_CLASSLOADER_ENABLED])) {

            throw new RuntimeException('Classloader enablement is not boolean.');
        }
    }

    private function _clearCacheIfRequested()
    {
        $killer = $this->_bootConfig[self::$_TOP_LEVEL_KEY_CACHE][self::$_2ND_LEVEL_KEY_CACHE_KILLERKEY];

        if (isset($_GET[$killer]) && $_GET[$killer] === 'true') {

            $this->getBootCache()->flush();
        }
    }
}
