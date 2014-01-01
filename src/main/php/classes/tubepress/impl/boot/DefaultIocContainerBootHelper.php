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
 * Handles constructing/initializing the IOC container.
 */
class tubepress_impl_boot_DefaultIocContainerBootHelper extends tubepress_impl_boot_AbstractCachingBootHelper implements tubepress_spi_boot_IocContainerHelper
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
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Default IOC Boot Helper');
    }

    /**
     * Compiles the container, if necessary.
     *
     * @param tubepress_impl_ioc_CoreIocContainer $container
     * @param array                               $addons
     *
     * @return void
     */
    public function compile(tubepress_impl_ioc_CoreIocContainer $container, array $addons)
    {
        $this->_shouldLog = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        $cachedIconicContainer = $this->getCachedObject(false);

        if ($cachedIconicContainer !== null) {

            $container->setDelegateIconicContainerBuilder($cachedIconicContainer);

            return;
        }

        $this->_compile($container, $addons);

        $this->tryToCache($container->getDelegateIconicContainerBuilder());
    }

    private function _compile(tubepress_impl_ioc_CoreIocContainer $container, array $addons)
    {
        if ($this->_shouldLog) {

            $this->_logger->debug('Compiling IOC container.');
        }

        /**
         * Load IOC container extensions.
         */
        $this->_registerIocContainerExtensions($container, $addons);

        if ($this->_shouldLog) {

            $this->_logger->debug('Done registering add-on IoC container extensions. Now registering add-on IoC compiler passes.');
        }

        /*
         * Load IOC compiler passes.
         */
        $this->_registerIocCompilerPasses($container, $addons);

        if ($this->_shouldLog) {

            $this->_logger->debug('Done registering add-on IoC compiler passes. Now compiling IoC container.');
        }

        /**
         * Compile all our services.
         */
        $container->compile();

        if ($this->_shouldLog) {

            $this->_logger->debug('Done compiling IoC container.');
        }
    }

    private function _registerIocContainerExtensions(tubepress_impl_ioc_CoreIocContainer $container, array $addons)
    {
        $index = 1;
        $count = count($addons);

        /**
         * @var $addon tubepress_spi_addon_Addon
         */
        foreach ($addons as $addon) {

            $extensions = $addon->getIocContainerExtensions();

            if (count($extensions) === 0) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Did not register any IoC container extensions',
                        $index, $count, $addon->getName()));
                }

                $index++;

                continue;
            }

            foreach ($extensions as $extension) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Will attempt to load %s as an IoC container extension',
                        $index, $count, $addon->getName(), $extension));
                }

                try {

                    $ref = new ReflectionClass($extension);

                    $container->registerExtension($ref->newInstance());

                    if ($this->_shouldLog) {

                        $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Successfully loaded %s as an IoC container extension',
                            $index, $count, $addon->getName(), $extension));
                    }

                } catch (Exception $e) {

                    if ($this->_shouldLog) {

                        $this->_logger->warn(sprintf('(Add-on %d of %d: %s) Failed to load %s as an IoC container extension: %s',
                            $index, $count, $addon->getName(), $extension, $e->getMessage()));
                    }
                }
            }

            $index++;
        }
    }

    private function _registerIocCompilerPasses(tubepress_impl_ioc_CoreIocContainer $container, array $addons)
    {
        $index = 1;
        $count = count($addons);

        /**
         * @var $addon tubepress_spi_addon_Addon
         */
        foreach ($addons as $addon) {

            $compilerPasses = $addon->getIocContainerCompilerPasses();

            if (count($compilerPasses) === 0) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Did not register any IoC compiler passes',
                        $index, $count, $addon->getName()));
                }

                $index++;

                continue;
            }

            foreach ($compilerPasses as $compilerPass) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Will attempt to load %s as an IoC compiler pass',
                        $index, $count, $addon->getName(), $compilerPass));
                }

                try {

                    $ref = new ReflectionClass($compilerPass);

                    $container->addCompilerPass($ref->newInstance());

                    if ($this->_shouldLog) {

                        $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Successfully loaded %s as an IoC compiler pass',
                            $index, $count, $addon->getName(), $compilerPass));
                    }

                } catch (Exception $e) {

                    if ($this->_shouldLog) {

                        $this->_logger->warn(sprintf('(Add-on %d of %d: %s) Failed to load %s as an IoC compiler pass: %s',
                            $index, $count, $addon->getName(), $compilerPass, $e->getMessage()));
                    }
                }
            }

            $index++;
        }
    }

    /**
     * @return string
     */
    protected function getBootCacheConfigElementName()
    {
        return 'ioc-container';
    }

    /**
     * @return ehough_epilog_Logger
     */
    protected function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @param string $path The contents of the cache file.
     *
     * @return object The hydrated object, or null if there was a problem.
     */
    protected function hydrate($path)
    {
        if (!is_file($path) || !is_readable($path)) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('IOC container cache file is not a readable file: %s', $path));
            }

            return false;
        }

        if (!class_exists('TubePressServiceContainer')) {

            include $path;
        }

        if (!class_exists('TubePressServiceContainer')) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Could not read cached service container', $path));
            }

            return false;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Loaded cached container. Now attempting to instantiate', $path));
        }

        /** @noinspection PhpUndefinedClassInspection */
        return new TubePressServiceContainer();
    }

    /**
     * @param object $object The object to convert to a string for the cache.
     *
     * @return string The string representation of the object, or null if there was a problem.
     */
    protected function toString($object)
    {
        $dumpConfig = array(

            'class' => 'TubePressServiceContainer',
            'base_class' => 'ehough_iconic_ContainerBuilder'
        );

        try {

            $dumper = new ehough_iconic_dumper_PhpDumper($object);

            return $dumper->dump($dumpConfig);

        } catch (Exception $e) {

            if ($this->_shouldLog) {

                $this->_logger->warn('Caught exception trying to dump IOC container: ' . $e->getMessage());
            }

            return null;
        }
    }

    /**
     * @return bool True if we should log, false otherwise.
     */
    protected function shouldLog()
    {
        return $this->_shouldLog;
    }
}