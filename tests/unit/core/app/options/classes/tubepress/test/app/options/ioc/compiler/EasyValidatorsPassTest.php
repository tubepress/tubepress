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
 * @covers tubepress_app_options_ioc_compiler_EasyValidatorsPass<extended>
 */
class tubepress_test_app_options_ioc_compiler_EasyValidatorsPassTest extends tubepress_test_app_options_ioc_compiler_AbstractEasyPassTest
{
    public function testFullProcess()
    {
        $this->prepareForProcessing(array(
            'priority' => 44,
            'map' => array('type1' => array('bar1', 'bar2'), 'type2' => array('hi'))
        ));

        $this->getMockLangUtils()->shouldReceive('isAssociativeArray')->once()->with(
            array('type1' => array('bar1', 'bar2'), 'type2' => array('hi')))->andReturn(true);

        $this->getMockLangUtils()->shouldReceive('isSimpleArrayOfStrings')->once()->with(array('bar1', 'bar2'))->andReturn(true);
        $this->getMockLangUtils()->shouldReceive('isSimpleArrayOfStrings')->once()->with(array('hi'))->andReturn(true);

        $this->_expectRegistration('type1', 44, 'bar1');
        $this->_expectRegistration('type1', 44, 'bar2');
        $this->_expectRegistration('type2', 44, 'hi');

        $this->doProcess();
    }

    private function _expectRegistration($type, $priority, $optionName)
    {
        $mockDefinition = $this->mock('tubepress_platform_api_ioc_DefinitionInterface');
        $mockDefinition->shouldReceive('addArgument')->once()->with($type)->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addArgument')->once()->with(ehough_mockery_Mockery::on(function ($ref) {

            return $ref instanceof tubepress_platform_api_ioc_Reference && "$ref" === strtolower(tubepress_app_options_api_ReferenceInterface::_);
        }))->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addArgument')->once()->with(ehough_mockery_Mockery::on(function ($ref) {

            return $ref instanceof tubepress_platform_api_ioc_Reference && "$ref" === strtolower(tubepress_lib_translation_api_TranslatorInterface::_);
        }))->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addTag')->once()->with(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_options_api_Constants::EVENT_OPTION_SET . '.' . $optionName,
            'priority' => $priority,
            'method'   => 'onOption',
        ))->andReturn($mockDefinition);

        $this->getMockContainer()->shouldReceive('register')->once()->with(
            'easy_validator_for_foo_' . $optionName,
            'tubepress_app_options_impl_easy_EasyValidator'
        )->andReturn($mockDefinition);
    }

    public function testNonStringNames()
    {
        $this->prepareForProcessing(array(
            'priority' => 33,
            'map' => array('type' => array('bar'))
        ));

        $this->getMockLangUtils()->shouldReceive('isAssociativeArray')->once()->with(array('type' => array('bar')))->andReturn(true);
        $this->getMockLangUtils()->shouldReceive('isSimpleArrayOfStrings')->once()->with(array('bar'))->andReturn(false);

        $this->getMockLogger()->shouldReceive('error')->atLeast(1);

        $this->doProcess();
    }

    public function testNonStringType()
    {
        $this->prepareForProcessing(array(
            'priority' => 33,
            'map' => array(3 => array('bar'))
        ));

        $this->getMockLangUtils()->shouldReceive('isAssociativeArray')->once()->with(array(3 => array('bar')))->andReturn(true);

        $this->getMockLogger()->shouldReceive('error')->atLeast(1);

        $this->doProcess();
    }

    public function testNonAssociativeMap()
    {
        $this->prepareForProcessing(array(
            'priority' => 33,
            'map' => array('positiveInteger' => array('bar'))
        ));

        $this->getMockLangUtils()->shouldReceive('isAssociativeArray')->once()->with(array('positiveInteger' => array('bar')))->andReturn(false);

        $this->getMockLogger()->shouldReceive('error')->atLeast(1);

        $this->doProcess();
    }

    public function testNonNumericPriority()
    {
        $this->prepareForProcessing(array(
            'priority' => new stdClass(),
            'map' => array('positiveInteger' => array('bar')
        )));

        $this->getMockLogger()->shouldReceive('error')->atLeast(1);

        $this->doProcess();
    }

    protected function buildSut()
    {
        return new tubepress_app_options_ioc_compiler_EasyValidatorsPass();
    }

    protected function getPrefix()
    {
        return strtolower(tubepress_app_options_api_Constants::IOC_PARAM_EASY_VALIDATION);
    }

    protected function getRequiredAttributes()
    {
        return array('priority', 'map');
    }
}