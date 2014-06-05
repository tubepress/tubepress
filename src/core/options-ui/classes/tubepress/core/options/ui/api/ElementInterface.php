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
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_core_options_ui_api_ElementInterface
{
    /**
     * @return string The name of the item that is displayed to the user.
     *
     * @api
     * @since 4.0.0
     */
    function getTranslatedDisplayName();

    /**
     * @return string The page-unique identifier for this item.
     *
     * @api
     * @since 4.0.0
     */
    function getId();
}