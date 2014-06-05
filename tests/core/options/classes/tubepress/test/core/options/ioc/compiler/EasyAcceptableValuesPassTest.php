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
 * @covers tubepress_core_options_ioc_compiler_EasyAcceptableValuesPass<extended>
 */
class tubepress_test_core_options_ioc_compiler_EasyAcceptableValuesPassTest extends tubepress_test_core_options_ioc_compiler_AbstractEasyPassTest
{
    public function testFullProcess()
    {
        $this->prepareForProcessing(array(
            'optionName' => 'bla',
            'priority' => 4,
            'values' => array('foo', 'bar')
        ));

        $this->getMockLangUtils()->shouldReceive('isAssociativeArray')->once()->with(array('foo', 'bar'))->andReturn(true);

        $mockDefinition = $this->mock('tubepress_api_ioc_DefinitionInterface');
        $mockDefinition->shouldReceive('addArgument')->once()->with(array('foo', 'bar'))->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addTag')->once()->with(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_GET_ACCEPTABLE_VALUES . '.bla',
            'priority' => 4,
            'method'   => 'onAcceptableValues',
        ))->andReturn($mockDefinition);

        $this->getMockContainer()->shouldReceive('register')->once()->with('easy_acceptable_values_for_foo',
            'tubepress_core_options_impl_easy_EasyAcceptableValuesListener')->andReturn($mockDefinition);

        $this->doProcess();
    }

    public function testNonAssocValues()
    {
        $this->prepareForProcessing(array(
            'optionName' => 'bla',
            'priority' => 4,
            'values' => array('foo', 'bar')
        ));

        $this->getMockLangUtils()->shouldReceive('isAssociativeArray')->once()->with(array('foo', 'bar'))->andReturn(false);
        $this->getMockLogger()->shouldReceive('error')->atLeast(1);

        $this->doProcess();
    }

    public function testNonNumericPriority()
    {
        $this->prepareForProcessing(array(
            'optionName' => 'bla',
            'priority' => new stdClass(),
            'values' => array('foo', 'bar')
        ));

        $this->getMockLogger()->shouldReceive('error')->atLeast(1);

        $this->doProcess();
    }

    public function testNonStringOptionName()
    {
        $this->prepareForProcessing(array(
            'optionName' => new stdClass(),
            'priority' => 30,
            'values' => array('foo', 'bar')
        ));

        $this->getMockLogger()->shouldReceive('error')->atLeast(1);

        $this->doProcess();
    }

    protected function buildSut()
    {
        return new tubepress_core_options_ioc_compiler_EasyAcceptableValuesPass();
    }

    protected function getPrefix()
    {
        return strtolower(tubepress_core_options_api_Constants::IOC_PARAM_EASY_ACCEPTABLE_VALUES);
    }

    protected function getRequiredAttributes()
    {
        return array('optionName', 'priority', 'values');
    }
}