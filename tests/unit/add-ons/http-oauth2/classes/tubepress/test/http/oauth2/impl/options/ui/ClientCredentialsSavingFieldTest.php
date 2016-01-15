<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_http_oauth2_impl_options_ui_ClientCredentialsSavingField<extended>
 */
class tubepress_test_http_oauth2_impl_options_ui_ClientCredentialsSavingFieldTest extends tubepress_test_options_ui_impl_fields_AbstractFieldTest
{
    /**
     * @var tubepress_http_oauth2_impl_options_ui_ClientCredentialsSavingField
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPersistenceHelper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOauth2Provider;

    public function onAfterAbstractFieldSetup()
    {
        $this->_mockPersistenceHelper = $this->mock('tubepress_http_oauth2_impl_util_PersistenceHelper');
        $this->_mockOauth2Provider    = $this->mock(tubepress_spi_http_oauth2_Oauth2ProviderInterface::_);

        $this->_sut = new tubepress_http_oauth2_impl_options_ui_ClientCredentialsSavingField(
            $this->getMockPersistence(),
            $this->getMockHttpRequestParams(),
            $this->_mockPersistenceHelper
        );

        $this->_sut->setOauth2Providers(array($this->_mockOauth2Provider));
    }

    public function testSubmit()
    {
        $this->getMockPersistence()->shouldReceive('fetch')->once()->with(tubepress_api_options_Names::OAUTH2_CLIENT_DETAILS)->andReturn(json_encode(array(
            'foo' => array('id' => 'someId', 'secret' => 'someSecret')
        )));

        $this->_mockOauth2Provider->shouldReceive('getName')->once()->andReturn('provider-name');
        $this->_mockOauth2Provider->shouldReceive('isClientSecretUsed')->once()->andReturn(true);

        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->atLeast(1)->with('clientId_provider-name')->andReturn(true);
        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->atLeast(1)->with('clientSecret_provider-name')->andReturn(true);
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->atLeast(1)->with('clientId_provider-name')->andReturn('new-id');
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->atLeast(1)->with('clientSecret_provider-name')->andReturn('new-secret');

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with(tubepress_api_options_Names::OAUTH2_CLIENT_DETAILS, '{"foo":{"id":"someId","secret":"someSecret"},"provider-name":{"id":"new-id","secret":"new-secret"}}');

        $actual = $this->_sut->onSubmit();

        $this->assertNull($actual);
    }
}
