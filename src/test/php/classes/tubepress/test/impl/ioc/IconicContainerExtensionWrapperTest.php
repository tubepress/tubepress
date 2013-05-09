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
class tubepress_test_impl_ioc_IconicContainerExtensionWrapperTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_ioc_IconicContainerExtensionWrapper
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExtension;

    public function onSetup()
    {
        $this->_mockExtension = ehough_mockery_Mockery::mock('tubepress_api_ioc_ContainerExtensionInterface');

        $this->_sut = new tubepress_impl_ioc_IconicContainerExtensionWrapper($this->_mockExtension);
    }

    public function testProcessWrongClass()
    {
        $this->setExpectedException('RuntimeException');

        $realContainer = new ehough_iconic_ContainerBuilder();

        $this->_sut->load(array(), $realContainer);
    }

    public function testProcess()
    {
        $realContainer = new tubepress_impl_ioc_CoreIocContainer();

        $this->_mockExtension->shouldReceive('load')->once()->with($realContainer);

        $this->_sut->load(array(), $realContainer);

        $this->assertTrue(true);
    }

    public function testXsd()
    {
        $this->assertNull($this->_sut->getXsdValidationBasePath());
    }
}