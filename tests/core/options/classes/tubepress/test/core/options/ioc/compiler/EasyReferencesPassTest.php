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
 * @covers tubepress_core_options_ioc_compiler_EasyReferencesPass<extended>
 */
class tubepress_test_core_options_ioc_compiler_EasyReferencesPassTest extends tubepress_test_core_options_ioc_compiler_AbstractEasyPassTest
{
    public function testFullProcess()
    {
        $this->prepareForProcessing(array(
            'defaultValues' => array('foo' => 'bar'),
            'proOptionNames' => array('foo'),
            'labels' => array('foo' => 'foo label')
        ));

        $this->getMockLangUtils()->shouldReceive('isSimpleArrayOfStrings')->once()->with(array('foo'))->andReturn(true);
        $this->getMockLangUtils()->shouldReceive('isAssociativeArray')->once()->with(array('foo' => 'foo label'))->andReturn(true);

        $mockDefinition = $this->mock('tubepress_api_ioc_DefinitionInterface');
        $mockDefinition->shouldReceive('addArgument')->once()->with(array('foo' => 'bar'))->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addArgument')->once()->with(ehough_mockery_Mockery::on(function ($ref) {

            return $ref instanceof tubepress_api_ioc_Reference && "$ref" === strtolower(tubepress_api_util_LangUtilsInterface::_);
        }))->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addTag')->once()->with(tubepress_core_options_api_ReferenceInterface::_)->andReturn($mockDefinition);
        $mockDefinition->shouldReceive('addMethodCall')->once()->with('setMapOfOptionNamesToUntranslatedLabels', array(array('foo' => 'foo label')));
        $mockDefinition->shouldReceive('addMethodCall')->once()->with('setProOptionNames', array(array('foo')));


        $this->getMockContainer()->shouldReceive('register')->once()->with('easy_reference_for_foo',
            'tubepress_core_options_impl_easy_EasyReference')->andReturn($mockDefinition);

        $this->doProcess();
    }

    public function testBadProNames()
    {
        $this->prepareForProcessing(array(
            'defaultValues' => array('foo' => 'bar'),
            'proOptionNames' => 'bla'
        ));

        $this->getMockLangUtils()->shouldReceive('isSimpleArrayOfStrings')->once()->with('bla')->andReturn(false);
        $this->getMockLogger()->shouldReceive('error')->once();

        $this->doProcess();
    }

    public function testBadLabels2()
    {
        $this->prepareForProcessing(array(
            'defaultValues' => array('foo' => 'bar'),
            'labels' => array(3, 4)
        ));

        $this->getMockLangUtils()->shouldReceive('isAssociativeArray')->once()->with(array(3, 4))->andReturn(true);
        $this->getMockLogger()->shouldReceive('error')->once();

        $this->doProcess();
    }

    public function testBadLabels1()
    {
        $this->prepareForProcessing(array(
            'defaultValues' => array('foo' => 'bar'),
            'labels' => 'bla'
        ));

        $this->getMockLangUtils()->shouldReceive('isAssociativeArray')->once()->with('bla')->andReturn(false);
        $this->getMockLogger()->shouldReceive('error')->once();

        $this->doProcess();
    }

    protected function buildSut()
    {
        return new tubepress_core_options_ioc_compiler_EasyReferencesPass();
    }

    protected function getPrefix()
    {
        return strtolower(tubepress_core_options_api_Constants::IOC_PARAM_EASY_REFERENCE);
    }

    protected function getRequiredAttributes()
    {
        return array('defaultValues');
    }
}