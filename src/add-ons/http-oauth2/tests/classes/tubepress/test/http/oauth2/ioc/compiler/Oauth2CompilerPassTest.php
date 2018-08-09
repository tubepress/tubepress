<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_http_oauth2_ioc_compiler_Oauth2CompilerPass
 */
class tubepress_test_http_oauth2_ioc_compiler_Oauth2CompilerPassTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_http_oauth2_ioc_compiler_Oauth2CompilerPass
     */
    private $_sut;

    /**
     * @var \Mockery\MockInterface
     */
    private $_mockContainerBuilder;

    public function onSetup()
    {
        $this->_mockContainerBuilder = \Mockery::mock(tubepress_api_ioc_ContainerBuilderInterface::_);
        $this->_sut                  = new tubepress_http_oauth2_ioc_compiler_Oauth2CompilerPass();
    }

    public function testProcess()
    {
        $this->_mockContainerBuilder->shouldReceive('hasDefinition')->once()->with(tubepress_api_http_oauth2_Oauth2EnvironmentInterface::_)->andReturn(false);

        $this->_mockContainerBuilder->shouldReceive('register')->once()->with(

            tubepress_api_http_oauth2_Oauth2EnvironmentInterface::_,
            'tubepress_http_oauth2_impl_Oauth2Environment'
        );

        $this->_sut->process($this->_mockContainerBuilder);
    }

}
