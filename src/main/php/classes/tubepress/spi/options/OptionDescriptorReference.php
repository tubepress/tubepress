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
 * Holds all the option descriptors for TubePress.
 */
interface tubepress_spi_options_OptionDescriptorReference
{
    const _ = 'tubepress_spi_options_OptionDescriptorReference';

    /**
     * Returns all of the option descriptors currently registered in the system.
     *
     * @return array All of the registered option descriptors. May be empty, never null.
     */
    function findAll();

    /**
     * Finds a single option descriptor by name, or null if no such option.
     *
     * @param string $name The option descriptor to look up.
     *
     * @return tubepress_spi_options_OptionDescriptor The option descriptor with the
     *                                                      given name, or null if not found.
     */
    function findOneByName($name);

    /**
     * Register a new option descriptor for use by TubePress.
     *
     * @param tubepress_spi_options_OptionDescriptor $optionDescriptor The new option descriptor.
     *
     * @throws InvalidArgumentException If the descriptor could not be registered.
     *
     * @return void
     */
    function registerOptionDescriptor(tubepress_spi_options_OptionDescriptor $optionDescriptor);
}
