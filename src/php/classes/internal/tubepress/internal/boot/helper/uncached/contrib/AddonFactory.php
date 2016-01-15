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

/**
 * @api
 * @since 4.0.0
 */
class tubepress_internal_boot_helper_uncached_contrib_AddonFactory extends tubepress_internal_boot_helper_uncached_contrib_AbstractFactory
{
    private static $_FIRST_LEVEL_KEY_IOC      = 'container-builder';
    private static $_FIRST_LEVEL_KEY_AUTOLOAD = 'autoload';

    private static $_SECOND_LEVEL_KEY_CLASSMAP   = 'classmap';
    private static $_SECOND_LEVEL_KEY_PASSES     = 'compiler-passes';
    private static $_SECOND_LEVEL_KEY_EXTENSIONS = 'extensions';

    /**
     * @var tubepress_api_boot_BootSettingsInterface
     */
    private $_bootSettings;

    public function __construct(tubepress_api_log_LoggerInterface        $logger,
                                tubepress_api_url_UrlFactoryInterface    $urlFactory,
                                tubepress_api_util_LangUtilsInterface    $langUtils,
                                tubepress_api_util_StringUtilsInterface  $stringUtils,
                                tubepress_api_boot_BootSettingsInterface $bootSettings)
    {
        parent::__construct($logger, $urlFactory, $langUtils, $stringUtils);

        $this->_bootSettings = $bootSettings;
    }

    /**
     * @param string $manifestPath
     * @param array  $manifestData
     *
     * @return array
     */
    protected function normalizeAndReturnErrors($manifestPath, array &$manifestData)
    {
        $errors = array();

        $this->_handleBlacklist($manifestData, $errors);
        $this->_handleClassMap($manifestPath, $manifestData, $errors);
        $this->_handleIoc($manifestData, $errors);

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
        $addon = new tubepress_internal_contrib_Addon(
            $manifestData[self::$FIRST_LEVEL_KEY_NAME],
            $manifestData[self::$FIRST_LEVEL_KEY_VERSION],
            $manifestData[self::$FIRST_LEVEL_KEY_TITLE],
            $manifestData[self::$FIRST_LEVEL_KEY_AUTHORS],
            $manifestData[self::$FIRST_LEVEL_KEY_LICENSE]
        );

        $classMapKey       = self::$_SECOND_LEVEL_KEY_CLASSMAP;
        $extensionsKey     = self::$_SECOND_LEVEL_KEY_EXTENSIONS;
        $compilerPassesKey = self::$_SECOND_LEVEL_KEY_PASSES;
        $autoloadKey       = self::$_FIRST_LEVEL_KEY_AUTOLOAD;
        $iocKey            = self::$_FIRST_LEVEL_KEY_IOC;

        if (isset($manifestData[$autoloadKey][$classMapKey])) {

            $addon->setClassMap($manifestData[$autoloadKey][$classMapKey]);
        }

        if (isset($manifestData[$iocKey][$extensionsKey])) {

            $addon->setExtensions($manifestData[$iocKey][$extensionsKey]);
        }

        if (isset($manifestData[$iocKey][$compilerPassesKey])) {

            $addon->setCompilerPasses($manifestData[$iocKey][$compilerPassesKey]);
        }

        return $addon;
    }

    private function _handleBlacklist(array &$manifestData, array &$errors)
    {
        $name = $manifestData[self::$FIRST_LEVEL_KEY_NAME];

        if (in_array($name, $this->_bootSettings->getAddonBlacklistArray())) {

            $errors[] = "$name is blacklisted";
        }
    }

    private function _handleClassMap($manifestPath, array &$manifestData, array &$errors)
    {
        $classMapKey = self::$_SECOND_LEVEL_KEY_CLASSMAP;
        $autoloadKey = self::$_FIRST_LEVEL_KEY_AUTOLOAD;

        if (!isset($manifestData[$autoloadKey][$classMapKey])) {

            return;
        }

        $classMap = $manifestData[$autoloadKey][$classMapKey];

        if (!$this->getLangUtils()->isAssociativeArray($classMap)) {

            $errors[] = 'Classmap is non-associative array';
            return;
        }

        $toSet   = array();
        $rootDir = dirname($manifestPath);

        foreach ($classMap as $class => $path) {

            if (!is_string($class) || !is_string($path)) {

                $errors[] = 'Classmap contains non-string data';
                break;
            }

            $new           = $rootDir . DIRECTORY_SEPARATOR . $path;
            $toSet[$class] = $new;
        }

        $manifestData[$autoloadKey][$classMapKey] = $toSet;
    }

    private function _handleIoc(array &$manifestData, array &$errors)
    {
        if (!isset($manifestData[self::$_FIRST_LEVEL_KEY_IOC])) {

            return;
        }

        $this->_handleExtensions($manifestData, $errors);
        $this->_handleCompilerPasses($manifestData, $errors);
    }

    private function _handleExtensions(array &$manifestData, array &$errors)
    {
        $extensionsKey = self::$_SECOND_LEVEL_KEY_EXTENSIONS;
        $iocKey        = self::$_FIRST_LEVEL_KEY_IOC;

        if (!isset($manifestData[$iocKey][$extensionsKey])) {

            return;
        }

        $extensions = $manifestData[$iocKey][$extensionsKey];

        if (!$this->getLangUtils()->isSimpleArrayOfStrings($extensions)) {

            $errors[] = 'Invalid extensions';
        }
    }

    private function _handleCompilerPasses(array &$manifestData, array &$errors)
    {
        $compilerPassesKey = self::$_SECOND_LEVEL_KEY_PASSES;
        $iocKey            = self::$_FIRST_LEVEL_KEY_IOC;

        if (!isset($manifestData[$iocKey][$compilerPassesKey])) {

            return;
        }

        $passes = $manifestData[$iocKey][$compilerPassesKey];

        if (!$this->getLangUtils()->isAssociativeArray($passes)) {

            $errors[] = 'Compiler passes is a non-associative array';
            return;
        }

        foreach ($passes as $name => $priority) {

            if (!is_string($name) || !is_numeric($priority)) {

                $errors[] = 'Compiler passes contains invalid data';
                break;
            }
        }
    }
}