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

/**
 * @covers tubepress_http_oauth2_impl_Oauth2Environment
 */
class tubepress_test_http_oauth2_impl_Oauth2EnvironmentTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_http_oauth2_impl_Oauth2Environment
     */
    private $_sut;

    /**
     * @var \Mockery\MockInterface
     */
    private $_mockOauth2Provider;

    public function onSetup()
    {
        $this->_mockOauth2Provider = \Mockery::mock(tubepress_spi_http_oauth2_Oauth2ProviderInterface::_);
        $this->_sut                = new tubepress_http_oauth2_impl_Oauth2Environment();
    }

    public function getCsrfSecret()
    {
        $this->setExpectedException('LogicException');

        $this->_sut->getCsrfSecret();
    }

    public function testGetAuthzEndpoint()
    {
        $this->setExpectedException('LogicException');

        $this->_sut->getAuthorizationInitiationUrl($this->_mockOauth2Provider);
    }

    public function testGetRedirectionUrl()
    {
        $this->setExpectedException('LogicException');

        $this->_sut->getRedirectionUrl($this->_mockOauth2Provider);
    }

}
