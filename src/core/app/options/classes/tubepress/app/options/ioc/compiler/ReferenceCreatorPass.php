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
 * tubepress_app_options_impl_Reference needs the following passed into it:
 *
 *   array $mapOfOptionNamesToDefaultValues
 *   array $mapOfOptionNamesToLabels
 *   array $mapOfOptionNamesToDescriptions
 *   array $mapOfOptionNamesToAcceptableValues
 *   array $optionNamesThatArePro
 *   array $optionNamesThatShouldNotBePersisted
 *   array $optionNamesThatCannotBeSetViaShortcode
 *   tubepress_lib_event_api_EventDispatcherInterface
 */
class tubepress_app_options_ioc_compiler_ReferenceCreatorPass implements tubepress_platform_api_ioc_CompilerPassInterface
{
    /**
     * @param tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder The primary service container builder.
     *
     * @api
     * @since 4.0.0
     */
    public function process(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $services                               = $containerBuilder->findTaggedServiceIds(tubepress_app_options_api_ReferenceInterface::_);
        $mapOfOptionNamesToDefaultValues        = array();
        $mapOfOptionNamesToLabels               = array();
        $mapOfOptionNamesToDescriptions         = array();
        $optionNamesThatArePro                  = array();
        $optionNamesThatShouldNotBePersisted    = array();
        $optionNamesThatCannotBeSetViaShortcode = array();

        foreach ($services as $serviceId => $tags) {

            /**
             * @var $provider tubepress_app_options_api_ReferenceInterface
             */
            $provider = $containerBuilder->get($serviceId);

            $mapOfOptionNamesToDefaultValues        = array_merge($mapOfOptionNamesToDefaultValues, $this->_getDefaultValueMap($provider));
            $mapOfOptionNamesToLabels               = array_merge($mapOfOptionNamesToLabels, $this->_getLabelMap($provider));
            $mapOfOptionNamesToDescriptions         = array_merge($mapOfOptionNamesToDescriptions, $this->_getDescriptionMap($provider));
            $optionNamesThatArePro                  = array_merge($optionNamesThatArePro, $this->_getProOptionNames($provider));
            $optionNamesThatShouldNotBePersisted    = array_merge($optionNamesThatShouldNotBePersisted, $this->_getNoPersistNames($provider));
            $optionNamesThatCannotBeSetViaShortcode = array_merge($optionNamesThatCannotBeSetViaShortcode, $this->_getNoShortcodeNames($provider));
        }

        $containerBuilder->register(

            tubepress_app_options_api_ReferenceInterface::_,
            'tubepress_app_options_impl_Reference'
        )->addArgument($mapOfOptionNamesToDefaultValues)
         ->addArgument($mapOfOptionNamesToLabels)
         ->addArgument($mapOfOptionNamesToDescriptions)
         ->addArgument($optionNamesThatShouldNotBePersisted)
         ->addArgument($optionNamesThatCannotBeSetViaShortcode)
         ->addArgument($optionNamesThatArePro)
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_));
    }

    private function _getNoShortcodeNames(tubepress_app_options_api_ReferenceInterface $ref)
    {
        return $this->_set($ref, 'isAbleToBeSetViaShortcode', true);
    }

    private function _getNoPersistNames(tubepress_app_options_api_ReferenceInterface $ref)
    {
        return $this->_set($ref, 'isMeantToBePersisted', true);
    }

    private function _getProOptionNames(tubepress_app_options_api_ReferenceInterface $ref)
    {
        return $this->_set($ref, 'isProOnly');
    }

    private function _getDefaultValueMap(tubepress_app_options_api_ReferenceInterface $ref)
    {
        return $this->_map($ref, 'getDefaultValue', true);
    }

    private function _getDescriptionMap(tubepress_app_options_api_ReferenceInterface $ref)
    {
        return $this->_map($ref, 'getUntranslatedDescription');
    }

    private function _getLabelMap(tubepress_app_options_api_ReferenceInterface $ref)
    {
        return $this->_map($ref, 'getUntranslatedLabel');
    }

    private function _set(tubepress_app_options_api_ReferenceInterface $ref, $methodName, $inverse = false)
    {
        $toReturn    = array();
        $optionNames = $ref->getAllOptionNames();

        foreach ($optionNames as $optionName) {

            if ($ref->$methodName($optionName)) {

                $toReturn[] = $optionName;
            }
        }

        if ($inverse) {

            $toReturn = array_diff($optionNames, $toReturn);
        }

        return $toReturn;
    }

    private function _map(tubepress_app_options_api_ReferenceInterface $ref, $methodName, $allowNull = false)
    {
        $toReturn    = array();
        $optionNames = $ref->getAllOptionNames();

        foreach ($optionNames as $optionName) {

            $result = $ref->$methodName($optionName);

            if (!$allowNull && $result === null) {

                continue;
            }

            $toReturn[$optionName] = $result;
        }

        return $toReturn;
    }
}