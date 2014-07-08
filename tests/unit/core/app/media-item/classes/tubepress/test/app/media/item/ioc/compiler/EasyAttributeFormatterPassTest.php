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
 * @covers tubepress_app_media_item_ioc_compiler_EasyAttributeFormattersPass
 */
class tubepress_test_app_media_item_ioc_compiler_EasyAttributeFormatterPassTest extends tubepress_test_app_options_ioc_compiler_AbstractEasyPassTest
{

    public function testFullProcess()
    {
        $mapValue = array(
            array('x', 'y', 'number', 2),
            array('a', 'b', 'truncateString', 44),
            array('z', 'c', 'durationFromSeconds'),
            array('e', 'q', 'dateFromUnixTime')
        );

        $this->prepareForProcessing(array(
            'priority' => 4,
            'map' => $mapValue,
            'providerName' => 'provider name'
        ));
        $this->getMockLangUtils()->shouldReceive('isAssociativeArray')->once()->with($mapValue)->andReturn(false);

        $mockDefinition = $this->mock('tubepress_platform_api_ioc_DefinitionInterface');
        $this->_expectRefArg($mockDefinition, tubepress_lib_util_api_TimeUtilsInterface::_);
        $this->_expectRefArg($mockDefinition, tubepress_app_options_api_ContextInterface::_);
        $mockDefinition->shouldReceive('addTag')->once()->with(tubepress_lib_ioc_api_Constants::TAG_EVENT_LISTENER, array(
            'event'    => tubepress_app_media_provider_api_Constants::EVENT_NEW_MEDIA_ITEM,
            'method'   => 'onNewMediaItem',
            'priority' => 4
        ))->andReturn($mockDefinition);

        $mockDefinition->shouldReceive('addMethodCall')->once()->with('setProviderName', array('provider name'));
        $mockDefinition->shouldReceive('addMethodCall')->once()->with('formatNumber', array('x', 'y', 2));
        $mockDefinition->shouldReceive('addMethodCall')->once()->with('truncateString', array('a', 'b', 44));
        $mockDefinition->shouldReceive('addMethodCall')->once()->with('formatDurationFromSeconds', array('z', 'c'));
        $mockDefinition->shouldReceive('addMethodCall')->once()->with('formatDateFromUnixTime', array('e', 'q'));


        $this->getMockContainer()->shouldReceive('register')->once()->with('easy_attributes_formatter_for_foo',
            'tubepress_app_media_item_impl_easy_EasyAttributeFormatter')->andReturn($mockDefinition);

        $this->doProcess();
    }

    private function _expectRefArg(ehough_mockery_mockery_MockInterface $mock, $r)
    {
        $mock->shouldReceive('addArgument')->once()->with(ehough_mockery_Mockery::on(function ($ref) use ($r) {
            return $ref instanceof tubepress_platform_api_ioc_Reference && "$ref" === strtolower($r);
        }))->andReturn($mock);
    }

    public function testBadProviderName()
    {
        $this->prepareForProcessing(array(
            'priority' => 4,
            'map' => array(array('x', 'y', 'number')),
            'providerName' => new stdClass()
        ));
        $this->getMockLangUtils()->shouldReceive('isAssociativeArray')->once()->with(array(array('x', 'y', 'number')))->andReturn(false);
        $this->getMockLogger()->shouldReceive('error')->once()->with('foo did not pass validation');

        $this->doProcess();
    }

    /**
     * @dataProvider getDataBadMap
     */
    public function testBadMap($mapValue)
    {
        $this->prepareForProcessing(array(
            'priority' => 4,
            'map' => $mapValue
        ));
        $this->getMockLangUtils()->shouldReceive('isAssociativeArray')->once()->with($mapValue)->andReturn(false);
        $this->getMockLogger()->shouldReceive('error')->once()->with('foo did not pass validation');

        $this->doProcess();
    }

    public function getDataBadMap()
    {
        return array(
            array(array(array())),
            array(array(array('x'))),
            array(array(array('x', 'y'))),
            array(array('bla')),
            array(array(array('x', new stdClass(), 'y'))),
            array(array(array(new stdClass(), 'x', 'y'))),
            array(array(array('x', 'y', 'z'))),
        );
    }

    public function testAssocArrayMap()
    {
        $this->prepareForProcessing(array(
            'priority' => 4,
            'map' => array('foo' => 'bar')
        ));
        $this->getMockLangUtils()->shouldReceive('isAssociativeArray')->once()->with(array('foo' => 'bar'))->andReturn(true);
        $this->getMockLogger()->shouldReceive('error')->once()->with('foo did not pass validation');

        $this->doProcess();
    }

    public function testNonArrayMap()
    {
        $this->prepareForProcessing(array(
            'priority' => 4,
            'map' => new stdClass()
        ));
        $this->getMockLogger()->shouldReceive('error')->once()->with('foo did not pass validation');

        $this->doProcess();
    }

    public function testNonNumericPriority()
    {
        $this->prepareForProcessing(array(
            'priority' => 'sdf',
            'map' => array()
        ));
        $this->getMockLogger()->shouldReceive('error')->once()->with('foo did not pass validation');

        $this->doProcess();
    }

    protected function getPrefix()
    {
        return tubepress_app_media_item_api_Constants::IOC_PARAM_EASY_ATTRIBUTE_FORMATTER;
    }

    protected function getRequiredAttributes()
    {
        return array(
            'priority', 'map'
        );
    }

    protected function buildSut()
    {
        return new tubepress_app_media_item_ioc_compiler_EasyAttributeFormattersPass();
    }


}