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
 * @covers tubepress_core_options_ioc_compiler_EasyTrimmersPass<extended>
 */
class tubepress_test_core_options_ioc_compiler_EasyTrimmersPassTest extends tubepress_test_core_options_ioc_compiler_AbstractEasyPassTest
{
    public function testFullPass()
    {
        $this->prepareForProcessing(array(
            'priority' => 33,
            'charlist' => 'dd',
            'optionNames' => array('foo'),
            'ltrim' => true
        ));
        $this->getMockLangUtils()->shouldReceive('isSimpleArrayOfStrings')->once()->with(array('foo'))->andReturn(true);

        $mockDefinition = $this->mock('tubepress_api_ioc_DefinitionInterface');
        $mockDefinition->shouldReceive('addArgument')->once()->with('dd')->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addTag')->once()->with(tubepress_core_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_core_options_api_Constants::EVENT_OPTION_SET . '.' . 'foo',
            'priority' => 33,
            'method'   => 'onOption',
        ))->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addMethodCall')->once()->with('setModeToLtrim')->andReturn($mockDefinition);

        $this->getMockContainer()->shouldReceive('register')->once()->with(
            'easy_trimmer_for_foo_foo',
            'tubepress_core_options_impl_easy_EasyTrimmer'
        )->andReturn($mockDefinition);

        $this->doProcess();

    }

    public function testBothTrimTypes()
    {
        $this->prepareForProcessing(array(
            'priority' => 33,
            'charlist' => 'dd',
            'optionNames' => array('foo'),
            'ltrim' => true,
            'rtrim' => true
        ));
        $this->getMockLangUtils()->shouldReceive('isSimpleArrayOfStrings')->once()->with(array('foo'))->andReturn(true);

        $this->getMockLogger()->shouldReceive('error')->atLeast(1);

        $this->doProcess();
    }

    /**
     * @dataProvider getDataNonBoolTrims
     */
    public function testNonBoolLtrim($type, $value)
    {
        $this->prepareForProcessing(array(
            'priority' => 33,
            'charlist' => 'dd',
            'optionNames' => array('foo'),
            $type => $value
        ));
        $this->getMockLangUtils()->shouldReceive('isSimpleArrayOfStrings')->once()->with(array('foo'))->andReturn(true);

        $this->getMockLogger()->shouldReceive('error')->atLeast(1);

        $this->doProcess();
    }

    public function getDataNonBoolTrims()
    {
        return array(

            array('ltrim', array()),
            array('rtrim', array())
        );
    }

    public function testNonStringCharlist()
    {
        $this->prepareForProcessing(array(
            'priority' => 33,
            'charlist' => new stdClass(),
            'optionNames' => array('foo')
        ));
        $this->getMockLangUtils()->shouldReceive('isSimpleArrayOfStrings')->once()->with(array('foo'))->andReturn(true);

        $this->getMockLogger()->shouldReceive('error')->atLeast(1);

        $this->doProcess();
    }

    public function testBadOptionNames()
    {
        $this->prepareForProcessing(array(
            'priority' => 33,
            'charlist' => '33',
            'optionNames' => array('foo')
        ));
        $this->getMockLangUtils()->shouldReceive('isSimpleArrayOfStrings')->once()->with(array('foo'))->andReturn(false);

        $this->getMockLogger()->shouldReceive('error')->atLeast(1);

        $this->doProcess();
    }

    public function testNonNumericPriority()
    {
        $this->prepareForProcessing(array(
            'priority' => new stdClass(),
            'charlist' => '123',
            'optionNames' => array('foo')
        ));

        $this->getMockLogger()->shouldReceive('error')->atLeast(1);

        $this->doProcess();
    }

    protected function buildSut()
    {
        return new tubepress_core_options_ioc_compiler_EasyTrimmersPass();
    }

    protected function getPrefix()
    {
        return strtolower(tubepress_core_options_api_Constants::IOC_PARAM_EASY_TRIMMER);
    }

    protected function getRequiredAttributes()
    {
        return array('charlist', 'optionNames', 'priority');
    }
}