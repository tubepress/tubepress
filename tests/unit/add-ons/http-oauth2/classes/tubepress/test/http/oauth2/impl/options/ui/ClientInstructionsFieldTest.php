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
 * @covers tubepress_http_oauth2_impl_options_ui_ClientInstructionsField<extended>
 */
class tubepress_test_http_oauth2_impl_options_ui_ClientInstructionsFieldTest extends tubepress_test_options_ui_impl_fields_templated_AbstractTemplatedFieldTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockRedirectCalculator;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockProvider;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPersistenceHelper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTranslator;

    protected function onAfterTemplateBasedFieldSetup()
    {
        $this->_mockRedirectCalculator = $this->mock('tubepress_http_oauth2_impl_util_RedirectionEndpointCalculator');
        $this->_mockPersistenceHelper  = $this->mock('tubepress_http_oauth2_impl_util_PersistenceHelper');
        $this->_mockProvider           = $this->mock(tubepress_spi_http_oauth_v2_Oauth2ProviderInterface::_);
        $this->_mockTranslator         = $this->mock(tubepress_api_translation_TranslatorInterface::_);
    }

    /**
     * @return string
     */
    protected function getExpectedTemplateName()
    {
        return 'options-ui/fields/oauth2/client-instructions';
    }

    /**
     * @return array
     */
    protected function getExpectedTemplateVariables()
    {
        $this->_mockProvider->shouldReceive('getName')->once()->andReturn('provider-name');
        $this->_mockProvider->shouldReceive('getTranslatedClientRegistrationInstructions')->once()->with($this->_mockTranslator)->andReturn(array('instructions'));
        $this->_mockProvider->shouldReceive('getTranslatedTermForClientId')->once()->with($this->_mockTranslator)->andReturn('the client id');
        $this->_mockProvider->shouldReceive('getTranslatedTermForClientSecret')->once()->with($this->_mockTranslator)->andReturn('the client secret');
        $this->_mockProvider->shouldReceive('getTranslatedTermForRedirectEndpoint')->once()->with($this->_mockTranslator)->andReturn('the endpoint');
        $this->_mockPersistenceHelper->shouldReceive('getClientId')->once()->with($this->_mockProvider)->andReturn('client-id');
        $this->_mockPersistenceHelper->shouldReceive('getClientSecret')->once()->with($this->_mockProvider)->andReturn('client-secret');
        $this->_mockRedirectCalculator->shouldReceive('getRedirectionEndpoint')->once()->with('provider-name')->andReturn('endpoint');

        return array(
            'clientId'         => 'client-id',
            'clientSecret'     => 'client-secret',
            'provider'         => $this->_mockProvider,
            'callbackUri'      => 'endpoint',
            'instructions'     => array('instructions'),
            'clientIdTerm'     => 'the client id',
            'clientSecretTerm' => 'the client secret',
            'callbackTerm'     => 'the endpoint',
        );
    }

    /**
     * @return tubepress_options_ui_impl_fields_templated_AbstractTemplatedField
     */
    protected function getSut()
    {
        $this->_mockProvider->shouldReceive('getName')->once()->andReturn('provider-name');

        return new tubepress_http_oauth2_impl_options_ui_ClientInstructionsField(

            $this->_mockProvider,
            $this->getMockPersistence(),
            $this->getMockHttpRequestParams(),
            $this->getMockTemplating(),
            $this->_mockPersistenceHelper,
            $this->_mockRedirectCalculator,
            $this->_mockTranslator
        );
    }
}
