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
class tubepress_core_media_item_ioc_compiler_EasyAttributeFormattersPass extends tubepress_core_options_ioc_compiler_AbstractEasyPass
{
    private static $_validTypes = array(

        'number', 'truncateString', 'durationFromSeconds', 'dateFromUnixTime', 'implodeArray'
    );

    protected function okToProcessParameter($paramName,
                                            array $param,
                                            tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        if (!is_numeric($param['priority'])) {

            return false;
        }

        if (!is_array($param['map'])) {

            return false;
        }

        if ($this->getLangUtils()->isAssociativeArray($param['map'])) {

            return false;
        }

        foreach ($param['map'] as $element) {

            if (!is_array($element)) {

                return false;
            }

            if (count($element) < 3) {

                return false;
            }

            if (!is_string($element[0]) || !is_string($element[1])) {

                return false;
            }

            if (!in_array($element[2], self::$_validTypes)) {

                return false;
            }
        }

        if (isset($param['providerName']) && !is_string($param['providerName'])) {

            return false;
        }

        return true;
    }

    protected function processParameter($paramName,
                                        array $paramValue,
                                        tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $def = $containerBuilder->register(

            'easy_attributes_formatter_for_' . $paramName,
            'tubepress_core_media_item_impl_easy_EasyAttributeFormatter'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_util_api_TimeUtilsInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_media_provider_api_Constants::EVENT_NEW_MEDIA_ITEM,
            'method'   => 'onNewMediaItem',
            'priority' => $paramValue['priority']
        ));

        if (isset($paramValue['providerName'])) {

            $def->addMethodCall('setProviderName', array($paramValue['providerName']));
        }

        foreach ($paramValue['map'] as $array) {

            $source      = $array[0];
            $destination = $array[1];
            $type        = $array[2];

            switch ($type) {

                case 'number':
                    $def->addMethodCall('formatNumber', array($source, $destination, $array[3]));
                    break;
                case 'truncateString':
                    $def->addMethodCall('truncateString', array($source, $destination, $array[3]));
                    break;
                case 'durationFromSeconds':
                    $def->addMethodCall('formatDurationFromSeconds', array($source, $destination));
                    break;
                case 'dateFromUnixTime':
                    $def->addMethodCall('formatDateFromUnixTime', array($source, $destination));
                    break;
                case 'implodeArray':
                    $def->addMethodCall('implodeArray', array($source, $destination, $array[3]));
                    break;
                default:
                    throw new LogicException();
            }
        }
    }

    /**
     * @return string
     */
    protected function getParameterPrefix()
    {
        return tubepress_core_media_item_api_Constants::IOC_PARAM_EASY_ATTRIBUTE_FORMATTER;
    }

    /**
     * @return string[]
     */
    protected function getRequiredKeyNames()
    {
        return array(

            'map',
            'priority'
        );
    }

    /**
     * @return string[]
     */
    protected function getOptionalKeyNames()
    {
        return array(
            'providerName'
        );
    }
}