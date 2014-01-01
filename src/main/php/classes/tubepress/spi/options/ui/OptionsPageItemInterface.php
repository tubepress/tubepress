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
 * An item appears on and participates in the options page.
 */
interface tubepress_spi_options_ui_OptionsPageItemInterface
{
    /**
     * @return string The name of the item that is displayed to the user.
     */
    function getTranslatedDisplayName();

    /**
     * @return string The page-unique identifier for this item.
     */
    function getId();
}