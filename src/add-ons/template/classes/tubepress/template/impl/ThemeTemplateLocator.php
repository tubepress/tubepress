<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 * 
 * This file is part of TubePress (http://tubepress.com)
 * 
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_template_impl_ThemeTemplateLocator
{
    /**
     * @var tubepress_api_contrib_RegistryInterface
     */
    private $_themeRegistry;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_theme_impl_CurrentThemeService
     */
    private $_currentThemeService;

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    /**
     * @var array
     */
    private $_currentThemeNameToTemplateNameToThemeInstanceCache = array();

    public function __construct(tubepress_api_log_LoggerInterface        $logger,
                                tubepress_api_options_ContextInterface   $context,
                                tubepress_api_contrib_RegistryInterface  $themeRegistry,
                                tubepress_theme_impl_CurrentThemeService $currentThemeService)
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

        if ($theme === null || !($theme instanceof tubepress_internal_theme_FilesystemTheme)) {

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
     *
     * @return null|tubepress_api_theme_ThemeInterface
     */
    private function _findThemeForTemplate($templateName)
    {
        $activeTheme     = $this->_currentThemeService->getCurrentTheme();
        $activeThemeName = $activeTheme->getName();

        if (strpos($templateName, '::') !== false) {

            $exploded = explode('::', $templateName);

            if (count($exploded) === 2 && $this->_themeRegistry->getInstanceByName($exploded[0]) !== null) {

                $activeTheme     = $this->_themeRegistry->getInstanceByName($exploded[0]);
                $activeThemeName = $activeTheme->getName();
                $templateName    = $exploded[1];
            }
        }

        if ($this->_shouldLog) {

            $this->_logDebug(sprintf('Seeing if we can find <code>%s</code> in the theme hierarchy. %s.',
                $templateName, $this->_loggerPostfix($activeTheme)));
        }

        if (isset($this->_currentThemeNameToTemplateNameToThemeInstanceCache[$activeThemeName]) &&
                isset($this->_currentThemeNameToTemplateNameToThemeInstanceCache[$activeThemeName][$templateName])) {

            $cachedValue = $this->_currentThemeNameToTemplateNameToThemeInstanceCache[$activeThemeName][$templateName];

            if ($this->_shouldLog) {

                if ($cachedValue) {

                    $this->_logDebug(sprintf('Theme for template <code>%s</code> was found in the cache to be contained in theme <code>%s</code> version <code>%s</code>. %s.',
                        $templateName, $cachedValue->getName(), $cachedValue->getVersion(), $this->_loggerPostfix($activeTheme)));

                } else {

                    $this->_logDebug(sprintf('We already tried to find a theme that contains <code>%s</code> in the theme hierarchy but didn\'t find it anywhere. %s.',
                        $templateName, $this->_loggerPostfix($activeTheme)));
                }
            }

            return $cachedValue ? $cachedValue : null;

        } else {

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('Looks like this is the first time searching for a theme that contains <code>%s</code>. %s.', $templateName, $this->_loggerPostfix($activeTheme)));
            }
        }

        do {

            $activeThemeName = $activeTheme->getName();

            if ($activeTheme->hasTemplateSource($templateName)) {

                if (!isset($this->_currentThemeNameToTemplateNameToThemeInstanceCache[$activeThemeName])) {

                    $this->_currentThemeNameToTemplateNameToThemeInstanceCache[$activeThemeName] = array();
                }

                $this->_currentThemeNameToTemplateNameToThemeInstanceCache[$activeThemeName][$templateName] = $activeTheme;

                if ($this->_shouldLog) {

                    $this->_logDebug(sprintf('Template source for <code>%s</code> was found in theme <code>%s</code> version <code>%s</code>. %s.',
                        $templateName, $activeThemeName, $activeTheme->getVersion(), $this->_loggerPostfix($activeTheme)));
                }

                return $activeTheme;
            }

            $nextThemeNameToCheck = $activeTheme->getParentThemeName();

            if ($nextThemeNameToCheck === null) {

                break;
            }

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('Template source for <code>%s</code> was not found in theme <code>%s</code>. Now trying its parent theme: <code>%s</code>.',
                    $templateName, $activeTheme->getName(), $nextThemeNameToCheck));
            }

            try {

                $activeTheme = $this->_themeRegistry->getInstanceByName($nextThemeNameToCheck);

            } catch (InvalidArgumentException $e) {

                if ($this->_shouldLog) {

                    $this->_logger->error(sprintf('Unable to get the theme instance for <code>%s</code>. This should never happen!', $nextThemeNameToCheck));
                }

                break;
            }

        } while ($activeTheme !== null);

        if (!isset($this->_currentThemeNameToTemplateNameToThemeInstanceCache[$activeThemeName])) {

            $this->_currentThemeNameToTemplateNameToThemeInstanceCache[$activeThemeName] = array();
        }

        $this->_currentThemeNameToTemplateNameToThemeInstanceCache[$activeThemeName][$templateName] = false;

        if ($this->_shouldLog) {

            $this->_logDebug(sprintf('Unable to find source of template <code>%s</code> from theme hierarchy.', $templateName));
        }

        return null;
    }

    private function _loggerPostfix(tubepress_api_theme_ThemeInterface $theme)
    {
        return sprintf('Theme <code>%s</code> version <code>%s</code>', $theme->getName(), $theme->getVersion());
    }

    private function _logDebug($msg)
    {
        $this->_logger->debug(sprintf('(Theme Template Locator) %s', $msg));
    }
}
