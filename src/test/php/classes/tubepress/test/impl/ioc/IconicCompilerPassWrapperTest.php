<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_impl_ioc_IconicCompilerPassWrapperTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_ioc_IconicCompilerPassWrapper
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockPass;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainer;

    public function onSetup()
    {
        $this->_mockPass = ehough_mockery_Mockery::mock(tubepress_api_ioc_CompilerPassInterface::_);
        $this->_mockContainer = ehough_mockery_Mockery::mock(tubepress_api_ioc_ContainerInterface::_);

        $this->_sut = new tubepress_impl_ioc_IconicCompilerPassWrapper($this->_mockPass);
    }

    public function testProcessWrongClass()
    {
        $this->setExpectedException('RuntimeException');

        $realContainer = new ehough_iconic_ContainerBuilder();

        $this->_sut->process($realContainer);
    }

    public function testProcess()
    {
        $realContainer = new tubepress_impl_ioc_CoreIocContainer();

        $this->_mockPass->shouldReceive('process')->once()->with($realContainer);

        $this->_sut->process($realContainer);

        $this->assertTrue(true);
    }
}