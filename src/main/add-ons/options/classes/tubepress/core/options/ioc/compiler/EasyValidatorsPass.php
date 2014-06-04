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
class tubepress_core_options_ioc_compiler_EasyValidatorsPass extends tubepress_core_options_ioc_compiler_AbstractEasyPass
{
    private static $_KEY_MAP      = 'map';
    private static $_KEY_PRIORITY = 'priority';

    protected function okToProcessParameter($paramName, array $param, tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        if (!is_int($param[self::$_KEY_PRIORITY])) {

            return false;
        }

        if (!$this->getLangUtils()->isAssociativeArray($param[self::$_KEY_MAP])) {

            return false;
        }

        foreach ($param[self::$_KEY_MAP] as $type => $optionNames) {

            if (!is_string($type)) {

                return false;
            }

            if (!$this->getLangUtils()->isSimpleArrayOfStrings($optionNames)) {

                return false;
            }
        }

        return true;
    }

    protected function processParameter($paramName, array $paramValue, tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        foreach ($paramValue[self::$_KEY_MAP] as $type => $optionNames) {

            foreach ($optionNames as $optionName) {

                $containerBuilder->register(

                    'easy_validator_for_' . $paramName . '_' . $optionName,
                    'tubepress_core_options_impl_easy_EasyValidator'
                )->addArgument($type)
                 ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ReferenceInterface::_))
                 ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_translation_api_TranslatorInterface::_))
                 ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                    'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_SET . '.' . $optionName,
                    'priority' => $paramValue[self::$_KEY_PRIORITY],
                    'method'   => 'onOption',
                ));
            }
        }
    }

    /**
     * @return string
     */
    protected function getParameterPrefix()
    {
        return strtolower(tubepress_core_options_api_Constants::IOC_PARAM_EASY_VALIDATION);
    }

    /**
     * @return string[]
     */
    protected function getRequiredKeyNames()
    {
        return array(
            self::$_KEY_PRIORITY,
            self::$_KEY_MAP
        );
    }

    /**
     * @return string[]
     */
    protected function getOptionalKeyNames()
    {
        return array();
    }
}