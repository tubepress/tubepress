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
 * Constructs an efficient classloader.
 */
class tubepress_impl_boot_helper_secondary_ClassLoaderPrimer
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog = false;

    /**
     * @var array
     */
    private $_classmap;

    /**
     * @var array
     */
    private $_psr0FallbackRoots;

    /**
     * @var array
     */
    private $_psr0Roots;

    public function __construct(tubepress_api_log_LoggerInterface $logger)
    {
        $this->_logger    = $logger;
        $this->_shouldLog = $logger->isEnabled();
    }

    public function getClassMapFromAddons(array $addons)
    {
        $this->_execute($addons);

        return $this->_classmap;
    }

    public function getPsr0Roots(array $addons)
    {
        $this->_execute($addons);

        return $this->_psr0Roots;
    }

    public function getPsr0Fallbacks(array $addons)
    {
        $this->_execute($addons);

        return $this->_psr0FallbackRoots;
    }

    private function _execute(array $addons)
    {
        if (isset($this->_classmap)) {

            return;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Examining classloading data for %d add-ons', count($addons)));
        }

        $this->_classmap          = array();
        $this->_psr0Roots         = array();
        $this->_psr0FallbackRoots = array();
        $langUtils                = new tubepress_impl_util_LangUtils();

        /**
         * @var $addon tubepress_api_addon_AddonInterface
         */
        foreach ($addons as $addon) {

            $map   = $addon->getClassMap();
            $roots = $addon->getPsr0ClassPathRoots();

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Add-on %s has a classmap of size %d',
                    $addon->getName(), count($map)));
                $this->_logger->debug(sprintf('Add-on %s has %d PSR-0 path(s)',
                    $addon->getName(), count($roots)));
            }

            $this->_classmap = array_merge($this->_classmap, $map);

            if ($langUtils->isAssociativeArray($roots)) {

                $this->_psr0Roots = array_merge($this->_psr0Roots, $roots);

            } else {

                $this->_psr0FallbackRoots = array_merge($this->_psr0FallbackRoots, $roots);
            }
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Done examining classloading data for %d add-ons', count($addons)));
        }
    }
}