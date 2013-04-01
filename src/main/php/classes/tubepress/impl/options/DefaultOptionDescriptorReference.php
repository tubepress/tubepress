<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org);
 *
 * This file is part of TubePress (http://tubepress.org);
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Holds all the option descriptors for TubePress. This implementation just holds them in memory.
 */
class tubepress_impl_options_DefaultOptionDescriptorReference implements tubepress_spi_options_OptionDescriptorReference
{
    /** Provides fast lookup by name. */
    private $_nameToOptionDescriptorMap = array();

    /**
     * @var tubepress_spi_options_StorageManager Cache of the storage manager.
     */
    private $_storageManager;

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
        $name = $optionDescriptor->getName();

        if (isset($this->_nameToOptionDescriptorMap[$name])) {

            throw new InvalidArgumentException($optionDescriptor->getName() . ' is already registered as an option descriptor');
        }

        $this->_nameToOptionDescriptorMap[$name] = $optionDescriptor;

        if (!isset($this->_storageManager)) {

            $this->_storageManager = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();
        }

        if ($optionDescriptor->isMeantToBePersisted()) {

            $this->_storageManager->createIfNotExists($name, $optionDescriptor->getDefaultValue());
        }
    }
}
