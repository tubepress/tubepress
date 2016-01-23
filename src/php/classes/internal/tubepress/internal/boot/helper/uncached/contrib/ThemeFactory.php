<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */


class tubepress_internal_boot_helper_uncached_contrib_ThemeFactory extends tubepress_internal_boot_helper_uncached_contrib_AbstractFactory
{
    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_internal_finder_FinderFactory
     */
    private $_finderFactory;

    private static $_FIRST_LEVEL_KEY_PARENT  = 'parent';
    private static $_FIRST_LEVEL_KEY_SCRIPTS = 'scripts';
    private static $_FIRST_LEVEL_KEY_STYLES  = 'styles';
    private static $_FIRST_LEVEL_KEY_CSS     = 'inlineCSS';

    public function __construct(tubepress_api_options_ContextInterface  $context,
                                tubepress_api_url_UrlFactoryInterface   $urlFactory,
                                tubepress_api_util_LangUtilsInterface   $langUtils,
                                tubepress_api_log_LoggerInterface       $log,
                                tubepress_api_util_StringUtilsInterface $stringUtils,
                                tubepress_internal_finder_FinderFactory $finderFactory)
    {
        parent::__construct($log, $urlFactory, $langUtils, $stringUtils);

        $this->_context       = $context;
        $this->_finderFactory = $finderFactory;
    }


    /**
     * @param string $manifestPath
     * @param array  &$manifestData
     *
     * @return array
     */
    protected function normalizeAndReturnErrors($manifestPath, array &$manifestData)
    {
        $errors = array();

        $this->_handleParentThemeName($manifestData, $errors);
        $this->_handleScripts($manifestData, $errors);
        $this->_handleStyles($manifestData, $errors);
        $this->_handleInlineCss($manifestData, $errors);

        return $errors;
    }

    /**
     * @param string $manifestPath
     * @param array  &$manifestData
     *
     * @return tubepress_internal_contrib_AbstractContributable
     */
    protected function buildWithValidNormalizedData($manifestPath, array &$manifestData)
    {
        $theme = new tubepress_internal_theme_FilesystemTheme(
            $manifestData[self::$FIRST_LEVEL_KEY_NAME],
            $manifestData[self::$FIRST_LEVEL_KEY_VERSION],
            $manifestData[self::$FIRST_LEVEL_KEY_TITLE],
            $manifestData[self::$FIRST_LEVEL_KEY_AUTHORS],
            $manifestData[self::$FIRST_LEVEL_KEY_LICENSE]
        );

        $this->_setParentThemeName($theme, $manifestData);
        $this->_setInlineCss($theme, $manifestData);
        $this->_setStyles($theme, $manifestData);
        $this->_setScripts($theme, $manifestData);
        $this->_setTemplates($manifestPath, $theme);

        $theme->setManifestPath($manifestPath);

        return $theme;
    }

    private function _handleParentThemeName(array &$manifestData, array &$errors)
    {
        $parentKey = self::$_FIRST_LEVEL_KEY_PARENT;

        if (!isset($manifestData[$parentKey])) {

            return;
        }

        $candidate = $manifestData[$parentKey];

        if (!is_string($candidate)) {

            $errors[] = 'Parent theme name must be a string';
            return;
        }

        $matchesRegex = preg_match_all('~[0-9a-z-_/\.]{1,100}~', $candidate, $matches) === 1;

        if (!$matchesRegex) {

            $message = 'Parent theme name must be all lowercase, 100 characters or less, and contain only alphanumerics, dots, dashes, underscores, and slashes';

            $errors[] = $message;
        }
    }

    private function _handleScripts(array &$manifestData, array &$errors)
    {
        $this->_handleScriptsOrStyles($manifestData, $errors,
            self::$_FIRST_LEVEL_KEY_SCRIPTS, 'script');
    }

    private function _handleStyles(array &$manifestData, array &$errors)
    {
        $this->_handleScriptsOrStyles($manifestData, $errors,
            self::$_FIRST_LEVEL_KEY_STYLES, 'style');
    }

    private function _handleScriptsOrStyles(array &$manifestData, array &$errors, $key, $name)
    {
        if (!isset($manifestData[$key])) {

            return;
        }

        $candidate = $manifestData[$key];

        if (!$this->getLangUtils()->isSimpleArrayOfStrings($candidate)) {

            $errors[] = sprintf('%ss must be a simple array of strings', ucwords($name));
            return;
        }

        for ($x = 0; $x < count($candidate); $x++) {

            $stringUrl = $candidate[$x];

            if (!$stringUrl) {

                $errors[] = sprintf('%s <code>%d</code> is empty', ucwords($name), ($x + 1));
                return;
            }

            $realUrl = $this->toUrl($stringUrl, false);

            if (!$realUrl) {

                $errors[] = sprintf('%s <code>%d</code> is invalid', ucwords($name), ($x + 1));
                return;
            }

            $candidate[$x] = $realUrl;
        }

        $manifestData[$key] = $candidate;
    }

    private function _handleInlineCss(array &$manifestData, array &$errors)
    {
        $key = self::$_FIRST_LEVEL_KEY_CSS;

        if (!isset($manifestData[$key])) {

            return;
        }

        $candidate = $manifestData[$key];
        $cssString = null;

        try {

            $cssString = $this->_cssArrayToString($candidate);

        } catch (InvalidArgumentException $e) {

            $errors[] = $e->getMessage();
            return;
        }

        $manifestData[$key] = $cssString;
    }

