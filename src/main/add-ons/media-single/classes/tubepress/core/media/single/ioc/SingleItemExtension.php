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
class tubepress_core_media_single_ioc_SingleItemExtension implements tubepress_api_ioc_ContainerExtensionInterface
{

    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 3.2.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_core_media_single_impl_listeners_html_SingleVideoListener',
            'tubepress_core_media_single_impl_listeners_html_SingleVideoListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_provider_api_CollectorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_html_api_Constants::EVENT_PRIMARY_HTML,
            'method'   => 'onHtmlGeneration',
            'priority' => 8000
        ));

        $containerBuilder->register(
            'tubepress_core_media_single_impl_listeners_template_SingleVideoCoreVariables',
            'tubepress_core_media_single_impl_listeners_template_SingleVideoCoreVariables'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_embedded_api_EmbeddedHtmlInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_media_single_api_Constants::EVENT_SINGLE_ITEM_TEMPLATE,
            'method'   => 'onSingleVideoTemplate',
            'priority' => 10100
        ));

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_single', array(

            'defaultValues' => array(
                tubepress_core_media_single_api_Constants::OPTION_AUTHOR         => false,
                tubepress_core_media_single_api_Constants::OPTION_CATEGORY       => false,
                tubepress_core_media_single_api_Constants::OPTION_DATEFORMAT     => 'M j, Y',
                tubepress_core_media_single_api_Constants::OPTION_DESC_LIMIT     => 80,
                tubepress_core_media_single_api_Constants::OPTION_DESCRIPTION    => false,
                tubepress_core_media_single_api_Constants::OPTION_ID             => false,
                tubepress_core_media_single_api_Constants::OPTION_KEYWORDS       => false,
                tubepress_core_media_single_api_Constants::OPTION_LENGTH         => true,
                tubepress_core_media_single_api_Constants::OPTION_RELATIVE_DATES => false,
                tubepress_core_media_single_api_Constants::OPTION_TITLE          => true,
                tubepress_core_media_single_api_Constants::OPTION_UPLOADED       => false,
                tubepress_core_media_single_api_Constants::OPTION_URL            => false,
                tubepress_core_media_single_api_Constants::OPTION_VIEWS          => true,

                tubepress_core_media_single_api_Constants::OPTION_VIDEO          => null,
            ),

            'labels' => array(
                tubepress_core_media_single_api_Constants::OPTION_AUTHOR         => 'Author',           //>(translatable)<
                tubepress_core_media_single_api_Constants::OPTION_CATEGORY       => 'Category',         //>(translatable)<
                tubepress_core_media_single_api_Constants::OPTION_DATEFORMAT     => 'Date format',                //>(translatable)<
                tubepress_core_media_single_api_Constants::OPTION_DESC_LIMIT     => 'Maximum description length', //>(translatable)<
                tubepress_core_media_single_api_Constants::OPTION_DESCRIPTION    => 'Description',      //>(translatable)<
                tubepress_core_media_single_api_Constants::OPTION_ID             => 'ID',               //>(translatable)<
                tubepress_core_media_single_api_Constants::OPTION_KEYWORDS       => 'Keywords',         //>(translatable)<
                tubepress_core_media_single_api_Constants::OPTION_LENGTH         => 'Runtime',          //>(translatable)<
                tubepress_core_media_single_api_Constants::OPTION_RELATIVE_DATES => 'Use relative dates',         //>(translatable)<
                tubepress_core_media_single_api_Constants::OPTION_TITLE          => 'Title',            //>(translatable)<
                tubepress_core_media_single_api_Constants::OPTION_UPLOADED       => 'Date posted',      //>(translatable)<
                tubepress_core_media_single_api_Constants::OPTION_URL            => 'URL',              //>(translatable)<
                tubepress_core_media_single_api_Constants::OPTION_VIEWS          => 'View count',       //>(translatable)<
            ),

            'descriptions' => array(
                tubepress_core_media_single_api_Constants::OPTION_DATEFORMAT     => sprintf('Set the textual formatting of date information for videos. See <a href="%s" target="_blank">date</a> for examples.', "http://php.net/date"),    //>(translatable)<
                tubepress_core_media_single_api_Constants::OPTION_DESC_LIMIT     => 'Maximum number of characters to display in video descriptions. Set to 0 for no limit.', //>(translatable)<
                tubepress_core_media_single_api_Constants::OPTION_RELATIVE_DATES => 'e.g. "yesterday" instead of "November 3, 1980".',  //>(translatable)<
            ),

            'noPersistNames' => array(
                tubepress_core_media_single_api_Constants::OPTION_VIDEO,
            )
        ));

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_single', array(

            'priority' => 30000,
            'map'      => array(
                'nonNegativeInteger' => array(
                    tubepress_core_media_single_api_Constants::OPTION_DESC_LIMIT,
                )
            )
        ));
    }
}