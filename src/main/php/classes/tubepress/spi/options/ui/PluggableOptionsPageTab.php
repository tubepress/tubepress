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
 * An individual option tab.
 */
interface tubepress_spi_options_ui_PluggableOptionsPageTab extends tubepress_spi_options_ui_FormHandler
{
    const CLASS_NAME = 'tubepress_spi_options_ui_PluggableOptionsPageTab';

    /**
     * Get the title of this tab.
     *
     * @return string The title of this tab.
     */
    function getTitle();

    function getName();
}