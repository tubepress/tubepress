<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
interface tubepress_api_options_ui_ElementInterface
{
    /**
     * @return string The page-unique identifier for this item.
     *
     * @api
     * @since 4.0.0
     */
    function getId();

    /**
     * @return tubepress_api_collection_MapInterface
     */
    function getProperties();

    /**
     * @param string $name  The property name.
     * @param mixed  $value The property value.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function setProperty($name, $value);

    /**
     * @return string|null The untranslated display name of this element. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getUntranslatedDisplayName();
}