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
 * @covers tubepress_core_impl_options_ioc_BaseOptionsProvidersPass<extended>
 */
class tubepress_test_core_impl_options_ioc_BaseOptionsProvidersPassTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_options_ioc_BaseOptionsProvidersPass
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainer;

    public function onSetup()
    {
        $this->_sut           = new tubepress_core_impl_options_ioc_BaseOptionsProvidersPass();
        $this->_mockContainer = $this->mock('tubepress_api_ioc_ContainerBuilderInterface');
    }

    public function testLoad()
    {
        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with(
            tubepress_core_api_options_EasyProviderInterface::_
        )->andReturn(array('id' => array('some', 'data')));

        $mockDefinition = $this->mock('tubepress_api_ioc_DefinitionInterface');
        $mockDefinition->shouldReceive('addArgument')->once()->with(ehough_mockery_Mockery::on(function ($arg) {
            return "$arg" === 'id';
        }))->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addArgument')->once()->with(ehough_mockery_Mockery::on(function ($arg) {
            return "$arg" === strtolower(tubepress_core_api_translation_TranslatorInterface::_);
        }))->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addArgument')->once()->with(ehough_mockery_Mockery::on(function ($arg) {
            return "$arg" === strtolower(tubepress_core_api_event_EventDispatcherInterface::_);
        }))->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addArgument')->once()->with(ehough_mockery_Mockery::on(function ($arg) {
            return "$arg" === strtolower(tubepress_api_util_LangUtilsInterface::_);
        }))->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addTag')->once()->with(tubepress_core_api_options_ProviderInterface::_)->andReturn($mockDefinition);
        $this->_mockContainer->shouldReceive('register')->once()->with(

            'options_provider_for_id',
            'tubepress_core_impl_options_BaseProvider'
        )->andReturn($mockDefinition);

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }

}