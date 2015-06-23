<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_app_template_ioc_compiler_TemplatePathProvidersPass<extended>
 */
class tubepress_test_app_template_ioc_compiler_TemplatePathProvidersPassTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_template_ioc_compiler_TemplatePathProvidersPass
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainer;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTwigFsLoaderDefinition;

    public function onSetup()
    {
        $this->_sut                        = new tubepress_app_template_ioc_compiler_TemplatePathProvidersPass();
        $this->_mockContainer              = $this->mock('tubepress_platform_api_ioc_ContainerBuilderInterface');
        $this->_mockTwigFsLoaderDefinition = $this->mock('tubepress_platform_api_ioc_Definition');
    }

    public function testNoDispatcherService()
    {
        $this->_mockContainer->shouldReceive('hasDefinition')->once()->with('Twig_Loader_Filesystem')->andReturn(false);
        $this->_mockContainer->shouldReceive('hasDefinition')->once()->with('Twig_Loader_Filesystem.admin')->andReturn(false);
        $this->_sut->process($this->_mockContainer);
        $this->assertTrue(true);
    }

    public function testAddPaths()
    {
        $this->_mockContainer->shouldReceive('hasDefinition')->once()->with('Twig_Loader_Filesystem')->andReturn(true);
        $this->_mockContainer->shouldReceive('hasDefinition')->once()->with('Twig_Loader_Filesystem.admin')->andReturn(true);
        $this->_mockContainer->shouldReceive('getDefinition')->once()->with('Twig_Loader_Filesystem')->andReturn($this->_mockTwigFsLoaderDefinition);
        $this->_mockContainer->shouldReceive('getDefinition')->once()->with('Twig_Loader_Filesystem.admin')->andReturn($this->_mockTwigFsLoaderDefinition);
        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with('tubepress_lib_api_template_PathProviderInterface')
            ->andReturn(array(
                'foo' => array(),
            ));
        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with('tubepress_lib_api_template_PathProviderInterface.admin')
            ->andReturn(array(
                'bar' => array()
            ));

        $mockProvider1 = $this->mock('tubepress_lib_api_template_PathProviderInterface');
        $mockProvider2 = $this->mock('tubepress_lib_api_template_PathProviderInterface');

        $this->_mockContainer->shouldReceive('get')->once()->with('foo')->andReturn($mockProvider1);
        $this->_mockContainer->shouldReceive('get')->once()->with('bar')->andReturn($mockProvider2);

        $mockProvider1->shouldReceive('getTemplateDirectories')->once()->andReturn(array('/sdf'));
        $mockProvider2->shouldReceive('getTemplateDirectories')->once()->andReturn(array(sys_get_temp_dir()));

        $this->_mockTwigFsLoaderDefinition->shouldReceive('addMethodCall')->once()->with('addPath', array(sys_get_temp_dir()));

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }
}