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
class tubepress_addons_core_impl_ioc_compiler_LegacyOptionsPass implements tubepress_api_ioc_CompilerPassInterface
{
    /**
     * Provides add-ons with the ability to modify the TubePress IOC container builder
     * before it is compiled for production.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder The core IOC container builder.
     *
     * @throws InvalidArgumentException If a service tag doesn't include the event attribute.
     *
     * @api
     * @since 3.1.0
     */
    public function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $legacyOptionProviders = $containerBuilder->findTaggedServiceIds(tubepress_spi_options_PluggableOptionDescriptorProvider::_);

        foreach ($legacyOptionProviders as $serviceId => $tags) {

            /**
             * @var $service tubepress_spi_options_PluggableOptionDescriptorProvider
             */
            $service = $containerBuilder->get($serviceId);

            $optionDescriptors = $service->getOptionDescriptors();
            $labelMap          = $this->_buildLabelMap($optionDescriptors);
            $descriptionMap    = $this->_buildDescriptionMap($optionDescriptors);
            $valueMap          = $this->_buildValueMap($optionDescriptors);

            $definition = new tubepress_impl_ioc_Definition(
                'tubepress_impl_bc_LegacyOptionProvider',
                array($labelMap, $descriptionMap, $valueMap)
            );

            $definition->addTag(tubepress_spi_options_OptionProvider::_);

            $this->_buildShortcodeSettability($definition, $optionDescriptors);
            $this->_buildPersistability($definition, $optionDescriptors);
            $this->_buildAcceptableValues($definition, $optionDescriptors);
            $this->_buildRegexes($definition, $optionDescriptors);

            $containerBuilder->setDefinition($serviceId . '__converted', $definition);
        }
    }

    private function _buildRegexes(tubepress_api_ioc_DefinitionInterface $definition, array $optionDescriptors)
    {
        /**
         * @var $optionDescriptor tubepress_spi_options_OptionDescriptor
         */
        foreach ($optionDescriptors as $optionDescriptor) {

            $regex = $optionDescriptor->getValidValueRegex();

            if ($regex) {

                $definition->addMethodCall('setValidValueRegex', array($optionDescriptor->getName(), $regex));
            }
        }
    }

    private function _buildAcceptableValues(tubepress_api_ioc_DefinitionInterface $definition, array $optionDescriptors)
    {
        /**
         * @var $optionDescriptor tubepress_spi_options_OptionDescriptor
         */
        foreach ($optionDescriptors as $optionDescriptor) {

            $acceptableValues = $optionDescriptor->getAcceptableValues();

            if ($acceptableValues && is_array($acceptableValues) && count($acceptableValues) > 0) {

                $definition->addMethodCall('setAcceptableValues', array($optionDescriptor->getName(), $acceptableValues));
            }
        }
    }

    private function _buildPersistability(tubepress_api_ioc_DefinitionInterface $definition, array $optionDescriptors)
    {
        /**
         * @var $optionDescriptor tubepress_spi_options_OptionDescriptor
         */
        foreach ($optionDescriptors as $optionDescriptor) {

            $persist = $optionDescriptor->isMeantToBePersisted();

            if (!$persist) {

                $definition->addMethodCall('setOptionAsDoNotPersist', array($optionDescriptor->getName()));
            }
        }
    }

    private function _buildShortcodeSettability(tubepress_api_ioc_DefinitionInterface $definition, array $optionDescriptors)
    {
        /**
         * @var $optionDescriptor tubepress_spi_options_OptionDescriptor
         */
        foreach ($optionDescriptors as $optionDescriptor) {

            $canBeSetViaShortcode = $optionDescriptor->isAbleToBeSetViaShortcode();

            if (!$canBeSetViaShortcode) {

                $definition->addMethodCall('setOptionAsNonShortcodeSettable', array($optionDescriptor->getName()));
            }
        }
    }

    /**
     * @param tubepress_spi_options_OptionDescriptor[] $optionDescriptors
     *
     * @return array
     */
    private function _buildValueMap(array $optionDescriptors)
    {
        $toReturn = array();

        foreach ($optionDescriptors as $optionDescriptor) {

            $value = $optionDescriptor->getDefaultValue();

            $toReturn[$optionDescriptor->getName()] = $value;
        }

        return $toReturn;
    }

    /**
     * @param tubepress_spi_options_OptionDescriptor[] $optionDescriptors
     *
     * @return array
     */
    private function _buildDescriptionMap(array $optionDescriptors)
    {
        $toReturn = array();

        foreach ($optionDescriptors as $optionDescriptor) {

            $description = $optionDescriptor->getDescription();

            if ($description) {

                $toReturn[$optionDescriptor->getName()] = $description;
            }
        }

        return $toReturn;
    }

    /**
     * @param tubepress_spi_options_OptionDescriptor[] $optionDescriptors
     *
     * @return array
     */
    private function _buildLabelMap(array $optionDescriptors)
    {
        $toReturn = array();

        foreach ($optionDescriptors as $optionDescriptor) {

            $label = $optionDescriptor->getLabel();

            if ($label) {

                $toReturn[$optionDescriptor->getName()] = $label;
            }
        }

        return $toReturn;
    }


}