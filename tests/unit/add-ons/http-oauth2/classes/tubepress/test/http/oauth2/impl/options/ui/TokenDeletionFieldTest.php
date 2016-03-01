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
 * @covers tubepress_http_oauth2_impl_options_ui_TokenDeletionField<extended>
 */
class tubepress_test_http_oauth2_impl_options_ui_TokenDeletionFieldTest extends tubepress_test_options_ui_impl_fields_AbstractFieldTest
{
    /**
     * @var tubepress_http_oauth2_impl_options_ui_TokenDeletionField
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockPersistenceHelper;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockOauth2Provider;

    public function onAfterAbstractFieldSetup()
    {
        $this->_mockPersistenceHelper = $this->mock('tubepress_http_oauth2_impl_util_PersistenceHelper');
        $this->_mockOauth2Provider    = $this->mock(tubepress_spi_http_oauth2_Oauth2ProviderInterface::_);

        $this->_sut = new tubepress_http_oauth2_impl_options_ui_TokenDeletionField(
            $this->getMockPersistence(),
            $this->getMockHttpRequestParams(),
            $this->_mockPersistenceHelper
        );

        $this->_sut->setOauth2Providers(array($this->_mockOauth2Provider));
    }

    public function testSubmit()
    {
        $this->getMockPersistence()->shouldReceive('fetch')->once()->with(tubepress_api_options_Names::OAUTH2_TOKENS)->andReturn(json_encode(array(
            'provider-name' => array('id' => array('hi' => 'there'))
        )));

        $this->_mockOauth2Provider->shouldReceive('getName')->once()->andReturn('provider-name');

        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->atLeast(1)->with('oauth2-token-delete-provider-name')->andReturn(true);
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->atLeast(1)->with('oauth2-token-delete-provider-name')->andReturn(array('id'));

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with(tubepress_api_options_Names::OAUTH2_TOKENS, '{}');

        $actual = $this->_sut->onSubmit();

        $this->assertNull($actual);
    }
}
