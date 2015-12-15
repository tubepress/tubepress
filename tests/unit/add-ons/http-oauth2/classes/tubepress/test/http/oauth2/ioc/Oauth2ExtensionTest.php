<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_http_oauth2_ioc_Oauth2Extension
 */
class tubepress_test_http_oauth2_ioc_Oauth2ExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_http_oauth2_ioc_Oauth2Extension
     */
    protected function buildSut()
    {
        return  new tubepress_http_oauth2_ioc_Oauth2Extension();
    }

    protected function prepareForLoad()
    {
        $this->_registerOptions();
        $this->_registerUtils();
        $this->_registerPopups();
        $this->_registerListener();
        $this->_registerTemplatePathProvider();
    }

    private function _registerListener()
    {
        $this->expectRegistration(
            'tubepress_http_oauth2_impl_listeners_Oauth2Listener',
            'tubepress_http_oauth2_impl_listeners_Oauth2Listener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_util_PersistenceHelper'))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_util_AccessTokenFetcher'))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . '.' . tubepress_api_options_Names::OAUTH2_TOKEN,
                'priority' => 100000,
                'method'   => 'onAcceptableValues'
            ));
    }

    private function _registerUtils()
    {
        $this->expectRegistration(
            'tubepress_http_oauth2_impl_util_RedirectionEndpointCalculator',
            'tubepress_http_oauth2_impl_util_RedirectionEndpointCalculator'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));

        $this->expectRegistration(
            'tubepress_http_oauth2_impl_util_PersistenceHelper',
            'tubepress_http_oauth2_impl_util_PersistenceHelper'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_PersistenceInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_array_ArrayReaderInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_));

        $this->expectRegistration(
            'tubepress_http_oauth2_impl_util_AccessTokenFetcher',
            'tubepress_http_oauth2_impl_util_AccessTokenFetcher'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_HttpClientInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_util_PersistenceHelper'))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_util_RedirectionEndpointCalculator'));
    }

    private function _registerPopups()
    {
        $this->expectRegistration(
            'tubepress_http_oauth2_impl_popup_AuthorizationInitiator',
            'tubepress_http_oauth2_impl_popup_AuthorizationInitiator'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_NonceManagerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_util_RedirectionEndpointCalculator'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_util_PersistenceHelper'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_spi_http_oauth_v2_Oauth2ProviderInterface::_,
                'method' => 'setOauth2Providers',
            ));

        $this->expectRegistration(
            'tubepress_http_oauth2_impl_popup_RedirectionCallback',
            'tubepress_http_oauth2_impl_popup_RedirectionCallback'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_url_UrlFactoryInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_util_PersistenceHelper'))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_util_AccessTokenFetcher'))
            ->withTag(tubepress_api_ioc_ServiceTags::TAGGED_SERVICES_CONSUMER, array(
                'tag'    => tubepress_spi_http_oauth_v2_Oauth2ProviderInterface::_,
                'method' => 'setOauth2Providers',
            ));
    }

    private function _registerOptions()
    {
        $this->expectRegistration(
            'tubepress_api_options_Reference__oauth2',
            'tubepress_api_options_Reference'
        )->withTag(tubepress_api_options_ReferenceInterface::_)
            ->withArgument(array(

                tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                    tubepress_api_options_Names::OAUTH2_TOKEN          => null,
                    tubepress_api_options_Names::OAUTH2_TOKENS         => '{}',
                    tubepress_api_options_Names::OAUTH2_CLIENT_DETAILS => '{}',
                ),
            ))->withArgument(array(
                tubepress_api_options_Reference::PROPERTY_NO_SHORTCODE => array(
                    tubepress_api_options_Names::OAUTH2_TOKENS,
                    tubepress_api_options_Names::OAUTH2_CLIENT_DETAILS,
                ),
            ));
    }

    private function _registerTemplatePathProvider()
    {
        $this->expectRegistration(
            'tubepress_api_template_BasePathProvider__oauth2',
            'tubepress_api_template_BasePathProvider'
        )->withArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/http-oauth2/templates'
        ))->withTag('tubepress_spi_template_PathProviderInterface.admin');
    }

    protected function getExpectedExternalServicesMap()
    {
        $logger = $this->mock(tubepress_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        return array(
            tubepress_api_http_NonceManagerInterface::_       => tubepress_api_http_NonceManagerInterface::_,
            tubepress_api_http_RequestParametersInterface::_  => tubepress_api_http_RequestParametersInterface::_,
            tubepress_api_event_EventDispatcherInterface::_   => tubepress_api_event_EventDispatcherInterface::_,
            tubepress_api_template_TemplatingInterface::_     => tubepress_api_template_TemplatingInterface::_,
            tubepress_api_environment_EnvironmentInterface::_ => tubepress_api_environment_EnvironmentInterface::_,
            tubepress_api_options_PersistenceInterface::_     => tubepress_api_options_PersistenceInterface::_,
            tubepress_api_url_UrlFactoryInterface::_          => tubepress_api_url_UrlFactoryInterface::_,
            tubepress_api_http_HttpClientInterface::_         => tubepress_api_http_HttpClientInterface::_,
            tubepress_api_array_ArrayReaderInterface::_       => tubepress_api_array_ArrayReaderInterface::_,
            tubepress_api_options_ContextInterface::_         => tubepress_api_options_ContextInterface::_,
            tubepress_api_log_LoggerInterface::_              => $logger,
        );
    }
}
