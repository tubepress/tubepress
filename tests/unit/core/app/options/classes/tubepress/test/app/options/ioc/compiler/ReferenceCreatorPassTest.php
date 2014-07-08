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
 * @covers tubepress_app_options_ioc_compiler_ReferenceCreatorPass<extended>
 */
class tubepress_test_app_options_ioc_compiler_ReferenceCreatorPassTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_options_ioc_compiler_ReferenceCreatorPass
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainer;

    public function onSetup()
    {
        $this->_sut           = new tubepress_app_options_ioc_compiler_ReferenceCreatorPass();
        $this->_mockContainer = $this->mock('tubepress_platform_api_ioc_ContainerBuilderInterface');
    }

    public function testCreate()
    {
        $services = array(
            'id' => array(
                array()
            ),
            'id2' => array(
                array()
            )
        );
        $mockReference1 = $this->mock(tubepress_app_options_api_ReferenceInterface::_);
        $mockReference1->shouldReceive('getAllOptionNames')->times(6)->andReturn(array('a', 'pro', 'nopersist', 'noshortcode'));

        $mockReference1->shouldReceive('getDefaultValue')->once()->with('a')->andReturn('a default value');
        $mockReference1->shouldReceive('getDefaultValue')->once()->with('pro')->andReturn('pro default value');
        $mockReference1->shouldReceive('getDefaultValue')->once()->with('nopersist')->andReturn('nopersist default value');
        $mockReference1->shouldReceive('getDefaultValue')->once()->with('noshortcode')->andReturn('noshortcode default value');

        $mockReference1->shouldReceive('getUntranslatedLabel')->once()->with('a')->andReturn('a label');
        $mockReference1->shouldReceive('getUntranslatedLabel')->once()->with('pro')->andReturn(null);
        $mockReference1->shouldReceive('getUntranslatedLabel')->once()->with('nopersist')->andReturn('nopersist label');
        $mockReference1->shouldReceive('getUntranslatedLabel')->once()->with('noshortcode')->andReturn('noshortcode label');

        $mockReference1->shouldReceive('getUntranslatedDescription')->once()->with('a')->andReturn('a desc');
        $mockReference1->shouldReceive('getUntranslatedDescription')->once()->with('pro')->andReturn('pro desc');
        $mockReference1->shouldReceive('getUntranslatedDescription')->once()->with('nopersist')->andReturn('nopersist desc');
        $mockReference1->shouldReceive('getUntranslatedDescription')->once()->with('noshortcode')->andReturn(null);

        $mockReference1->shouldReceive('isProOnly')->once()->with('a')->andReturn(false);
        $mockReference1->shouldReceive('isProOnly')->once()->with('pro')->andReturn(true);
        $mockReference1->shouldReceive('isProOnly')->once()->with('nopersist')->andReturn(false);
        $mockReference1->shouldReceive('isProOnly')->once()->with('noshortcode')->andReturn(false);

        $mockReference1->shouldReceive('isMeantToBePersisted')->once()->with('a')->andReturn(true);
        $mockReference1->shouldReceive('isMeantToBePersisted')->once()->with('pro')->andReturn(true);
        $mockReference1->shouldReceive('isMeantToBePersisted')->once()->with('nopersist')->andReturn(false);
        $mockReference1->shouldReceive('isMeantToBePersisted')->once()->with('noshortcode')->andReturn(true);

        $mockReference1->shouldReceive('isAbleToBeSetViaShortcode')->once()->with('a')->andReturn(true);
        $mockReference1->shouldReceive('isAbleToBeSetViaShortcode')->once()->with('pro')->andReturn(true);
        $mockReference1->shouldReceive('isAbleToBeSetViaShortcode')->once()->with('nopersist')->andReturn(true);
        $mockReference1->shouldReceive('isAbleToBeSetViaShortcode')->once()->with('noshortcode')->andReturn(false);

        $mockReference2 = $this->mock(tubepress_app_options_api_ReferenceInterface::_);
        $mockReference2->shouldReceive('getAllOptionNames')->times(6)->andReturn(array('b'));
        $mockReference2->shouldReceive('getDefaultValue')->once()->with('b')->andReturn('b default value');
        $mockReference2->shouldReceive('getUntranslatedLabel')->once()->with('b')->andReturn('b label');
        $mockReference2->shouldReceive('getUntranslatedDescription')->once()->with('b')->andReturn('b desc');
        $mockReference2->shouldReceive('isProOnly')->once()->with('b')->andReturn(false);
        $mockReference2->shouldReceive('isMeantToBePersisted')->once()->with('b')->andReturn(true);
        $mockReference2->shouldReceive('isAbleToBeSetViaShortcode')->once()->with('b')->andReturn(true);

        $mockDefinition = $this->mock('tubepress_platform_api_ioc_DefinitionInterface');
        $mockDefinition->shouldReceive('addArgument')->once()->with(array(
            'a' => 'a default value',
            'pro' => 'pro default value',
            'nopersist' => 'nopersist default value',
            'noshortcode' => 'noshortcode default value',
            'b' => 'b default value',
        ))->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addArgument')->once()->with(array(
            'a' => 'a label',
            'nopersist' => 'nopersist label',
            'noshortcode' => 'noshortcode label',
            'b' => 'b label'
        ))->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addArgument')->once()->with(array(
            'a' => 'a desc',
            'pro' => 'pro desc',
            'nopersist' => 'nopersist desc',
            'b' => 'b desc',
        ))->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addArgument')->once()->with(array(
            'pro'
        ))->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addArgument')->once()->with(array(
            'nopersist'
        ))->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addArgument')->once()->with(array(
            'noshortcode'
        ))->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addArgument')->once()->with(ehough_mockery_Mockery::on(function ($arg) {

            return $arg instanceof tubepress_platform_api_ioc_Reference && "$arg" === strtolower(tubepress_lib_event_api_EventDispatcherInterface::_);
        }))->andReturn($mockDefinition);

        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_app_options_api_ReferenceInterface::_)->andReturn($services);
        $this->_mockContainer->shouldReceive('get')->once()->with('id')->andReturn($mockReference1);
        $this->_mockContainer->shouldReceive('get')->once()->with('id2')->andReturn($mockReference2);
        $this->_mockContainer->shouldReceive('register')->once()->with(

            tubepress_app_options_api_ReferenceInterface::_,
            'tubepress_app_options_impl_Reference'
        )->andReturn($mockDefinition);

        $this->_sut->process($this->_mockContainer);
        $this->assertTrue(true);
    }

}