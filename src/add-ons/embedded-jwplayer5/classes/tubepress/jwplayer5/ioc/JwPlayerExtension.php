<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_jwplayer5_ioc_JwPlayerExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerEmbeddedProvider($containerBuilder);
        $this->_registerListeners($containerBuilder);
        $this->_registerOptions($containerBuilder);
        $this->_registerOptionsUi($containerBuilder);
    }

    private function _registerEmbeddedProvider(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_jwplayer5_impl_embedded_JwPlayer5EmbeddedProvider',
            'tubepress_jwplayer5_impl_embedded_JwPlayer5EmbeddedProvider'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
         ->addTag('tubepress_spi_embedded_EmbeddedProviderInterface')
         ->addTag('tubepress_spi_template_PathProviderInterface');
    }

    private function _registerListeners(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $colors = array(
            tubepress_jwplayer5_api_OptionNames::COLOR_BACK,
            tubepress_jwplayer5_api_OptionNames::COLOR_FRONT,
            tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT,
            tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN,
        );

        foreach ($colors as $optionName) {

            $containerBuilder->register(
                'tubepress_api_options_listeners_RegexValidatingListener.' . $optionName,
                'tubepress_api_options_listeners_RegexValidatingListener'
            )->addArgument(tubepress_api_options_listeners_RegexValidatingListener::TYPE_STRING_HEXCOLOR)
             ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
             ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
             ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_SET . ".$optionName",
                'priority' => 98000,
                'method'   => 'onOption',
            ));

            $containerBuilder->register(
                'value_trimmer.' . $optionName,
                'tubepress_api_options_listeners_TrimmingListener'
            )->addArgument('#')
             ->addMethodCall('setModeToLtrim', array())
             ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'priority' => 100000,
                'method'   => 'onOption',
                'event'    => tubepress_api_event_Events::OPTION_SET . ".$optionName",
           ));
        }
    }

    private function _registerOptions(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_options_Reference__jwplayer5',
            'tubepress_api_options_Reference'
        )->addArgument(array(

            tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                tubepress_jwplayer5_api_OptionNames::COLOR_BACK   => 'FFFFFF',
                tubepress_jwplayer5_api_OptionNames::COLOR_FRONT  => '000000',
                tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT  => '000000',
                tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN => '000000',
            ),
            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                tubepress_jwplayer5_api_OptionNames::COLOR_BACK   => 'Background color', //>(translatable)<
                tubepress_jwplayer5_api_OptionNames::COLOR_FRONT  => 'Front color',     //>(translatable)<
                tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT  => 'Light color',     //>(translatable)<
                tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN => 'Screen color',    //>(translatable)<
            ),
            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                tubepress_jwplayer5_api_OptionNames::COLOR_BACK   => sprintf('Default is %s.', "FFFFFF"),   //>(translatable)<
                tubepress_jwplayer5_api_OptionNames::COLOR_FRONT  => sprintf('Default is %s.', "000000"),   //>(translatable)<
                tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT  => sprintf('Default is %s.', "000000"),   //>(translatable)<
                tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN => sprintf('Default is %s.', "000000"),   //>(translatable)<
            ),
        ))->addTag(tubepress_api_options_ReferenceInterface::_);
    }

    private function _registerOptionsUi(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $colors = array(
            tubepress_jwplayer5_api_OptionNames::COLOR_BACK,
            tubepress_jwplayer5_api_OptionNames::COLOR_FRONT,
            tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT,
            tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN,
        );

        $fieldIndex = 0;
        foreach ($colors as $color) {

            $containerBuilder->register(

                'jwplayer_field_' . $fieldIndex++,
                'tubepress_api_options_ui_FieldInterface'
            )->setFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
             ->setFactoryMethod('newInstance')
             ->addArgument($color)
             ->addArgument('spectrum');
        }

        $fieldReferences = array();
        for ($x = 0; $x < $fieldIndex; ++$x) {
            $fieldReferences[] = new tubepress_api_ioc_Reference('jwplayer_field_' . $x);
        }

        $fieldMap = array(

            tubepress_api_options_ui_CategoryNames::EMBEDDED => array(

                tubepress_jwplayer5_api_OptionNames::COLOR_BACK,
                tubepress_jwplayer5_api_OptionNames::COLOR_FRONT,
                tubepress_jwplayer5_api_OptionNames::COLOR_LIGHT,
                tubepress_jwplayer5_api_OptionNames::COLOR_SCREEN,
            ),
        );

        $containerBuilder->register(

            'tubepress_api_options_ui_BaseFieldProvider__jwplayer5',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->addArgument('field-provider-jwplayer5')
         ->addArgument('JW Player')
         ->addArgument(false)
         ->addArgument(true)
         ->addArgument(array())
         ->addArgument($fieldReferences)
         ->addArgument($fieldMap)
         ->addTag('tubepress_spi_options_ui_FieldProviderInterface');
    }
}
