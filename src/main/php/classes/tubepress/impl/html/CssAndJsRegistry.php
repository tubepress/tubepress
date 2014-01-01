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
class tubepress_impl_html_CssAndJsRegistry implements tubepress_spi_html_CssAndJsRegistryInterface
{
    private $_styles = array();

    private $_scripts = array();

    private $_isDebugLoggingEnabled = false;

    /**
     * @var ehough_epilog_Logger Logger.
     */
    private $_logger;

    /**
     * @var null|array
     */
    private $_styleHandleCache;

    /**
     * @var null|array
     */
    private $_scriptHandleCache;

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
    public function enqueueStyle($handle, $url, array $deps = array(), $media = 'all')
    {
        $this->_initLogging();

        if (!$this->_checkHandleAndUrl($handle, $url, 'CSS')) {

            return false;
        }

        if (!$this->_isNonEmptyString($media, 'media')) {

            return false;
        }

        $this->_styles[(string) $handle] = array(

            'url'          => $url,
            'dependencies' => $deps,
            'media'        => $media
        );

        unset($this->_styleHandleCache);

        return true;
    }

    /**
     * Dequeue a CSS file for TubePress to display.
     *
     * @param string $handle The unique handle for the stylesheet.
     *
     * @return bool True if style successfully deregistered. False if no matching handle.
     */
    public function dequeueStyle($handle)
    {
        $this->_initLogging();

        if (!isset($this->_styles[$handle])) {

            return false;
        }

        unset($this->_styles[$handle]);
        unset($this->_styleHandleCache);

        return true;
    }

    /**
     * @return array An array of all registered style handles. May be empty, never null. Handles are given in order
     *               of correct dependency. i.e. CSS files with no dependencies are loaded first.
     */
    public function getStyleHandlesForDisplay()
    {
        $this->_initLogging();

        if (!isset($this->_styleHandleCache)) {

            $this->_styleHandleCache = $this->_calculateHandlesForDisplay($this->_styles);
        }

        return $this->_styleHandleCache;
    }

    /**
     * @param string $handle The unique handle for the stylesheet.
     *
     * @return array|null Null if no style registered with that handle. Otherwise an associative array with keys
     *                    "url", "dependencies", and "media".
     */
    public function getStyle($handle)
    {
        if (!isset($this->_styles[$handle])) {

            return null;
        }

        return $this->_styles[$handle];
    }

    /**
     * Enqueue a JS file for TubePress to display.
     *
     * @param string $handle The unique handle for this script.
     * @param string $url    The absolute URL to the script.
     * @param array  $deps   (Optional). Array of dependencies, specified by script handles.
     *
     * @return bool True if script successfully registered, false otherwise.
     */
    public function enqueueScript($handle, $url, array $deps = array())
    {
        $this->_initLogging();

        if (!$this->_checkHandleAndUrl($handle, $url, 'JS')) {

            return false;
        }

        $this->_scripts[(string) $handle] = array(

            'url'          => $url,
            'dependencies' => $deps,
        );

        unset($this->_scriptHandleCache);

        return true;
    }

    /**
     * Dequeue a JS file for TubePress to display.
     *
     * @param string $handle The unique handle for the script.
     *
     * @return bool True if script successfully deregistered. False if no matching handle.
     */
    public function dequeueScript($handle)
    {
        $this->_initLogging();

        if (!isset($this->_scripts[$handle])) {

            return false;
        }

        unset($this->_scripts[$handle]);
        unset($this->_scriptHandleCache);

        return true;
    }

    /**
     * @return array An array of all registered script handles. May be empty, never null. Handles are given in order
     *               of correct dependency. i.e. JS files with no dependencies are loaded first.
     */
    public function getScriptHandlesForDisplay()
    {
        $this->_initLogging();

        if (!isset($this->_scriptHandleCache)) {

            $this->_scriptHandleCache = $this->_calculateHandlesForDisplay($this->_scripts);
        }

        return $this->_scriptHandleCache;
    }

    /**
     * @param string $handle The unique handle for the script.
     *
     * @return array|null Null if no script registered with that handle. Otherwise an associative array with keys
     *                    "url", "dependencies", and "media".
     */
    public function getScript($handle)
    {
        if (!isset($this->_scripts[$handle])) {

            return null;
        }

        return $this->_scripts[$handle];
    }

    private function _checkHandleAndUrl($handle, $url, $name)
    {
        if (!$this->_isNonEmptyString($handle, $name . ' handle')) {

            return false;
        }

        if (!$this->_isNonEmptyString($url, $name . ' URL')) {

            return false;
        }

        try {

            new ehough_curly_Url($url);

        } catch (InvalidArgumentException $e) {

            if ($this->_isDebugLoggingEnabled) {

                $this->_logger->warning(sprintf('Invalid URL supplied for %s handle "%s"', $name, $handle));
            }

            return false;
        }

        if ($this->_isDebugLoggingEnabled) {

            $this->_logger->debug(sprintf('"%s" accepted as a %s file with URL %s', $handle, $name, $url));
        }

        return true;
    }

    private function _isNonEmptyString($candidate, $name)
    {
        if (!is_string($candidate)) {

            if ($this->_isDebugLoggingEnabled) {

                $this->_logger->warning("$name must be a string");

                return false;
            }
        }

        if ($candidate === '') {

            if ($this->_isDebugLoggingEnabled) {

                $this->_logger->warning("$name must be a non-empty string");

                return false;
            }
        }

        return true;
    }

    private function _initLogging()
    {
        if (isset($this->_logger)) {

            return;
        }

        $this->_logger = ehough_epilog_LoggerFactory::getLogger('CSS and JS Registry');

        $this->_isDebugLoggingEnabled = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);
    }

    private function _calculateHandlesForDisplay(array $arr)
    {
        $handles     = array_keys($arr);
        $handleCount = count($handles);

        /**
         * Remove nodes with unsatisfied deps.
         */
        for ($x = 0; $x < $handleCount; $x++) {

            $file = $arr[$handles[$x]];

            if (empty($file['dependencies'])) {

                //no deps
                continue;
            }

            foreach ($file['dependencies'] as $dep) {

                if (array_search($dep, $handles) === false) {

                    //missing dependency, start over
                    unset($handles[$x]);
                    $x = 0;
                    $handleCount--;
                    break;
                }
            }
        }

        $edges = $this->_buildEdgeMap($handles, $arr);

        $sorted = tubepress_impl_patterns_toposort_TopologicalSort::sort($handles, $edges);

        return array_reverse($sorted);
    }

    private function _buildEdgeMap($handles, array $arr)
    {
        $edges = array();

        foreach ($handles as $handle) {

            $info     = $arr[$handle];
            $nodeDeps = $info['dependencies'];

            foreach ($nodeDeps as $nodeDep) {

                $edges[] = array($handle, $nodeDep);
            }
        }

        return $edges;
    }
}