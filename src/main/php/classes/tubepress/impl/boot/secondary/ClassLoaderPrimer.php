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
 * Constructs an efficient classloader.
 */
class tubepress_impl_boot_secondary_ClassLoaderPrimer implements tubepress_spi_boot_secondary_ClassLoaderPrimerInterface
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog = false;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Class Loader Primer');
    }

    /**
     * Load the rest of the default classmap into this classloader.
     *
     * @param ehough_pulsar_ComposerClassLoader $classLoader
     */
    public function prime(ehough_pulsar_ComposerClassLoader $classLoader)
    {
        $classMapFile = TUBEPRESS_ROOT . '/src/main/php/scripts/classmaps/full.php';

        if ($this->_shouldLog) {

            $this->_logger->debug('Now including classmap from ' . $classMapFile);
        }

        /** @noinspection PhpIncludeInspection */
        $classMap = require $classMapFile;

        $classLoader->addToClassMap($classMap);

        if ($this->_shouldLog) {

            $this->_logger->debug('Done including classmap from ' . $classMapFile);
        }
    }

    /**
     * Loads the PSR-0 class paths and any classmaps for this add-on into
     * the system's primary classloader.
     *
     * @param array                             $addons
     * @param ehough_pulsar_ComposerClassLoader $classLoader
     *
     * @return void
     */
    public function addClassHintsForAddons(array $addons, ehough_pulsar_ComposerClassLoader $classLoader)
    {
        if ($this->_shouldLog) {

            $this->_logger->debug('Now registering add-on class hints');
        }

        /**
         * @var $addon tubepress_spi_addon_AddonInterface
         */
        foreach ($addons as $addon) {

            $this->_addClassHints($addon, $classLoader);
        }

        if ($this->_shouldLog) {

            $this->_logger->debug('Done registering add-on class hints.');
        }
    }

    /**
     * Loads the PSR-0 class paths and any classmaps for this add-on into
     * the system's primary classloader.
     *
     * @param tubepress_spi_addon_AddonInterface $addon
     * @param ehough_pulsar_ComposerClassLoader
     *
     * @return void
     */
    private function _addClassHints(tubepress_spi_addon_AddonInterface $addon, ehough_pulsar_ComposerClassLoader $classLoader)
    {
        $this->_shouldLog = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        $this->_registerPsr0ClassPath($addon, $classLoader);
        $this->_registerClassMap($addon, $classLoader);
    }

    private function _registerClassMap(tubepress_spi_addon_AddonInterface $addon, ehough_pulsar_ComposerClassLoader $classLoader)
    {
        $classMap = $addon->getClassMap();

        if (count($classMap) === 0) {

            return;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Add-on %s has a classmap of size %d for the classloader',
                $addon->getName(), count($classMap)));
        }

        $classLoader->addToClassMap($classMap);
    }

    private function _registerPsr0ClassPath(tubepress_spi_addon_AddonInterface $addon, ehough_pulsar_ComposerClassLoader $classLoader)
    {
        $classPaths = $addon->getPsr0ClassPathRoots();

        if (count($classPaths) === 0) {

            return;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Add-on %s has %d PSR-0 path(s) for the classloader',
                $addon->getName(), count($classPaths)));
        }

        foreach ($classPaths as $prefix => $path) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Add-on %s registered %s => %s as a PSR-0 classpath',
                    $addon->getName(), $prefix, $path));
            }

            if ($prefix) {

                $classLoader->registerPrefix($prefix, $path);
                $classLoader->registerNamespace($prefix, $path);

            } else {

                $classLoader->registerNamespaceFallback($path);
                $classLoader->registerPrefixFallback($path);
            }
        }
    }
}