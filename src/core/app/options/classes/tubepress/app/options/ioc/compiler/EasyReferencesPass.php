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
class tubepress_app_options_ioc_compiler_EasyReferencesPass extends tubepress_app_options_ioc_compiler_AbstractEasyPass
{
    private static $_KEY_DEFAULT_VALUES = 'defaultValues';
    private static $_KEY_LABELS         = 'labels';
    private static $_KEY_DESCRIPTIONS   = 'descriptions';
    private static $_KEY_PRO_NAMES      = 'proOptionNames';
    private static $_KEY_NO_PERSIST     = 'doNotPersistNames';
    private static $_KEY_NO_SHORTCODE   = 'noShortcodeNames';

    /**
     * @var array
     */
    private $_stringSets;

    /**
     * @var array
     */
    private $_stringMaps;

    protected function okToProcessParameter($paramName, array $param, tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_stringSets = array(

            self::$_KEY_PRO_NAMES    => 'setProOptionNames',
            self::$_KEY_NO_PERSIST   => 'setDoNotPersistOptions',
            self::$_KEY_NO_SHORTCODE => 'setNoShortcodeOptions'
        );

        $this->_stringMaps = array(

            self::$_KEY_LABELS       => 'setMapOfOptionNamesToUntranslatedLabels',
            self::$_KEY_DESCRIPTIONS => 'setMapOfOptionNamesToUntranslatedDescriptions'
        );

        $langUtils = $this->getLangUtils();

        return $this->_validStringSets($param, $langUtils) && $this->_validStringMaps($param, $langUtils);
    }

    protected function processParameter($paramName, array $paramValue, tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $definition = $containerBuilder->register(

            'easy_reference_for_' . $paramName,
            'tubepress_app_options_impl_easy_EasyReference'
        )->addArgument($paramValue[self::$_KEY_DEFAULT_VALUES])
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_LangUtilsInterface::_))
         ->addTag(tubepress_app_options_api_ReferenceInterface::_);

        $this->_addOptionalMethodCalls($definition, $paramValue);
    }

    private function _validStringSets(array $param, tubepress_platform_api_util_LangUtilsInterface $langUtils)
    {
        foreach (array_keys($this->_stringSets) as $key) {

            if (!isset($param[$key])) {

                continue;
            }

            if (!$langUtils->isSimpleArrayOfStrings($param[$key])) {

                return false;
            }
        }

        return true;
    }

    private function _validStringMaps(array $param, tubepress_platform_api_util_LangUtilsInterface $langUtils)
    {
        foreach (array_keys($this->_stringMaps) as $key) {

            if (!isset($param[$key])) {

                continue;
            }

            $value = $param[$key];

            if (!$langUtils->isAssociativeArray($value)) {

                return false;
            }

            foreach ($value as $subKey => $subValue) {

                if (!is_string($subKey) || !is_string($subValue)) {

                    return false;
                }
            }
        }

        return true;
    }

    private function _addOptionalMethodCalls(tubepress_platform_api_ioc_DefinitionInterface $definition, array $paramValue)
    {
        $map = array_merge($this->_stringMaps, $this->_stringSets);

        foreach ($map as $key => $methodName) {

            if (isset($paramValue[$key])) {

                $definition->addMethodCall($methodName, array($paramValue[$key]));
            }
        }
    }

    /**
     * @return string
     */
    protected function getParameterPrefix()
    {
        return tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE;
    }

    /**
     * @return string[]
     */
    protected function getRequiredKeyNames()
    {
        return array(

            self::$_KEY_DEFAULT_VALUES
        );
    }

    /**
     * @return string[]
     */
    protected function getOptionalKeyNames()
    {
        return array(

            self::$_KEY_LABELS,
            self::$_KEY_DESCRIPTIONS,
            self::$_KEY_NO_PERSIST,
            self::$_KEY_NO_SHORTCODE,
            self::$_KEY_PRO_NAMES
        );
    }
}