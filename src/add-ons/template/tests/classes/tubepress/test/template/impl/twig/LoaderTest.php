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
 * @covers tubepress_template_impl_twig_ThemeLoader<extended>
 */
class tubepress_test_app_impl_template_twig_LoaderTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_template_impl_twig_ThemeLoader
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockThemeTemplateLocator;

    public function onSetup()
    {
        $this->_mockThemeTemplateLocator = $this->mock('tubepress_template_impl_ThemeTemplateLocator');

        $this->_sut = new tubepress_template_impl_twig_ThemeLoader($this->_mockThemeTemplateLocator);
    }

    /**
     * @dataProvider getFreshData
     */
    public function testIsFresh($exists)
    {
        $this->_mockThemeTemplateLocator->shouldReceive('exists')->once()->with('template-name')->andReturn($exists);

        if ($exists) {

            $this->_mockThemeTemplateLocator->shouldReceive('isFresh')->once()->with('template-name', 33)->andReturn(true);
            $this->assertTrue($this->_sut->isFresh('template-name', 33));

        } else {

            $this->setExpectedException('Twig_Error_Loader', 'Twig template template-name not found');
            $this->_sut->isFresh('template-name', 33);
        }
    }

    /**
     * @dataProvider getData
     */
    public function testGetSourceExists($exists, $method)
    {
        $this->_mockThemeTemplateLocator->shouldReceive('exists')->once()->with('template-name')->andReturn($exists);

        if ($exists) {

            $this->_mockThemeTemplateLocator->shouldReceive($method)->once()->with('template-name')->andReturn('x');
            $this->assertEquals('x', $this->_sut->$method('template-name'));

        } else {

            $this->setExpectedException('Twig_Error_Loader', 'Twig template template-name not found');
            $this->_sut->$method('template-name');
        }
    }

    public function getFreshData()
    {
        return array(
            array(true),
            array(false),
        );
    }

    public function getData()
    {
        return array(
            array(true, 'getSource'),
            array(false, 'getSource'),
            array(true, 'getCacheKey'),
            array(false, 'getCacheKey'),
        );
    }
}
