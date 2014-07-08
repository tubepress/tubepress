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
class tubepress_app_options_ioc_compiler_EasyAcceptableValuesPass extends tubepress_app_options_ioc_compiler_AbstractEasyPass
{
    private static $_KEY_OPTION_NAME = 'optionName';
    private static $_KEY_PRIORITY    = 'priority';
    private static $_KEY_VALUES      = 'values';

    protected function okToProcessParameter($paramName, array $param, tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        if (!is_int($param[self::$_KEY_PRIORITY])) {

            return false;
        }

        if (!is_string($param[self::$_KEY_OPTION_NAME])) {

            return false;
        }

        if (!$this->getLangUtils()->isAssociativeArray($param[self::$_KEY_VALUES])) {

            return false;
        }

        return true;
    }

    protected function processParameter($paramName, array $paramValue, tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'easy_acceptable_values_for_' . $paramName,
            'tubepress_app_options_impl_easy_EasyAcceptableValuesListener'
        )->addArgument($paramValue[self::$_KEY_VALUES])
         ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.' . $paramValue[self::$_KEY_OPTION_NAME],
            'priority' => $paramValue[self::$_KEY_PRIORITY],
            'method'   => 'onAcceptableValues',
        ));
    }

    /**
     * @return string
     */
    protected function getParameterPrefix()
    {
        return tubepress_app_options_api_Constants::IOC_PARAM_EASY_ACCEPTABLE_VALUES;
    }

    /**
     * @return string[]
     */
    protected function getRequiredKeyNames()
    {
        return array(

            self::$_KEY_OPTION_NAME,
            self::$_KEY_PRIORITY,
            self::$_KEY_VALUES
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