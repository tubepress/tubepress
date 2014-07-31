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

class tubepress_app_impl_template_twig_EnvironmentBuilder
{
    /**
     * @var Twig_LoaderInterface
     */
    private $_loader;

    /**
     * @var tubepress_platform_api_boot_BootSettingsInterface
     */
    private $_bootSettingsInterface;

    private $_context;

    public function __construct(Twig_LoaderInterface                              $loader,
                                tubepress_platform_api_boot_BootSettingsInterface $bootSettings,
                                tubepress_app_api_options_ContextInterface        $context)
    {
        $this->_loader                = $loader;
        $this->_bootSettingsInterface = $bootSettings;
        $this->_context               = $context;
    }

    public function buildTwigEnvironment()
    {
        return new Twig_Environment($this->_loader, array(
            'cache'       => $this->_getCache(),
            'auto_reload' => $this->_getAutoReload()
        ));
    }

    private function _getAutoReload()
    {
        return (bool) $this->_context->get(tubepress_app_api_options_Names::TEMPLATE_CACHE_AUTORELOAD);
    }

    private function _getCache()
    {
        $enabled = $this->_context->get(tubepress_app_api_options_Names::TEMPLATE_CACHE_ENABLED);

        if (!$enabled) {

            return false;
        }

        $dir = $this->_context->get(tubepress_app_api_options_Names::TEMPLATE_CACHE_DIR);

        if ($this->_writableDirectory($dir)) {

            return $dir;
        }

        $dir = $this->_bootSettingsInterface->getPathToSystemCacheDirectory() . DIRECTORY_SEPARATOR . '/twig-cache';

        if ($this->_writableDirectory($dir)) {

            return $dir;
        }

        @mkdir($dir);

        if ($this->_writableDirectory($dir)) {

            return $dir;
        }

        return false;
    }

    private function _writableDirectory($candidate)
    {
        return is_dir($candidate) && is_writable($candidate);
    }
}