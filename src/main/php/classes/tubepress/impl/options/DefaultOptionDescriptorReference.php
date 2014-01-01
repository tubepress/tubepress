<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com);
 *
 * This file is part of TubePress (http://tubepress.com);
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Holds all the option descriptors for TubePress. This implementation just holds them in memory.
 */
class tubepress_impl_options_DefaultOptionDescriptorReference extends tubepress_impl_boot_AbstractCachingBootHelper implements tubepress_spi_options_OptionDescriptorReference
{
    /** Provides fast lookup by name. */
    private $_nameToOptionDescriptorMap;

    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog = false;

    /**
     * @var tubepress_spi_options_PluggableOptionDescriptorProvider[]
     */
    private $_optionDescriptorProviders = array();

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Default Option Descriptor Reference');
    }

    /**
     * Returns all of the option descriptors.
     *
     * @return array All of the registered option descriptors.
     */
    public final function findAll()
    {
        $this->_primeCache();

        return array_values($this->_nameToOptionDescriptorMap);
    }

    /**
     * Finds a single option descriptor by name, or null if no such option.
     *
     * @param string $name The option descriptor to look up.
     *
     * @return tubepress_spi_options_OptionDescriptor The option descriptor with the
     *                                                    given name, or null if not found.
     */
    public final function findOneByName($name)
    {
        $this->_primeCache();

        if (! array_key_exists($name, $this->_nameToOptionDescriptorMap)) {

            return null;
        }

        return $this->_nameToOptionDescriptorMap[$name];
    }

    public function _primeCache()
    {
        if (isset($this->_nameToOptionDescriptorMap)) {

            return;
        }

        $this->_shouldLog = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        $fromCache = $this->getCachedObject();

        if ($fromCache !== null) {

            $this->_nameToOptionDescriptorMap = $fromCache;
            return;
        }

        $this->_cacheOds();

        $this->tryToCache($this->_nameToOptionDescriptorMap);
    }

    public function setPluggableOptionDescriptorProviders(array $providers)
    {
        $this->_optionDescriptorProviders = $providers;
    }

    /**
     * @return string
     */
    protected function getBootCacheConfigElementName()
    {
        return 'option-descriptors';
    }

    /**
     * @return ehough_epilog_Logger
     */
    protected function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @param string $string The contents of the cache file, or the cache file path.
     *
     * @return object The hydrated object, or null if there was a problem.
     */
    protected function hydrate($string)
    {
        return $this->hydrateByDeserialization($string);
    }

    /**
     * @param object $object The object to convert to a string for the cache.
     *
     * @return string The string representation of the object, or null if there was a problem.
     */
    protected function toString($object)
    {
        return $this->toStringBySerialization($object);
    }

    /**
     * @return bool True if we should log, false otherwise.
     */
    protected function shouldLog()
    {
        return $this->_shouldLog;
    }

    private function _cacheOds()
    {
        $this->_nameToOptionDescriptorMap = array();

        foreach ($this->_optionDescriptorProviders as $optionDescriptorProvider) {

            $descriptors = $optionDescriptorProvider->getOptionDescriptors();

            foreach ($descriptors as $descriptor) {

                $this->_registerOptionDescriptor($descriptor);
            }
        }
    }

    /**
     * Register a new option descriptor for use by TubePress.
     *
     * @param tubepress_spi_options_OptionDescriptor $optionDescriptor The new option descriptor.
     *
     * @return void
     */
    private function _registerOptionDescriptor(tubepress_spi_options_OptionDescriptor $optionDescriptor)
    {
        $name = $optionDescriptor->getName();

        if (isset($this->_nameToOptionDescriptorMap[$name])) {

            if ($this->_shouldLog) {

                $this->_logger->warn($optionDescriptor->getName() . ' is already registered as an option descriptor');
            }

            return;
        }

        $optionRegistrationEvent = new tubepress_spi_event_EventBase($optionDescriptor);
        $eventDispatcher         = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $eventDispatcher->dispatch(

            tubepress_api_const_event_EventNames::OPTIONS_DESCRIPTOR_REGISTRATION,
            $optionRegistrationEvent
        );

        $this->_nameToOptionDescriptorMap[$name] = $optionDescriptor;
    }
}
