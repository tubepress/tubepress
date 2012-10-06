<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com);
 *
 * This file is part of TubePress (http://tubepress.org);
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
 */

/**
 * Holds all the option descriptors for TubePress. This implementation just holds them in memory.
 */
class tubepress_impl_options_DefaultOptionDescriptorReference implements tubepress_spi_options_OptionDescriptorReference
{
    /** Provides fast lookup by name. */
    private $_nameToOptionDescriptorMap = array();

    /**
     * @var tubepress_spi_options_StorageManager
     */
    private $_optionStorageManager;

    /**
     * @var array An array of option descriptors that were registered before the storage manager
     *            was alive.
     */
    private $_preStorageManagerOptionDescriptorBuffer = array();

    /**
     * @var bool True if we already registered ourselves as a listener, false otherwise.
     */
    private $_didRegisterSelfAsListener = false;

    /**
     * Returns all of the option descriptors.
     *
     * @return array All of the registered option descriptors.
     */
    public final function findAll()
    {
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
        if (! array_key_exists($name, $this->_nameToOptionDescriptorMap)) {

            return null;
        }

        return $this->_nameToOptionDescriptorMap[$name];
    }

    /**
     * Register a new option descriptor for use by TubePress.
     *
     * @param tubepress_spi_options_OptionDescriptor $optionDescriptor The new option descriptor.
     *
     * @throws InvalidArgumentException If the descriptor could not be registered.
     *
     * @return void
     */
    public final function registerOptionDescriptor(tubepress_spi_options_OptionDescriptor $optionDescriptor)
    {
        if (array_key_exists($optionDescriptor->getName(), $this->_nameToOptionDescriptorMap)) {

            throw new InvalidArgumentException($optionDescriptor->getName() . ' is already registered as an option descriptor');
        }

        if (! $this->_didRegisterSelfAsListener) {

            $eventDispatcher = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();

            $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::OPTION_STORAGE_MANAGER_READY,
                array($this, 'onOptionStorageManagerReady'));

            $this->_didRegisterSelfAsListener = true;
        }

        $this->_nameToOptionDescriptorMap[$optionDescriptor->getName()] = $optionDescriptor;

        if (! isset($this->_optionStorageManager)) {

            $this->_preStorageManagerOptionDescriptorBuffer[] = $optionDescriptor;

            return;
        }

        $this->_quickRegister($optionDescriptor);
    }

    public final function onOptionStorageManagerReady(tubepress_api_event_TubePressEvent $event)
    {
        $this->_optionStorageManager        = $event->getSubject();

        foreach ($this->_preStorageManagerOptionDescriptorBuffer as $optionDescriptor) {

            $this->_quickRegister($optionDescriptor);
        }
    }

    private function _quickRegister(tubepress_spi_options_OptionDescriptor $optionDescriptor)
    {
        if ($optionDescriptor->isMeantToBePersisted()) {

            $this->_optionStorageManager->createIfNotExists($optionDescriptor->getName(), $optionDescriptor->getDefaultValue());
        }
    }
}
