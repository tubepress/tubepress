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
 * @covers tubepress_core_media_provider_ioc_compiler_EasyHttpProvidersPass<extended>
 */
class tubepress_test_core_media_provider_ioc_compiler_EasyHttpProvidersPassTest extends tubepress_test_TubePressUnitTest
{
    public function testProcess()
    {
        $sut = new tubepress_core_media_provider_ioc_compiler_EasyHttpProvidersPass();
        $mockContainerBuilder = $this->mock('tubepress_api_ioc_ContainerBuilderInterface');

        $mockContainerBuilder->shouldReceive('findTaggedServiceIds')->once()
            ->with('tubepress_core_media_provider_api_HttpProviderInterface')
            ->andReturn(array('x' => array(array('a'))));

        $mockDef = $this->mock('tubepress_api_ioc_DefinitionInterface');

        $mockContainerBuilder->shouldReceive('register')->once()->with(
            'http_video_provider_for_x',
            'tubepress_core_media_provider_impl_HttpMediaProvider'
        )->andReturn($mockDef);
        $this->_expectArgument($mockDef, 'x');
        $this->_expectArgument($mockDef, tubepress_api_log_LoggerInterface::_);
        $this->_expectArgument($mockDef, tubepress_core_event_api_EventDispatcherInterface::_);
        $this->_expectArgument($mockDef, tubepress_core_http_api_HttpClientInterface::_);
        $mockDef->shouldReceive('addTag')->once()->with(tubepress_core_media_provider_api_MediaProviderInterface::_);

        $sut->process($mockContainerBuilder);
        $this->assertTrue(true);
    }

    private function _expectArgument(ehough_mockery_mockery_MockInterface $mock, $argument)
    {
        $mock->shouldReceive('addArgument')->once()->with(ehough_mockery_Mockery::on(function ($arg) use ($argument) {

            return $arg instanceof tubepress_api_ioc_Reference && "$arg" === strtolower($argument);
        }))->andReturn($mock);
    }
}