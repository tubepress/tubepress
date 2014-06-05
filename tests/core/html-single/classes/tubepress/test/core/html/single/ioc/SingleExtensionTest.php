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
 * @covers tubepress_core_html_single_ioc_SingleItemExtension
 */
class tubepress_test_core_media_search_ioc_SingleExtensionTest extends tubepress_test_core_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_core_html_single_ioc_SingleItemExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(
            'tubepress_core_html_single_impl_listeners_html_SingleVideoListener',
            'tubepress_core_html_single_impl_listeners_html_SingleVideoListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_media_provider_api_CollectorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_html_api_Constants::EVENT_PRIMARY_HTML,
                'method'   => 'onHtmlGeneration',
                'priority' => 8000
            ));

        $this->expectRegistration(
            'tubepress_core_html_single_impl_listeners_template_SingleVideoCoreVariables',
            'tubepress_core_html_single_impl_listeners_template_SingleVideoCoreVariables'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_embedded_api_EmbeddedHtmlInterface::_))
            ->withTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
                'event'    => tubepress_core_html_single_api_Constants::EVENT_SINGLE_ITEM_TEMPLATE,
                'method'   => 'onSingleVideoTemplate',
                'priority' => 10100
            ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_single', array(

            'defaultValues' => array(
                tubepress_core_media_item_api_Constants::OPTION_AUTHOR         => false,
                tubepress_core_media_item_api_Constants::OPTION_CATEGORY       => false,
                tubepress_core_media_item_api_Constants::OPTION_DATEFORMAT     => 'M j, Y',
                tubepress_core_media_item_api_Constants::OPTION_DESC_LIMIT     => 80,
                tubepress_core_media_item_api_Constants::OPTION_DESCRIPTION    => false,
                tubepress_core_media_item_api_Constants::OPTION_ID             => false,
                tubepress_core_media_item_api_Constants::OPTION_KEYWORDS       => false,
                tubepress_core_media_item_api_Constants::OPTION_LENGTH         => true,
                tubepress_core_media_item_api_Constants::OPTION_RELATIVE_DATES => false,
                tubepress_core_media_item_api_Constants::OPTION_TITLE          => true,
                tubepress_core_media_item_api_Constants::OPTION_UPLOADED       => false,
                tubepress_core_media_item_api_Constants::OPTION_URL            => false,
                tubepress_core_media_item_api_Constants::OPTION_VIEWS          => true,

                tubepress_core_html_single_api_Constants::OPTION_VIDEO          => null,
            ),

            'labels' => array(
                tubepress_core_media_item_api_Constants::OPTION_AUTHOR         => 'Author',           //>(translatable)<
                tubepress_core_media_item_api_Constants::OPTION_CATEGORY       => 'Category',         //>(translatable)<
                tubepress_core_media_item_api_Constants::OPTION_DATEFORMAT     => 'Date format',                //>(translatable)<
                tubepress_core_media_item_api_Constants::OPTION_DESC_LIMIT     => 'Maximum description length', //>(translatable)<
                tubepress_core_media_item_api_Constants::OPTION_DESCRIPTION    => 'Description',      //>(translatable)<
                tubepress_core_media_item_api_Constants::OPTION_ID             => 'ID',               //>(translatable)<
                tubepress_core_media_item_api_Constants::OPTION_KEYWORDS       => 'Keywords',         //>(translatable)<
                tubepress_core_media_item_api_Constants::OPTION_LENGTH         => 'Runtime',          //>(translatable)<
                tubepress_core_media_item_api_Constants::OPTION_RELATIVE_DATES => 'Use relative dates',         //>(translatable)<
                tubepress_core_media_item_api_Constants::OPTION_TITLE          => 'Title',            //>(translatable)<
                tubepress_core_media_item_api_Constants::OPTION_UPLOADED       => 'Date posted',      //>(translatable)<
                tubepress_core_media_item_api_Constants::OPTION_URL            => 'URL',              //>(translatable)<
                tubepress_core_media_item_api_Constants::OPTION_VIEWS          => 'View count',       //>(translatable)<
            ),

            'descriptions' => array(
                tubepress_core_media_item_api_Constants::OPTION_DATEFORMAT     => sprintf('Set the textual formatting of date information for videos. See <a href="%s" target="_blank">date</a> for examples.', "http://php.net/date"),    //>(translatable)<
                tubepress_core_media_item_api_Constants::OPTION_DESC_LIMIT     => 'Maximum number of characters to display in video descriptions. Set to 0 for no limit.', //>(translatable)<
                tubepress_core_media_item_api_Constants::OPTION_RELATIVE_DATES => 'e.g. "yesterday" instead of "November 3, 1980".',  //>(translatable)<
            ),

            'doNotPersistNames' => array(
                tubepress_core_html_single_api_Constants::OPTION_VIDEO,
            )
        ));

        $this->expectParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_single', array(

            'priority' => 30000,
            'map'      => array(
                'nonNegativeInteger' => array(
                    tubepress_core_media_item_api_Constants::OPTION_DESC_LIMIT,
                )
            )
        ));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            tubepress_core_translation_api_TranslatorInterface::_ => tubepress_core_translation_api_TranslatorInterface::_,
            tubepress_core_options_api_ContextInterface::_ => tubepress_core_options_api_ContextInterface::_,
            tubepress_core_options_api_ReferenceInterface::_ => tubepress_core_options_api_ReferenceInterface::_,
            tubepress_core_event_api_EventDispatcherInterface::_ => tubepress_core_event_api_EventDispatcherInterface::_,
            tubepress_api_log_LoggerInterface::_ => tubepress_api_log_LoggerInterface::_,
            tubepress_core_media_provider_api_CollectorInterface::_ => tubepress_core_media_provider_api_CollectorInterface::_,
            tubepress_core_template_api_TemplateFactoryInterface::_ => tubepress_core_template_api_TemplateFactoryInterface::_,
            tubepress_core_embedded_api_EmbeddedHtmlInterface::_ => tubepress_core_embedded_api_EmbeddedHtmlInterface::_
        );
    }
}