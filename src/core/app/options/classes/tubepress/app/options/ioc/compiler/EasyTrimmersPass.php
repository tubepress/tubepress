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
class tubepress_app_options_ioc_compiler_EasyTrimmersPass extends tubepress_app_options_ioc_compiler_AbstractEasyPass
{
    private static $_KEY_CHARLIST     = 'charlist';
    private static $_KEY_LTRIM        = 'ltrim';
    private static $_KEY_OPTION_NAMES = 'optionNames';
    private static $_KEY_PRIORITY     = 'priority';
    private static $_KEY_RTRIM        = 'rtrim';

    protected function okToProcessParameter($paramName, array $param, tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        if (!is_int($param[self::$_KEY_PRIORITY]) ||
            !$this->getLangUtils()->isSimpleArrayOfStrings($param[self::$_KEY_OPTION_NAMES]) ||
            !is_string($param[self::$_KEY_CHARLIST])) {

            return false;
        }

        if (isset($param[self::$_KEY_LTRIM]) && !is_bool($param[self::$_KEY_LTRIM])) {

            return false;
        }

        if (isset($param[self::$_KEY_RTRIM]) && !is_bool($param[self::$_KEY_RTRIM])) {

            return false;
        }

        if (isset($param[self::$_KEY_LTRIM]) && isset($param[self::$_KEY_RTRIM]) && $param[self::$_KEY_LTRIM] && $param[self::$_KEY_RTRIM]) {

            return false;
        }

        return true;
    }

    protected function processParameter($paramName, array $paramValue, tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        foreach ($paramValue[self::$_KEY_OPTION_NAMES] as $optionName) {

            $definition = $containerBuilder->register(

                'easy_trimmer_for_' . $paramName . '_' . $optionName,
                'tubepress_app_options_impl_easy_EasyTrimmer'
            )->addArgument($paramValue[self::$_KEY_CHARLIST])
             ->addTag(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                 'event'    => tubepress_app_options_api_Constants::EVENT_OPTION_SET . '.' . $optionName,
                 'priority' => $paramValue[self::$_KEY_PRIORITY],
                 'method'   => 'onOption',
             ));

            if (isset($paramValue[self::$_KEY_LTRIM]) && $paramValue[self::$_KEY_LTRIM]) {

                $definition->addMethodCall('setModeToLtrim');
            }

            if (isset($paramValue[self::$_KEY_RTRIM]) && $paramValue[self::$_KEY_RTRIM]) {

                $definition->addMethodCall('setModeToRtrim');
            }
        }
    }

    /**
     * @return string
     */
    protected function getParameterPrefix()
    {
        return tubepress_app_options_api_Constants::IOC_PARAM_EASY_TRIMMER;
    }

    /**
     * @return string[]
     */
    protected function getRequiredKeyNames()
    {
        return array(

            self::$_KEY_CHARLIST,
            self::$_KEY_OPTION_NAMES,
            self::$_KEY_PRIORITY
        );
    }

    /**
     * @return string[]
     */
    protected function getOptionalKeyNames()
    {
        return array(

            self::$_KEY_LTRIM,
            self::$_KEY_RTRIM
        );
    }
}