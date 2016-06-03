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

class tubepress_shortcode_ioc_ShortcodeExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerParser($containerBuilder);
        $this->_registerOptions($containerBuilder);
    }
    
    private function _registerParser(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_api_shortcode_ParserInterface::_,
            'tubepress_shortcode_impl_Parser'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_));
    }

    private function _registerOptions(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_options_Reference__shortcode',
            'tubepress_api_options_Reference'
        )->addArgument(array(
            tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                tubepress_api_options_Names::SHORTCODE_KEYWORD => 'tubepress',
            ),
            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                tubepress_api_options_Names::SHORTCODE_KEYWORD => 'Shortcode keyword',        //>(translatable)<,
            ),
            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                tubepress_api_options_Names::SHORTCODE_KEYWORD => 'The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.',   //>(translatable)<,
            ),
        ))->addTag(tubepress_api_options_ReferenceInterface::_);

        $toValidate = array(
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS => array(
                tubepress_api_options_Names::SHORTCODE_KEYWORD,
            ),
        );

        foreach ($toValidate as $type => $optionNames) {
            foreach ($optionNames as $optionName) {
                $containerBuilder->register(
                    'regex_validator.' . $optionName,
                    'tubepress_api_options_listeners_RegexValidatingListener'
                )->addArgument($type)
                 ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
                 ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
                 ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                     'event'    => tubepress_api_event_Events::OPTION_SET . ".$optionName",
                     'priority' => 100000,
                     'method'   => 'onOption',
                ));
            }
        }
    }
}
