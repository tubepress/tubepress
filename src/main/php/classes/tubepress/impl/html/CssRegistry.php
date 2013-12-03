<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
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
class tubepress_impl_html_CssRegistry implements tubepress_spi_html_CssRegistryInterface
{
    private $_styles = array();

    private $_isDebugLoggingEnabled = false;

    /**
     * @var ehough_epilog_Logger Logger.
     */
    private $_logger;

    /**
     * @var null|array
     */
    private $_handleCache;

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

        if (!$this->_checkStyle($handle, $url, $deps, $media)) {

            return false;
        }

        $this->_styles[(string) $handle] = array(

            'url'          => $url,
            'dependencies' => $deps,
            'media'        => $media
        );

        unset($this->_handleCache);

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
        unset($this->_handleCache);

        return true;
    }

    /**
     * @return array An array of all registered style handles. May be empty, never null. Handles are given in order
     *               of correct dependency. i.e. CSS files with no dependencies are loaded first.
     */
    public function getStyleHandlesForDisplay()
    {
        $this->_initLogging();

        if (!isset($this->_handleCache)) {

            $this->_handleCache = $this->_calculateHandlesForDisplay();
        }

        return $this->_handleCache;
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

    private function _checkStyle($handle, $url, $deps, $media)
    {
        if (!$this->_isNonEmptyString($handle, 'handle')) {

            return false;
        }

        if (!$this->_isNonEmptyString($url, 'url')) {

            return false;
        }

        if (!$this->_isNonEmptyString($media, 'media')) {

            return false;
        }

        try {

            new ehough_curly_Url($url);

        } catch (InvalidArgumentException $e) {

            if ($this->_isDebugLoggingEnabled) {

                $this->_logger->warning('Invalid URL supplied for CSS handle ' . $handle);
            }

            return false;
        }

        if ($this->_isDebugLoggingEnabled) {

            $this->_logger->debug(sprintf('%s accepted as a CSS file with URL %s', $handle, $url));
        }

        return true;
    }

    private function _isNonEmptyString($candidate, $name)
    {
        if (!is_string($candidate)) {

            if ($this->_isDebugLoggingEnabled) {

                $this->_logger->warning("CSS $name must be a string");

                return false;
            }
        }

        if ($candidate === '') {

            if ($this->_isDebugLoggingEnabled) {

                $this->_logger->warning("CSS $name must be a non-empty string");

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

        $this->_logger = ehough_epilog_LoggerFactory::getLogger('CSS Registry');

        $this->_isDebugLoggingEnabled = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);
    }

    private function _calculateHandlesForDisplay()
    {
        $handles     = array_keys($this->_styles);
        $handleCount = count($handles);

        /**
         * Remove nodes with unsatisfied deps.
         */
        for ($x = 0; $x < $handleCount; $x++) {

            $style = $this->_styles[$handles[$x]];

            if (empty($style['dependencies'])) {

                //no deps
                continue;
            }

            foreach ($style['dependencies'] as $styleDep) {

                if (array_search($styleDep, $handles) === false) {

                    //missing dependency, start over
                    unset($handles[$x]);
                    $x = 0;
                    $handleCount--;
                    break;
                }
            }
        }

        $edges = $this->_buildEdgeMap($handles);

        $sorted = tubepress_impl_patterns_toposort_TopologicalSort::sort($handles, $edges);

        return array_reverse($sorted);
    }

    private function _buildEdgeMap($handles)
    {
        $edges = array();

        foreach ($handles as $handle) {

            $info     = $this->_styles[$handle];
            $nodeDeps = $info['dependencies'];

            foreach ($nodeDeps as $nodeDep) {

                $edges[] = array($handle, $nodeDep);
            }
        }

        return $edges;
    }
}