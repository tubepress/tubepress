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
 * @covers tubepress_core_impl_template_contemplate_Template
 */
class tubepress_test_core_impl_template_contemplate_TemplateTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_template_contemplate_Template
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockDelegate;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLangUtils;

    public function onSetup()
    {
        $this->_mockDelegate  = $this->mock('ehough_contemplate_api_Template');
        $this->_mockLangUtils = $this->mock('tubepress_api_util_LangUtilsInterface');

        $this->_sut = new tubepress_core_impl_template_contemplate_Template(
            $this->_mockDelegate,
            $this->_mockLangUtils
        );
    }

    public function testDefaults()
    {
        $this->assertEquals(array(), $this->_sut->getVariables());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage tubepress_core_api_template_TemplateInterface::setContext() requires an associative array.
     */
    public function testSetContextNonAssociativeArray()
    {
        $this->_mockLangUtils->shouldReceive('isAssociativeArray')->once()->with(array('foo'))->andReturn(false);
        $this->_sut->setVariables(array('foo'));
    }

    public function testSetContext()
    {
        $this->_mockLangUtils->shouldReceive('isAssociativeArray')->once()->with(array('foo'))->andReturn(true);
        $this->_sut->setVariables(array('foo'));

        $this->assertEquals(array('foo'), $this->_sut->getVariables());
    }

    public function testGetSetVariable()
    {
        $this->assertFalse($this->_sut->hasVariable('foo'));
        $this->_sut->setVariable('foo', 'bar');
        $this->assertEquals('bar', $this->_sut->getVariable('foo'));
    }

    public function testToString()
    {
        $this->_mockDelegate->shouldReceive('reset')->once();
        $this->_mockDelegate->shouldReceive('setVariable')->once()->with('foo', 'bar');
        $this->_mockDelegate->shouldReceive('toString')->once()->andReturn('abc');

        $this->_mockLangUtils->shouldReceive('isAssociativeArray')->once()->with(array('foo' => 'bar'))->andReturn(true);

        $this->_sut->setVariables(array('foo' => 'bar'));

        $result = $this->_sut->toString();

        $this->assertEquals('abc', $result);
    }
}