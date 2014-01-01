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
 * Provides options.
 */
interface tubepress_spi_options_PluggableOptionDescriptorProvider
{
    const _ = 'tubepress_spi_options_PluggableOptionDescriptorProvider';

    /**
     * Fetch all the option descriptors from this provider.
     *
     * @return tubepress_spi_options_OptionDescriptor[]
     */
    function getOptionDescriptors();
}
