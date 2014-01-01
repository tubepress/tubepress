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
 * A form element that appears and participates in the options page.
 */
interface tubepress_spi_options_ui_OptionsPageFieldInterface extends tubepress_spi_options_ui_OptionsPageItemInterface
{
    /**
     * @return string The optional description of this element that is displayed to the user. May be empty or null.
     */
    function getTranslatedDescription();

    /**
     * @return string The widget HTML for this form element.
     */
    function getWidgetHTML();

    /**
     * Invoked when the element is submitted by the user.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    function onSubmit();

    /**
     * Gets whether or not this field is TubePress Pro only.
     *
     * @return boolean True if this field is TubePress Pro only. False otherwise.
     */
    function isProOnly();
}