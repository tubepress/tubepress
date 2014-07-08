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
 * @covers tubepress_app_options_ui_ioc_compiler_FormCreatorPass<extended>
 */
class tubepress_test_app_options_ui_ioc_compiler_FormCreatorPassTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_options_ui_ioc_compiler_FormCreatorPass
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainerBuilder;

    public function onSetup()
    {
        $this->_mockContainerBuilder = $this->mock(tubepress_platform_api_ioc_ContainerBuilderInterface::_);

        $this->_sut = new tubepress_app_options_ui_ioc_compiler_FormCreatorPass();
    }

    public function testProcessOne()
    {
        $this->_mockContainerBuilder->shouldReceive('findTaggedServiceIds')
            ->once()->with(tubepress_app_options_ui_api_Constants::IOC_TAG_OPTIONS_PAGE_TEMPLATE)
            ->andReturn(array('x' => array()));

        $mockDefinition = $this->mock('tubepress_platform_api_ioc_DefinitionInterface');
        $this->_expectReference($mockDefinition, 'x');
        $this->_expectReference($mockDefinition, tubepress_app_environment_api_EnvironmentInterface::_);
        $this->_expectReference($mockDefinition, tubepress_app_options_api_PersistenceInterface::_);
        $this->_expectReference($mockDefinition, tubepress_lib_event_api_EventDispatcherInterface::_);
        $this->_expectReference($mockDefinition, tubepress_platform_api_util_StringUtilsInterface::_);
        $mockDefinition->shouldReceive('addTag')->once()->with(tubepress_lib_ioc_api_Constants::TAG_TAGGED_SERVICES_CONSUMER, array(
            'tag'    => 'tubepress_app_options_ui_api_FieldProviderInterface',
            'method' => 'setFieldProviders'
        ));

        $this->_mockContainerBuilder->shouldReceive('register')->once()->with(
            tubepress_app_options_ui_api_FormInterface::_,
            'tubepress_app_options_ui_impl_Form'
        )->andReturn($mockDefinition);

        $this->_sut->process($this->_mockContainerBuilder);

        $this->assertTrue(true);
    }

    private function _expectReference(ehough_mockery_mockery_MockInterface $mock, $r)
    {
        $mock->shouldReceive('addArgument')->once()->with(ehough_mockery_Mockery::on(function ($ref) use ($r) {

            return $ref instanceof tubepress_platform_api_ioc_Reference && "$ref" === strtolower($r);
        }))->andReturn($mock);
    }

    public function testProcessNoTemplates()
    {
        $this->_mockContainerBuilder->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_app_options_ui_api_Constants::IOC_TAG_OPTIONS_PAGE_TEMPLATE)->andReturn(array());

        $this->_sut->process($this->_mockContainerBuilder);

        $this->assertTrue(true);
    }

    public function testProcessTooManyTemplates()
    {
        $this->setExpectedException('LogicException', 'More than one template tagged for the options page. Blacklist one of the add-ons.');

        $this->_mockContainerBuilder->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_app_options_ui_api_Constants::IOC_TAG_OPTIONS_PAGE_TEMPLATE)->andReturn(array(
            'x' => array(), 'y' => array())
        );

        $this->_sut->process($this->_mockContainerBuilder);
    }
}