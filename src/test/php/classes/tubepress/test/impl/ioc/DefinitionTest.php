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
 * @covers tubepress_impl_ioc_Definition
 */
class tubepress_test_impl_ioc_DefinitionTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_ioc_Definition
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_ioc_Definition('foo', array('x', 'z'));
    }

    public function testAddMethodCallBad()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->_sut->addMethodCall(null, array('a', 'b', 'c'));
    }

    public function testAddMethodCall()
    {
        $this->_sut->addMethodCall('foo', array('a', 'b', 'c'));

        $this->assertEquals(array(array('foo', array('a', 'b', 'c'))), $this->_sut->getMethodCalls());
    }

    public function testGetArgumentNotExist()
    {
        $this->setExpectedException('OutOfBoundsException');

        $this->_sut->getArgument(2);
    }

    public function testGetArgument()
    {
        $this->assertEquals('z', $this->_sut->getArgument(1));
    }

    public function testReplaceArg()
    {
        $this->_sut->replaceArgument(0, 'a');

        $this->assertEquals(array('a', 'z'), $this->_sut->getArguments());
    }

    public function testReplaceArgumentNotExist()
    {
        $this->setExpectedException('OutOfBoundsException');

        $this->_sut->replaceArgument(9, 'x');
    }

}
