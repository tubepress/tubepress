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
 * Handles loading add-ons into the system.
 */
class tubepress_impl_boot_DefaultAddonBooter implements tubepress_spi_boot_AddonBooter
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
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Default Add-on Booter');
    }

    /**
     * Loads the given add-on into the system. This consists of including any defined
     * bootstrap files, then calling boot() on any bootstrap services and classes.
     *
     * If errors are encountered, the loader will record them and make a best effort to continue
     * loading the add-on.
     *
     * @param array $addons An array of tubepress_spi_addon_Addon instances.
     *
     * @return mixed An array of string error messages encountered while loading the
     *               add-ons. May be empty, never null.
     */
    public function boot(array $addons)
    {
        $index            = 1;
        $count            = count($addons);
        $this->_shouldLog = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        /**
         * @var $addon tubepress_spi_addon_Addon
         */
        foreach ($addons as $addon) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Attempting to boot add-on %d of %d: %s',
                    $index, $count, $addon->getName()));
            }

            $this->_boot($addon);

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Done attempting to boot add-on %d of %d: %s',
                    $index, $count, $addon->getName()));
            }

            $index++;
        }
    }

    private function _boot(tubepress_spi_addon_Addon $addon)
    {
        $files    = $addon->getBootstrapFiles();
        $services = $addon->getBootstrapServices();
        $classes  = $addon->getBootstrapClasses();

        if ($this->_shouldLog) {

            if (count($files) > 0) {

                $this->_logger->debug(sprintf('%s add-on defined boot files: %s', $addon->getName(), json_encode($files)));
            }

            if (count($services) > 0) {

                $this->_logger->debug(sprintf('%s add-on defined boot services: %s', $addon->getName(), json_encode($services)));
            }

            if (count($classes) > 0) {

                $this->_logger->debug(sprintf('%s add-on defined boot classes: %s', $addon->getName(), json_encode($classes)));
            }
        }

        foreach ($files as $file) {

            $this->_includeFile($file, $addon);
        }

        foreach ($classes as $class) {

            $this->_bootClass($class, $addon);
        }

        foreach ($services as $service) {

            $this->_bootService($service, $addon);
        }
    }

    private function _bootService($service, tubepress_spi_addon_Addon $addon)
    {
        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Now loading service %s for add-on %s', $service, $addon->getName()));
        }

        try {

            $serviceInstance = tubepress_impl_patterns_sl_ServiceLocator::getService($service);

            if (!$serviceInstance) {

                $this->_logWarning(sprintf('%s is not a registered service', $service));

                return;
            }

            if (!$this->_hasValidBootMethod(new ReflectionClass(get_class($serviceInstance)))) {

                return;
            }

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Now calling boot() on %s for add-on %s', $service, $addon->getName()));
            }

            $serviceInstance->boot();

        } catch (Exception $e) {

            $this->_logWarning(sprintf('Caught exception when calling boot() on %s for add-on %s: %s',
                $service, $addon->getName(), $e->getMessage()));
        }
    }

    private function _bootClass($class, tubepress_spi_addon_Addon $addon)
    {
        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Now loading class %s for add-on %s', $class, $addon->getName()));
        }

        try {

            $ref = new ReflectionClass($class);

            if (!$ref->isInstantiable()) {

                $this->_logWarning(sprintf('%s is not instantiable', $class));

                return;
            }

            if (!$this->_hasValidBootMethod($ref)) {

                return;
            }

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Now calling boot() on %s for add-on %s', $class, $addon->getName()));
            }

            $instance = $ref->newInstance();

            $instance->boot();

        } catch (Exception $e) {

            $this->_logWarning(sprintf('Caught exception when calling boot() on %s for add-on %s: %s',
                $class, $addon->getName(), $e->getMessage()));
        }

    }

    private function _includeFile($file, tubepress_spi_addon_Addon $addon)
    {
        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Now including file %s for add-on %s', $file, $addon->getName()));
        }

        if (!is_file($file) || !is_readable($file)) {

            $this->_logWarning(sprintf('%s is not a readable file', $file));

            return;
        }

        try {

            /** @noinspection PhpIncludeInspection */
            include $file;

        } catch (Exception $e) {

            $this->_logWarning(sprintf('Failed to include %s: %s', $file, $e->getMessage()));
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Done including file %s for add-on %s', $file, $addon->getName()));
        }
    }

    private function _hasValidBootMethod(ReflectionClass $class)
    {
        if (!$class->hasMethod('boot')) {

            $this->_logWarning(sprintf('%s has no boot() method', $class->getName()));

            return false;
        }

        $method = $class->getMethod('boot');

        if (!$method->isPublic()) {

            $this->_logWarning(sprintf('%s\'s boot() method is not public', $class->getName()));

            return false;
        }

        return true;
    }

    private function _logWarning($message)
    {
        if (!$this->_shouldLog) {

            return;
        }

        $this->_logger->warn($message);
    }
}
