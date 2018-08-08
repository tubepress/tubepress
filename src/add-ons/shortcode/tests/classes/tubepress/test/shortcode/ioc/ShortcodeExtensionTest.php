<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_shortcode_ioc_ShortcodeExtension
 */
class tubepress_test_shortcode_ioc_ShortcodeExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_shortcode_ioc_ShortcodeExtension
     */
    protected function buildSut()
    {
        return  new tubepress_shortcode_ioc_ShortcodeExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerParser();
        $this->_registerOptions();
    }

    private function _registerParser()
    {
        $this->expectRegistration(

            tubepress_api_shortcode_ParserInterface::_,
            'tubepress_shortcode_impl_Parser'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_));
    }

    private function _registerOptions()
    {
        $this->expectRegistration(
            'tubepress_api_options_Reference__shortcode',
            'tubepress_api_options_Reference'
        )->withArgument(array(
            tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                tubepress_api_options_Names::SHORTCODE_KEYWORD => 'tubepress',
            ),
            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                tubepress_api_options_Names::SHORTCODE_KEYWORD => 'Shortcode keyword',        //>(translatable)<,
            ),
            tubepress_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                tubepress_api_options_Names::SHORTCODE_KEYWORD => 'The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.',   //>(translatable)<,
            ),
        ))->withTag(tubepress_api_options_ReferenceInterface::_);

        $toValidate = array(
            tubepress_api_options_listeners_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS => array(
                tubepress_api_options_Names::SHORTCODE_KEYWORD,
            ),
        );

        foreach ($toValidate as $type => $optionNames) {
            foreach ($optionNames as $optionName) {
                $this->expectRegistration(
                    'regex_validator.' . $optionName,
                    'tubepress_api_options_listeners_RegexValidatingListener'
                )->withArgument($type)
                    ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ReferenceInterface::_))
                    ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_translation_TranslatorInterface::_))
                    ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                        'event'    => tubepress_api_event_Events::OPTION_SET . ".$optionName",
                        'priority' => 100000,
                        'method'   => 'onOption',
                    ));
            }
        }
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->once()->andReturn(true);

        return array(
            tubepress_api_log_LoggerInterface::_             => $logger,
            tubepress_api_options_ContextInterface::_        => tubepress_api_options_ContextInterface::_,
            tubepress_api_event_EventDispatcherInterface::_  => tubepress_api_event_EventDispatcherInterface::_,
            tubepress_api_util_StringUtilsInterface::_       => tubepress_api_util_StringUtilsInterface::_,
            tubepress_api_options_ReferenceInterface::_      => tubepress_api_options_ReferenceInterface::_,
            tubepress_api_translation_TranslatorInterface::_ => tubepress_api_translation_TranslatorInterface::_,
        );
    }
}
