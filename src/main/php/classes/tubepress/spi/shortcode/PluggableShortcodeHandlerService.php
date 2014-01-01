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
 * Can handle a shortcode.
 */
interface tubepress_spi_shortcode_PluggableShortcodeHandlerService
{
    const _ = 'tubepress_spi_shortcode_PluggableShortcodeHandlerService';

    /**
     * @return string The name of this shortcode handler. Never empty or null. All lowercase alphanumerics and dashes.
     */
    function getName();

    /**
     * @return boolean True if this handler is interested in generating HTML, false otherwise.
     */
    function shouldExecute();

    /**
     * @return string The HTML for this shortcode handler.
     */
    function getHtml();
}
