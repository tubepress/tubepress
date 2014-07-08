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
 *
 */
class tubepress_lib_template_impl_contemplate_TemplateFactory implements tubepress_lib_template_api_TemplateFactoryInterface
{
    /**
     * @var tubepress_platform_api_util_LangUtilsInterface
     */
    private $_langUtils;

    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_app_theme_api_ThemeLibraryInterface
     */
    private $_themeLibrary;

    /**
     * @var
     */
    private $_filesystem;

    /**
     * @var bool
     */
    private $_logEnabled;

    /**
     * @var array
     */
    private $_cache = array();

    public function __construct(

        tubepress_platform_api_log_LoggerInterface              $logger,
        tubepress_platform_api_util_LangUtilsInterface          $langUtils,
        tubepress_app_theme_api_ThemeLibraryInterface $themeLibrary,
        ehough_filesystem_FilesystemInterface          $filesystem)
    {
        $this->_logger       = $logger;
        $this->_langUtils    = $langUtils;
        $this->_logEnabled   = $this->_logger->isEnabled();
        $this->_themeLibrary = $themeLibrary;
        $this->_filesystem   = $filesystem;
    }
    
    /**
     * Loads a new template instance by path.
     *
     * @param string[] $paths An array of filesystem paths to search, in order of priority. The first path
     *                        with an existing file will be used. Each path can either be absolute or relative.
     *                        If absolute, the absolute path will be used. If relative, assume path is
     *                        relative to the root of the current TubePress theme.
     *
     * @return tubepress_lib_template_api_TemplateInterface|null A template instance, or null if the template cannot be found.
     */
    public function fromFilesystem(array $paths)
    {
        $index     = 1;
        $pathCount = count($paths);

        $cacheKey = $this->_createCacheKey($paths);

        if (isset($this->_cache[$cacheKey])) {

            return $this->_newTemplateInstance($this->_cache[$cacheKey], $cacheKey);
        }

        if ($this->_logEnabled) {

            $this->_logger->debug(sprintf('Attempting to load a template from %d possible path(s): %s', $pathCount, json_encode($paths)));
        }

        foreach ($paths as $path) {

            if (!is_string($path)) {

                continue;
            }

            if ($this->_logEnabled) {

                $this->_logger->debug(sprintf('Attempting to load template from "%s" (%d of %d possible locations)',
                    $path, $index, $pathCount));
            }

            $template = $this->_loadTemplate($path, $cacheKey);

            if ($template) {

                if ($this->_logEnabled) {

                    $this->_logger->debug(sprintf('Able to load template from "%s" (%d of %d possible locations)',
                        $path, $index, $pathCount));
                }

                return $template;
            }

            if ($this->_logEnabled) {

                $this->_logger->debug(sprintf('Unable to load template from "%s" (%d of %d possible locations)',
                    $path, $index++, $pathCount));
            }
        }

        if ($this->_logEnabled) {

            $this->_logger->error(sprintf('Unable to load template from any of %d possible locations',
                $pathCount));
        }

        return null;
    }

    private function _createCacheKey(array $array)
    {
        return md5(json_encode($array) . $this->_themeLibrary->getCurrentThemeName());
    }

    private function _loadTemplate($path, $cacheKey)
    {
        if (is_file($path) && is_readable($path) && $this->_filesystem->isAbsolutePath($path)) {

            return $this->_newTemplateInstance($path, $cacheKey);
        }

        return $this->_loadTemplateFromRelativePath($path, $cacheKey);
    }

    private function _loadTemplateFromRelativePath($path, $cacheKey)
    {
        $pathToTemplate = ltrim($path, DIRECTORY_SEPARATOR);

        if ($this->_logEnabled) {

            $this->_logger->debug(sprintf('Attempting to load theme template at path "%s"', $pathToTemplate));
        }

        $filePath = $this->_themeLibrary->getAbsolutePathToTemplate($path);

        if ($filePath === null) {

            return null;
        }

        if ($this->_logEnabled) {

            $this->_logger->debug(sprintf('Candidate absolute path is "%s"', $filePath));
        }

        return $this->_newTemplateInstance($filePath, $cacheKey);
    }

    private function _newTemplateInstance($fullPath, $cacheKey)
    {
        $contemplateTemplate = new ehough_contemplate_impl_SimpleTemplate();
        $contemplateTemplate->setPath($fullPath);

        $this->_cache[$cacheKey] = $fullPath;

        return new tubepress_lib_template_impl_contemplate_Template(

            $contemplateTemplate,
            $this->_langUtils
        );
    }
}