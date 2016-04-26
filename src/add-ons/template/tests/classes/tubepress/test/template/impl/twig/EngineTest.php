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
 * @covers tubepress_template_impl_twig_Engine<extended>
 */
class tubepress_test_app_impl_template_twig_EngineTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_template_impl_twig_Engine
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockTwigEnvironment;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockTwigLoader;

    public function onSetup()
    {
        $this->_mockTwigEnvironment = $this->mock('Twig_Environment');
        $this->_mockTwigLoader      = $this->mock('Twig_LoaderInterface');

        $this->_sut = new tubepress_template_impl_twig_Engine($this->_mockTwigEnvironment);
    }

    public function testRenderNotExists()
    {
        $mockTemplate = $this->mock('Twig_Template');

        $this->_mockTwigEnvironment->shouldReceive('loadTemplate')->once()->with('template-name.html.twig')->andThrow(new Twig_Error_Loader('hi'));
        $params = array('foo' => 'bar');

        $this->setExpectedException('InvalidArgumentException', 'hi');

        $this->_sut->render('template-name', $params);
    }

    public function testRenderExists()
    {
        $mockTemplate = $this->mock('Twig_Template');

        $this->_mockTwigEnvironment->shouldReceive('loadTemplate')->once()->with('template-name.html.twig')->andReturn($mockTemplate);
        $params = array('foo' => 'bar');

        $mockTemplate->shouldReceive('render')->once()->with($params)->andReturn('hi');

        $actual = $this->_sut->render('template-name', $params);

        $this->assertEquals('hi', $actual);
    }

    public function testSupportsFalse()
    {
        $this->_mockTwigEnvironment->shouldReceive('getLoader')->once()->andReturn($this->_mockTwigLoader);
        $this->_mockTwigLoader->shouldReceive('getSource')->once()->with('template-name.html.twig')->andThrow(new Twig_Error_Loader('hi'));

        $this->assertFalse($this->_sut->supports('template-name'));
    }

    public function testSupportsTrue()
    {
        $this->_mockTwigEnvironment->shouldReceive('getLoader')->once()->andReturn($this->_mockTwigLoader);
        $this->_mockTwigLoader->shouldReceive('getSource')->once()->with('template-name.html.twig')->andReturn('xyz');

        $this->assertTrue($this->_sut->supports('template-name'));
    }

    public function testSupportsTrue2()
    {
        $mockTemplate = $this->mock('Twig_Template');

        $this->assertTrue($this->_sut->supports($mockTemplate));
    }
}
