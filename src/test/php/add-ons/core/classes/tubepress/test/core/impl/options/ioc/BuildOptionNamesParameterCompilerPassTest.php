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
 * @covers tubepress_core_impl_options_ioc_BuildOptionNamesParameterCompilerPass<extended>
 */
class tubepress_test_core_impl_options_ioc_BuildOptionNamesParameterCompilerPassTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_options_ioc_BuildOptionNamesParameterCompilerPass
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainer;

    public function onSetup()
    {
        $this->_sut           = new tubepress_core_impl_options_ioc_BuildOptionNamesParameterCompilerPass();
        $this->_mockContainer = $this->mock('tubepress_api_ioc_ContainerBuilderInterface');
    }

    public function testProcess()
    {
        $mockOptionProviderReferences = array(

            'x' => array(tubepress_core_api_options_ProviderInterface::_)
        );

        $mockOptionProvider = $this->mock(tubepress_core_api_options_ProviderInterface::_);
        $mockOptionProvider->shouldReceive('getAllOptionNames')->once()->andReturn(array('one', 'two', 'three'));

        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_core_api_options_ProviderInterface::_)->andReturn($mockOptionProviderReferences);
        $this->_mockContainer->shouldReceive('get')->once()->with('x')->andReturn($mockOptionProvider);
        $this->_mockContainer->shouldReceive('setParameter')->once()->with('tubePressOptionNames', array('one', 'two', 'three'));

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }
}