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
 * Collects CSS files to be loaded.
 */
interface tubepress_spi_html_CssAndJsRegistryInterface
{
    const _ = 'tubepress_spi_html_CssAndJsRegistryInterface';

    /**
     * Enqueue a CSS file for TubePress to display.
     *
     * @param string $handle The unique handle for this stylesheet.
     * @param string $url    The absolute URL to the stylesheet.
     * @param array  $deps   (Optional). Array of dependencies, specified by style handles.
     * @param string $media  (Optional). Media. Defaults to 'all'.
     *
     * @return bool True if style successfully registered, false otherwise.
     */
    function enqueueStyle($handle, $url, array $deps = array(), $media = 'all');

    /**
     * Dequeue a CSS file for TubePress to display.
     *
     * @param string $handle The unique handle for the stylesheet.
     *
     * @return bool True if style successfully deregistered. False if no matching handle.
     */
    function dequeueStyle($handle);

    /**
     * @return array An array of all registered style handles. May be empty, never null. Handles are given in order
     *               of correct dependency. i.e. CSS files with no dependencies are loaded first.
     */
    function getStyleHandlesForDisplay();

    /**
     * @param string $handle The unique handle for the stylesheet.
     *
     * @return array|null Null if no style registered with that handle. Otherwise an associative array with keys
     *                    "url", "dependencies", and "media".
     */
    function getStyle($handle);

    /**
     * Enqueue a JS file for TubePress to display.
     *
     * @param string $handle The unique handle for this script.
     * @param string $url    The absolute URL to the script.
     * @param array  $deps   (Optional). Array of dependencies, specified by script handles.
     *
     * @return bool True if script successfully registered, false otherwise.
     */
    function enqueueScript($handle, $url, array $deps = array());

    /**
     * Dequeue a JS file for TubePress to display.
     *
     * @param string $handle The unique handle for the script.
     *
     * @return bool True if script successfully deregistered. False if no matching handle.
     */
    function dequeueScript($handle);

    /**
     * @return array An array of all registered script handles. May be empty, never null. Handles are given in order
     *               of correct dependency. i.e. JS files with no dependencies are loaded first.
     */
    function getScriptHandlesForDisplay();

    /**
     * @param string $handle The unique handle for the script.
     *
     * @return array|null Null if no script registered with that handle. Otherwise an associative array with keys
     *                    "url", "dependencies".
     */
    function getScript($handle);
}