<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 * 
 * This file is part of TubePress (http://tubepress.com)
 * 
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 *
 */
class tubepress_app_impl_theme_CurrentThemeService
{
    /**
     * @var tubepress_platform_api_contrib_RegistryInterface
     */
    private $_themeRegistry;

    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var array
     */
    private $_themeMap;

    /**
     * @var string
     */
    private $_defaultThemeName;

    /**
     * @var string
     */
    private $_optionName;

    public function __construct(tubepress_app_api_options_ContextInterface       $context,
                                tubepress_platform_api_contrib_RegistryInterface $themeRegistry,
                                $defaultThemeName,
                                $optionName)
    {
        $this->_themeRegistry    = $themeRegistry;
        $this->_context          = $context;
        $this->_defaultThemeName = $defaultThemeName;
        $this->_optionName       = $optionName;
    }

    /**
     * @return tubepress_app_api_theme_ThemeInterface
     */
    public function getCurrentTheme()
    {
        $currentTheme = $this->_context->get($this->_optionName);

        $this->_initCache();

        if ($currentTheme == '') {

            $currentTheme = $this->_defaultThemeName;
        }

        if (array_key_exists($currentTheme, $this->_themeMap)) {

            return $this->_themeMap[$currentTheme];
        }

        if (array_key_exists("tubepress/legacy-$currentTheme", $this->_themeMap)) {

            return $this->_themeMap["tubepress/legacy-$currentTheme"];
        }

        if (array_key_exists("unknown/legacy-$currentTheme", $this->_themeMap)) {

            return $this->_themeMap["unknown/legacy-$currentTheme"];
        }

        return $this->_themeMap[$this->_defaultThemeName];
    }

    private function _initCache()
    {
        if (isset($this->_themeMap)) {

            return;
        }

        $this->_themeMap = array();

        foreach ($this->_themeRegistry->getAll() as $theme) {

            $this->_themeMap[$theme->getName()] = $theme;
        }
    }
}