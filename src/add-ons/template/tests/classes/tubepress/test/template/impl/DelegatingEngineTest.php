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
 * @covers tubepress_template_impl_DelegatingEngine<extended>
 */
class tubepress_test_impl_template_DelegatingEngineTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_template_impl_DelegatingEngine
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockLogger;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEngine1;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEngine2;

    public function onSetup()
    {
        $this->_mockLogger  = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockEngine1 = $this->mock('\Symfony\Component\Templating\EngineInterface');
        $this->_mockEngine2 = $this->mock('\Symfony\Component\Templating\EngineInterface');

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1)->andReturn(true);

        $this->_sut = new tubepress_template_impl_DelegatingEngine(

            array($this->_mockEngine1, $this->_mockEngine2),
            $this->_mockLogger
        );
    }

    public function testRender()
    {
        $this->_mockEngine1->shouldReceive('supports')->once()->with('foobar')->andReturn(false);
        $this->_mockEngine2->shouldReceive('supports')->once()->with('foobar')->andReturn(true);
        $this->_mockEngine2->shouldReceive('exists')->once()->with('foobar')->andReturn(true);
        $this->_mockEngine2->shouldReceive('render')->once()->with('foobar', array('foo' => 'bar'))->andReturn('x');

        $actual = $this->_sut->render('foobar', array('foo' => 'bar'));

        $this->assertEquals('x', $actual);
    }

    public function testGetEngine()
    {
        $this->_mockEngine1->shouldReceive('supports')->once()->with('foobar')->andReturn(false);
        $this->_mockEngine2->shouldReceive('supports')->once()->with('foobar')->andReturn(true);

        $this->_mockEngine2->shouldReceive('exists')->once()->with('foobar')->andReturn(true);

        $actual = $this->_sut->getEngine('foobar');

        $this->assertSame($this->_mockEngine2, $actual);
    }

    public function testGetEngineTemplateNotExists()
    {
        $this->setExpectedException('RuntimeException', 'Template <code>foobar</code> not found.');

        $this->_mockEngine1->shouldReceive('supports')->once()->with('foobar')->andReturn(false);
        $this->_mockEngine2->shouldReceive('supports')->once()->with('foobar')->andReturn(true);

        $this->_mockEngine2->shouldReceive('exists')->once()->with('foobar')->andReturn(false);

        $this->_sut->getEngine('foobar');
    }

    public function testGetEngineNoEngineSupports()
    {
        $this->setExpectedException('RuntimeException', 'Template <code>foobar</code> not found.');

        $this->_mockEngine1->shouldReceive('supports')->once()->with('foobar')->andReturn(false);
        $this->_mockEngine2->shouldReceive('supports')->once()->with('foobar')->andReturn(false);

        $this->_sut->getEngine('foobar');
    }
}
