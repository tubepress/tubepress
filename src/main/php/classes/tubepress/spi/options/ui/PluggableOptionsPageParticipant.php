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
 * Major participant in the option page.
 */
interface tubepress_spi_options_ui_PluggableOptionsPageParticipant
{
    const _ = 'tubepress_spi_options_ui_PluggableOptionsPageParticipant';

    /**
     * @return string The name that will be displayed in the options page filter (top right).
     */
    function getFriendlyName();

    /**
     * @return string All lowercase alphanumerics.
     */
    function getName();

    /**
     * @param string $tabName The name of the tab being built.
     *
     * @return array An array of fields that should be shown on the given tab. May be empty, never null.
     */
    function getFieldsForTab($tabName);
}