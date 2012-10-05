<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Can handle a shortcode.
 */
interface tubepress_spi_shortcode_ShortcodeHandler
{
    const _ = 'tubepress_spi_shortcode_ShortcodeHandler';

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
