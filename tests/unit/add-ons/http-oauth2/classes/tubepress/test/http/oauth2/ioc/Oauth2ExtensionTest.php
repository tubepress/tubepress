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
        $this->_registerAuthorizationInitiator();
        $this->_registerRedirectionEndpointCalculator();
    }

    private function _registerRedirectionEndpointCalculator()
    {
        $this->expectRegistration(
            'tubepress_http_oauth2_impl_RedirectionEndpointCalculator',
            'tubepress_http_oauth2_impl_RedirectionEndpointCalculator'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_environment_EnvironmentInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_));
    }

    private function _registerAuthorizationInitiator()
    {
        $this->expectRegistration(
            'tubepress_http_oauth2_impl_AuthorizationInitiator',
            'tubepress_http_oauth2_impl_AuthorizationInitiator'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_NonceManagerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_http_RequestParametersInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference('tubepress_http_oauth2_impl_RedirectionEndpointCalculator'))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_event_EventDispatcherInterface::_))
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
                    tubepress_api_options_Names::OAUTH2_TOKENS => '{}',
                ),
            ))->withArgument(array(
                tubepress_api_options_Reference::PROPERTY_NO_SHORTCODE => array(
                    tubepress_api_options_Names::OAUTH2_TOKENS,
                ),
            ));
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(
            tubepress_api_http_NonceManagerInterface::_       => tubepress_api_http_NonceManagerInterface::_,
            tubepress_api_http_RequestParametersInterface::_  => tubepress_api_http_RequestParametersInterface::_,
            tubepress_api_event_EventDispatcherInterface::_   => tubepress_api_event_EventDispatcherInterface::_,
            tubepress_api_template_TemplatingInterface::_     => tubepress_api_template_TemplatingInterface::_,
            tubepress_api_environment_EnvironmentInterface::_ => tubepress_api_environment_EnvironmentInterface::_,
        );
    }
}
