<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Simple add-on loader.
 */
class tubepress_impl_addon_DefaultAddonLoader implements tubepress_spi_addon_AddonLoader
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Default Add-on Loader');
    }

    /**
     * Loads the given add-on into the system. This consists of including any defined
     * bootstrap files, then calling boot() on any bootstrap services and classes.
     *
     * If errors are encountered, the loader will record them and make a best effort to continue
     * loading the add-on.
     *
     * @param tubepress_spi_addon_Addon $addon
     *
     * @return mixed An array of string error messages encountered while loading the
     *               add-on. May be empty, never null.
     */
    public final function load(tubepress_spi_addon_Addon $addon)
    {
        $files              = $addon->getBootstrapFiles();
        $services           = $addon->getBootstrapServices();
        $classes            = $addon->getBootstrapClasses();
        $isDebuggingEnabled = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);
        $errors             = array();

        if ($isDebuggingEnabled) {

            $this->_logger->debug(sprintf('%s add-on defined %d files, %d services, and %d classes for boot',
                $addon->getName(), count($files), count($services), count($classes)));

            if (count($files) > 0) {

                $this->_logger->debug(sprintf('files: %s', json_encode($files)));
            }

            if (count($services) > 0) {

                $this->_logger->debug(sprintf('services: %s', json_encode($services)));
            }

            if (count($classes) > 0) {

                $this->_logger->debug(sprintf('classes: %s', json_encode($classes)));
            }
        }

        foreach ($files as $file) {

            $this->_includeFile($errors, $file, $addon, $isDebuggingEnabled);
        }

        foreach ($classes as $class) {

            $this->_bootClass($errors, $class, $addon, $isDebuggingEnabled);
        }

        foreach ($services as $service) {

            $this->_bootService($errors, $service, $addon, $isDebuggingEnabled);
        }

        return $errors;
    }

    private function _bootService(&$errors, $service, tubepress_spi_addon_Addon $addon, $isDebuggingEnabled)
    {
        if ($isDebuggingEnabled) {

            $this->_logger->debug(sprintf('Now loading service %s for add-on %s', $service, $addon->getName()));
        }

        try {

            $serviceInstance = tubepress_impl_patterns_sl_ServiceLocator::getService($service);

            if (!$serviceInstance) {

                $this->_logWarning($errors, sprintf('%s is not a registered service', $service));

                return;
            }

            if (!$this->_hasValidBootMethod($errors, new ReflectionClass(get_class($serviceInstance)))) {

                return;
            }

            if ($isDebuggingEnabled) {

                $this->_logger->debug(sprintf('Now calling boot() on %s for add-on %s', $service, $addon->getName()));
            }

            $serviceInstance->boot();

        } catch (Exception $e) {

            $this->_logWarning($errors, sprintf('Caught exception when calling boot() on %s for add-on %s: %s',
                $service, $addon->getName(), $e->getMessage()));
        }
    }

    private function _bootClass(&$errors, $class, tubepress_spi_addon_Addon $addon, $isDebuggingEnabled)
    {
        if ($isDebuggingEnabled) {

            $this->_logger->debug(sprintf('Now loading class %s for add-on %s', $class, $addon->getName()));
        }

        try {

            $ref = new ReflectionClass($class);

            if (!$ref->isInstantiable()) {

                $this->_logWarning($errors, sprintf('%s is not instantiable', $class));

                return;
            }

            if (!$this->_hasValidBootMethod($errors, $ref)) {

                return;
            }

            if ($isDebuggingEnabled) {

                $this->_logger->debug(sprintf('Now calling boot() on %s for add-on %s', $class, $addon->getName()));
            }

            $instance = $ref->newInstance();

            $instance->boot();

        } catch (Exception $e) {

            $this->_logWarning($errors, sprintf('Caught exception when calling boot() on %s for add-on %s: %s',
                $class, $addon->getName(), $e->getMessage()));
        }

    }

    private function _includeFile(&$errors, $file, tubepress_spi_addon_Addon $addon, $isDebuggingEnabled)
    {
        if ($isDebuggingEnabled) {

            $this->_logger->debug(sprintf('Now including file %s for add-on %s', $file, $addon->getName()));
        }

        if (!is_file($file) || !is_readable($file)) {

            $this->_logWarning($errors, sprintf('%s is not a readable file', $file));

            return;
        }

        try {

            /** @noinspection PhpIncludeInspection */
            include $file;

        } catch (Exception $e) {

            $this->_logWarning($errors, sprintf('Failed to include %s: %s', $file, $e->getMessage()));
        }

        if ($isDebuggingEnabled) {

            $this->_logger->debug(sprintf('Done including file %s for add-on %s', $file, $addon->getName()));
        }
    }

    private function _hasValidBootMethod(&$errors, ReflectionClass $class)
    {
        if (!$class->hasMethod('boot')) {

            $this->_logWarning($errors, sprintf('%s has no boot() method', $class->getName()));

            return false;
        }

        $method = $class->getMethod('boot');

        if (!$method->isPublic()) {

            $this->_logWarning($errors, sprintf('%s\'s boot() method is not public', $class->getName()));

            return false;
        }

        return true;
    }

    private function _logWarning(&$errors, $message)
    {
        $this->_logger->warn($message);

        $errors[] = $message;
    }
}
