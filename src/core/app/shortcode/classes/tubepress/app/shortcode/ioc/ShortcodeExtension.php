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
class tubepress_app_shortcode_ioc_ShortcodeExtension implements tubepress_platform_api_ioc_ContainerExtensionInterface
{

    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_platform_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_platform_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function load(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            tubepress_app_shortcode_api_ParserInterface::_,
            'tubepress_app_shortcode_impl_Parser'
        )->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_ContextInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
         ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_));

        $containerBuilder->setParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_REFERENCE . '_shortcode', array(

            'defaultValues' => array(
                tubepress_app_shortcode_api_Constants::OPTION_KEYWORD => 'tubepress',
            ),

            'labels' => array(
                tubepress_app_shortcode_api_Constants::OPTION_KEYWORD     => 'Shortcode keyword',  //>(translatable)<
            ),

            'descriptions' => array(
                tubepress_app_shortcode_api_Constants::OPTION_KEYWORD     => 'The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.', //>(translatable)<
            )
        ));

        $containerBuilder->setParameter(tubepress_app_options_api_Constants::IOC_PARAM_EASY_VALIDATION . '_shortcode', array(

            'priority' => 30000,
            'map'      => array(
                'oneOrMoreWordChars' => array(
                    tubepress_app_shortcode_api_Constants::OPTION_KEYWORD
                )
            )
        ));
    }
}