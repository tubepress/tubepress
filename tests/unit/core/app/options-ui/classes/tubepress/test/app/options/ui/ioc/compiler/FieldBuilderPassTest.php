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
 * @covers tubepress_app_options_ui_ioc_compiler_FieldBuilderPass<extended>
 */
class tubepress_test_app_options_ui_ioc_compiler_FieldBuilderPassTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_options_ui_ioc_compiler_FieldBuilderPass
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainerBuilder;

    public function onSetup()
    {
        $this->_mockContainerBuilder = $this->mock(tubepress_platform_api_ioc_ContainerBuilderInterface::_);

        $this->_sut = new tubepress_app_options_ui_ioc_compiler_FieldBuilderPass();
    }

    public function testProcess()
    {
        $mockFieldBuilderDef = $this->mock(tubepress_app_options_ui_api_FieldBuilderInterface::_);
        $mockFieldBuilderDef->shouldReceive('addMethodCall')->once()->with('setThemeRegistry', ehough_mockery_Mockery::on(function ($ref) {

            $ref = $ref[0];
            return $ref instanceof tubepress_platform_api_ioc_Reference && "$ref" === 'x';
        }));
        $this->_mockContainerBuilder->shouldReceive('hasDefinition')->once()->with(tubepress_app_options_ui_api_FieldBuilderInterface::_)->andReturn(true);
        $this->_mockContainerBuilder->shouldReceive('getDefinition')->once()->with(tubepress_app_options_ui_api_FieldBuilderInterface::_)->andReturn($mockFieldBuilderDef);
        $this->_mockContainerBuilder->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_platform_api_contrib_RegistryInterface::_)
            ->andReturn(array('x' => array(array('type' => tubepress_app_theme_api_ThemeInterface::_))));

        $this->_sut->process($this->_mockContainerBuilder);

        $this->assertTrue(true);
    }

    public function testProcessNothingToDoNoFieldBuilder()
    {
        $this->_mockContainerBuilder->shouldReceive('hasDefinition')->once()->with(tubepress_app_options_ui_api_FieldBuilderInterface::_)->andReturn(false);

        $this->_sut->process($this->_mockContainerBuilder);

        $this->assertTrue(true);
    }

    /**
     * @dataProvider getDataNothingToDo
     */
    public function testProcessNothingToDo($taggedMap)
    {
        $this->_mockContainerBuilder->shouldReceive('hasDefinition')->once()->with(tubepress_app_options_ui_api_FieldBuilderInterface::_)->andReturn(true);
        $this->_mockContainerBuilder->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_platform_api_contrib_RegistryInterface::_)
            ->andReturn($taggedMap);
        $this->_sut->process($this->_mockContainerBuilder);

        $this->assertTrue(true);
    }

    public function getDataNothingToDo()
    {
        return array(

            array(array()),
            array(array('x' => array())),
            array(array('x' => array(array()))),
            array(array('x' => array(array('type' => 'foo')))),
        );
    }
}