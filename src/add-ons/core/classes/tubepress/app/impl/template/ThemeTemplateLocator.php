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
class tubepress_app_impl_template_ThemeTemplateLocator
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
     * @var tubepress_app_impl_theme_CurrentThemeService
     */
    private $_currentThemeService;

    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    /**
     * @var array
     */
    private $_templateNameToThemeInstanceCache = array();

    public function __construct(tubepress_platform_api_log_LoggerInterface       $logger,
                                tubepress_app_api_options_ContextInterface       $context,
                                tubepress_platform_api_contrib_RegistryInterface $themeRegistry,
                                tubepress_app_impl_theme_CurrentThemeService     $currentThemeService)
    {
        $this->_themeRegistry       = $themeRegistry;
        $this->_context             = $context;
        $this->_currentThemeService = $currentThemeService;
        $this->_logger              = $logger;
        $this->_shouldLog           = $logger->isEnabled();
    }

    /**
     * @param string $name The name of the template to load.
     *
     * @return bool True if this template can be loaded from the theme hierarchy, false otherwise.
     */
    public function exists($name)
    {
        return $this->_findThemeForTemplate($name) !== null;
    }

    /**
     * Loads a template.
     *
     * @param string $name A template
     *
     * @return string The template source, or null if not found.
     *
     * @api
     */
    public function getSource($name)
    {
        $theme = $this->_findThemeForTemplate($name);

        if ($theme === null) {

            return null;
        }

        return $theme->getTemplateSource($name);
    }

    public function getAbsolutePath($name)
    {
        $theme = $this->_findThemeForTemplate($name);

        if ($theme === null || !($theme instanceof tubepress_app_impl_theme_FilesystemTheme)) {

            return null;
        }

        return $theme->getTemplatePath($name);
    }


    /**
     * Returns true if the template is still fresh.
     *
     * @param string $name A template name
     * @param int    $time The last modification time of the cached template (timestamp)
     *
     * @return bool
     *
     * @api
     */
    public function isFresh($name, $time)
    {
        $theme = $this->_findThemeForTemplate($name);

        if (!$theme) {

            throw new InvalidArgumentException();
        }

        return $theme->isTemplateSourceFresh($name, $time);
    }

    public function getCacheKey($name)
    {
        $theme = $this->_findThemeForTemplate($name);

        if (!$theme) {

            throw new InvalidArgumentException();
        }

        return $theme->getTemplateCacheKey($name);
    }

    /**
     * @param $templateName
     * @return null|tubepress_app_api_theme_ThemeInterface
     */
    private function _findThemeForTemplate($templateName)
    {
        if (isset($this->_templateNameToThemeInstanceCache[$templateName])) {

            $cachedValue = $this->_templateNameToThemeInstanceCache[$templateName];

            if ($this->_shouldLog) {

                if ($cachedValue) {

                    $this->_logger->debug(sprintf('Theme for template <code>%s</code> was found in the cache', $templateName));

                }
            }

            return $cachedValue ? $cachedValue : null;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Seeing if able to find source of template <code>%s</code> from theme hierarchy', $templateName));
        }

        $currentTheme = null;

        if (strpos($templateName, '::') !== false) {

            $exploded = explode('::', $templateName);

            if (count($exploded) === 2 && $this->_themeRegistry->getInstanceByName($exploded[0]) !== null) {

                $currentTheme = $this->_themeRegistry->getInstanceByName($exploded[0]);
                $templateName = $exploded[1];
            }
        }

        if (!$currentTheme) {

            $currentTheme = $this->_currentThemeService->getCurrentTheme();
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Current theme is <code>%s</code> version <code>%s</code>', $currentTheme->getName(), $currentTheme->getVersion()));
        }

        do {

            if ($currentTheme->hasTemplateSource($templateName)) {

                $this->_templateNameToThemeInstanceCache[$templateName] = $currentTheme;

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('Template source for <code>%s</code> was found in theme <code>%s</code>',
                        $templateName, $currentTheme->getName()));
                }

                return $currentTheme;
            }

            $nextThemeNameToCheck = $currentTheme->getParentThemeName();

            if ($nextThemeNameToCheck === null) {

                break;
            }

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Template source for <code>%s</code> was not found in theme <code>%s</code>. Now trying its parent theme: <code>%s</code>.',
                    $templateName, $currentTheme->getName(), $nextThemeNameToCheck));
            }

            try {

                $currentTheme = $this->_themeRegistry->getInstanceByName($nextThemeNameToCheck);

            } catch (InvalidArgumentException $e) {

                if ($this->_shouldLog) {

                    $this->_logger->error(sprintf('Unable to get the theme instance for <code>%s</code>. This should never happen!', $nextThemeNameToCheck));
                }

                break;
            }

        } while ($currentTheme !== null);

        $this->_templateNameToThemeInstanceCache[$templateName] = false;

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Unable to find source of template <code>%s</code> from theme hierarchy. Falling back to registered path providers.', $templateName));
        }

        return null;
    }
}