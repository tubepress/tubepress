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

class tubepress_http_oauth2_ioc_Oauth2Extension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $this->_registerOptions($containerBuilder);
        $this->_registerUtils($containerBuilder);
        $this->_registerPopups($containerBuilder);
        $this->_registerListener($containerBuilder);
        $this->_registerTemplatePathProvider($containerBuilder);
        $this->_registerOptionsUi($containerBuilder);
    }

    private function _registerListener(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_http_oauth2_impl_listeners_Oauth2Listener',
            'tubepress_http_oauth2_impl_listeners_Oauth2Listener'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_util_PersistenceHelper'))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_util_AccessTokenFetcher'))
         ->addTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
            'event'    => tubepress_api_http_Events::EVENT_HTTP_REQUEST,
            'priority' => 99000,       //API cache runs at 100K, so lets run after that
            'method'   => 'onHttpRequest', 
         ))->addTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_spi_http_oauth2_Oauth2ProviderInterface::_,
            'method' => 'setOauth2Providers',
         ));
    }

    private function _registerUtils(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_http_oauth2_impl_util_PersistenceHelper',
            'tubepress_http_oauth2_impl_util_PersistenceHelper'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_array_ArrayReaderInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_));

        $containerBuilder->register(
            'tubepress_http_oauth2_impl_util_AccessTokenFetcher',
            'tubepress_http_oauth2_impl_util_AccessTokenFetcher'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_HttpClientInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_util_PersistenceHelper'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_oauth2_Oauth2EnvironmentInterface::_));
    }

    private function _registerPopups(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_http_oauth2_impl_popup_AuthorizationInitiator',
            'tubepress_http_oauth2_impl_popup_AuthorizationInitiator')
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_ . '.admin'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_util_PersistenceHelper'))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_util_AccessTokenFetcher'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_oauth2_Oauth2EnvironmentInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_spi_http_oauth2_Oauth2ProviderInterface::_,
            'method' => 'setOauth2Providers',
        ));

        $containerBuilder->register(
            'tubepress_http_oauth2_impl_popup_RedirectionCallback',
            'tubepress_http_oauth2_impl_popup_RedirectionCallback'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_ . '.admin'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_util_PersistenceHelper'))
         ->addArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_util_AccessTokenFetcher'))
         ->addArgument(new tubepress_api_ioc_Reference(tubepress_api_http_oauth2_Oauth2EnvironmentInterface::_))
         ->addTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_spi_http_oauth2_Oauth2ProviderInterface::_,
            'method' => 'setOauth2Providers',
         ));
    }

    private function _registerOptions(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_options_Reference__oauth2',
            'tubepress_api_options_Reference'
        )->addTag(tubepress_api_options_ReferenceInterface::_)
         ->addArgument(array(
            tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                tubepress_api_options_Names::OAUTH2_TOKEN          => null,
                tubepress_api_options_Names::OAUTH2_TOKENS         => '{}',
                tubepress_api_options_Names::OAUTH2_CLIENT_DETAILS => '{}',
            ),
        ))->addArgument(array(
            tubepress_api_options_Reference::PROPERTY_NO_SHORTCODE => array(
                tubepress_api_options_Names::OAUTH2_TOKENS,
                tubepress_api_options_Names::OAUTH2_CLIENT_DETAILS,
            ),
        ));
    }

    private function _registerOptionsUi(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(

            'tubepress_http_oauth2_impl_options_ui_ClientCredentialsSavingField',
            'tubepress_api_options_ui_FieldInterface'
        )->setFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument('does-not-matter')
         ->addArgument('oauth2ClientCredentialsSaving')
         ->addTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
             'tag'    => tubepress_spi_http_oauth2_Oauth2ProviderInterface::_,
             'method' => 'setOauth2Providers',
         ));

        $containerBuilder->register(

            'tubepress_http_oauth2_impl_options_ui_TokenDeletionField',
            'tubepress_api_options_ui_FieldInterface'
        )->setFactoryService(tubepress_api_options_ui_FieldBuilderInterface::_)
         ->setFactoryMethod('newInstance')
         ->addArgument('does-not-matter')
         ->addArgument('oauth2TokenDeletion')
         ->addTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
            'tag'    => tubepress_spi_http_oauth2_Oauth2ProviderInterface::_,
            'method' => 'setOauth2Providers',
        ));

        $containerBuilder->register(
            'tubepress_spi_options_ui_FieldProviderInterface__oauth2',
            'tubepress_api_options_ui_BaseFieldProvider'
        )->addArgument('oauth2')
         ->addArgument('')
         ->addArgument(false)
         ->addArgument(false)
         ->addArgument(array())
         ->addArgument(array(
             new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_options_ui_ClientCredentialsSavingField'),
             new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_options_ui_TokenDeletionField'),
         ))
         ->addArgument(array())
         ->addTag('tubepress_spi_options_ui_FieldProviderInterface');
    }

    private function _registerTemplatePathProvider(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'tubepress_api_template_BasePathProvider__oauth2',
            'tubepress_api_template_BasePathProvider'
        )->addArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/http-oauth2/templates',
        ))->addTag('tubepress_spi_template_PathProviderInterface.admin');
    }
}
