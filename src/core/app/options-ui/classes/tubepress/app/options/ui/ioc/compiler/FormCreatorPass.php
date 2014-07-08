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
class tubepress_app_options_ui_ioc_compiler_FormCreatorPass implements tubepress_platform_api_ioc_CompilerPassInterface
{

    /**
     * @param tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder The primary service container builder.
     *
     * @api
     * @since 4.0.0
     */
    public function process(tubepress_platform_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $simpleProviderIds = $containerBuilder->findTaggedServiceIds(tubepress_app_options_ui_api_Constants::IOC_TAG_OPTIONS_PAGE_TEMPLATE);

        if (count($simpleProviderIds) > 1) {

            throw new LogicException('More than one template tagged for the options page. Blacklist one of the add-ons.');
        }

        foreach ($simpleProviderIds as $simpleProviderId => $tags) {

            $containerBuilder->register(

                tubepress_app_options_ui_api_FormInterface::_,
                'tubepress_app_options_ui_impl_Form'
            )->addArgument(new tubepress_platform_api_ioc_Reference($simpleProviderId))
             ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_environment_api_EnvironmentInterface::_))
             ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_options_api_PersistenceInterface::_))
             ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_event_api_EventDispatcherInterface::_))
             ->addArgument(new tubepress_platform_api_ioc_Reference(tubepress_platform_api_util_StringUtilsInterface::_))
             ->addTag(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
                'tag'    => 'tubepress_app_options_ui_api_FieldProviderInterface',
                'method' => 'setFieldProviders'
            ));
        }
    }
}