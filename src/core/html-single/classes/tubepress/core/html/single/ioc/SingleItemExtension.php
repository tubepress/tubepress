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
class tubepress_core_html_single_ioc_SingleItemExtension implements tubepress_api_ioc_ContainerExtensionInterface
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
     * @since 4.0.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_core_html_single_impl_listeners_html_SingleVideoListener',
            'tubepress_core_html_single_impl_listeners_html_SingleVideoListener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_event_api_EventDispatcherInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_media_provider_api_CollectorInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_template_api_TemplateFactoryInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_html_api_Constants::EVENT_PRIMARY_HTML,
            'method'   => 'onHtmlGeneration',
            'priority' => 8000
        ));

        $containerBuilder->register(
            'tubepress_core_html_single_impl_listeners_template_SingleVideoCoreVariables',
            'tubepress_core_html_single_impl_listeners_template_SingleVideoCoreVariables'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_core_options_api_ContextInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_core_embedded_api_EmbeddedHtmlInterface::_))
         ->addTag(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_html_single_api_Constants::EVENT_SINGLE_ITEM_TEMPLATE,
            'method'   => 'onSingleVideoTemplate',
            'priority' => 10100
        ));

        $containerBuilder->setParameter(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_single', array(

            'defaultValues' => array(
                tubepress_core_html_single_api_Constants::OPTION_MEDIA_ITEM_ID => null,
            ),

            'doNotPersistNames' => array(
                tubepress_core_html_single_api_Constants::OPTION_MEDIA_ITEM_ID,
            )
        ));
    }
}