    private function _setParentThemeName(tubepress_internal_theme_FilesystemTheme $theme, array $manifestData)
    {
        if (isset($manifestData[self::$_FIRST_LEVEL_KEY_PARENT])) {

            $theme->setParentThemeName($manifestData[self::$_FIRST_LEVEL_KEY_PARENT]);
        }
    }

    private function _setInlineCss(tubepress_internal_theme_FilesystemTheme $theme, array $manifestData)
    {
        if (isset($manifestData[self::$_FIRST_LEVEL_KEY_CSS])) {

            $theme->setInlineCss($manifestData[self::$_FIRST_LEVEL_KEY_CSS]);
        }
    }

    private function _setStyles(tubepress_internal_theme_FilesystemTheme $theme, array $manifestData)
    {
        if (isset($manifestData[self::$_FIRST_LEVEL_KEY_STYLES])) {

            $theme->setStyles($manifestData[self::$_FIRST_LEVEL_KEY_STYLES]);
        }
    }

    private function _setScripts(tubepress_internal_theme_FilesystemTheme $theme, array $manifestData)
    {
        if (isset($manifestData[self::$_FIRST_LEVEL_KEY_SCRIPTS])) {

            $theme->setScripts($manifestData[self::$_FIRST_LEVEL_KEY_SCRIPTS]);
        }
    }

    private function _setTemplates($manifestPath, tubepress_internal_theme_FilesystemTheme $theme)
    {
        $themeRoot  = dirname($manifestPath);
        $templates  = $this->_findTemplates($themeRoot);
        $theme->setTemplateNamesToAbsPathsMap($templates);
    }

    private function _cssArrayToString($node, $depth = 0)
    {
        $toReturn = '';

        if (!$this->getLangUtils()->isAssociativeArray($node)) {

            throw new InvalidArgumentException('Inline CSS contains non-associative arrays');
        }

        if (isset($node['attributes'])) {

            if (count($node['attributes']) > 0 && !$this->getLangUtils()->isAssociativeArray($node['attributes'])) {

                throw new InvalidArgumentException('Attributes must be an associative array');
            }

            foreach ($node['attributes'] as $attributeName => $value) {

                if (is_array($value)) {

                    for ($i = 0; $i < count($value); $i++) {

                        $toReturn .= $this->_cssStrAttr($attributeName, $value[$i], $depth);
                    }

                } else {

                    $toReturn .= $this->_cssStrAttr($attributeName, $value, $depth);
                }
            }
        }

        if (isset($node['children'])) {

            $first = true;

            if (count($node['children']) > 0 && !$this->getLangUtils()->isAssociativeArray($node['children'])) {

                throw new InvalidArgumentException('Children must be an associative array');
            }

            foreach ($node['children'] as $childName => $childValues) {

                if (!$first) {

                    $toReturn .= "\n";

                } else {

                    $first = false;
                }

                $toReturn .= $this->_strNode($childName, $childValues, $depth);
            }
        }

        return $toReturn;
    }

    private function _cssStrAttr($name, $value, $depth)
    {
        return str_repeat("\t", $depth) . $name . ': ' . $value . ";\n";
    }

    private function _strNode($name, $value, $depth)
    {
        $cssString = str_repeat("\t", $depth) . $name . " {\n";
        $cssString .= $this->_cssArrayToString($value, $depth + 1);
        $cssString .= str_repeat("\t", $depth) . "}\n";

        return $cssString;
    }

    private function _findTemplates($rootDirectory)
    {
        $legacy   = $this->_doFind($rootDirectory, '.tpl.php');
        $twig     = $this->_doFind($rootDirectory, '.html.twig');
        $allPaths = array_merge($twig, $legacy);
        $toReturn = array();

        foreach ($allPaths as $path) {

            $key            = str_replace('\\', '/', $path);
            $toReturn[$key] = sprintf('%s%stemplates%s%s', $rootDirectory, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);
        }

        return $toReturn;
    }

    private function _doFind($rootDirectory, $suffix)
    {
        $rootDirectory = $rootDirectory . DIRECTORY_SEPARATOR . 'templates';

        if ($this->shouldLog()) {

            $this->_logDebug(sprintf('Looking for <code>%s</code> files in <code>%s</code>', $suffix, $rootDirectory));
        }

        if (!is_dir($rootDirectory)) {

            if ($this->shouldLog()) {

                $this->_logDebug(sprintf('<code>%s</code> does not exist', $rootDirectory));
            }

            return array();
        }

        $finder   = $this->_finderFactory->createFinder()->files()->in($rootDirectory)->name('*' . $suffix);
        $toReturn = array();

        /**
         * @var $file SplFileInfo
         */
        foreach ($finder as $file) {

            $toReturn[] = ltrim(str_replace($rootDirectory, '', $file->getRealPath()), DIRECTORY_SEPARATOR);
        }

        if ($this->shouldLog()) {

            $this->_logDebug(sprintf('Found <code>%d</code> <code>%s</code> templates in <code>%s</code>', count($toReturn), $suffix, $rootDirectory));
        }

        return $toReturn;
    }

    private function _logDebug($msg)
    {
        $this->getLogger()->debug(sprintf('(Theme Factory) %s', $msg));
    }
}