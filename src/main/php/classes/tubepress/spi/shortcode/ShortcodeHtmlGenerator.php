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
 * Handles shortcode HTML generation.
 */
interface tubepress_spi_shortcode_ShortcodeHtmlGenerator
{
    const _ = 'tubepress_spi_shortcode_ShortcodeHtmlGenerator';

    /**
     * Generates the HTML for the given shortcode.
     *
     * @param string $shortCodeContent The shortcode content.
     *
     * @return string The HTML for the given shortcode, or the error message if there was a problem.
     */
    function getHtmlForShortcode($shortCodeContent);
}