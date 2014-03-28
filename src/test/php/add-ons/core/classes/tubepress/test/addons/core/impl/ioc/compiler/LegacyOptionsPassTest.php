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
 * @covers tubepress_addons_core_impl_ioc_compiler_LegacyOptionsPass<extended>
 */
class tubepress_test_addons_core_impl_ioc_compiler_LegacyOptionsPassTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_ioc_compiler_LegacyOptionsPass
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContainer;

    public function onSetup()
    {
        $this->_sut           = new tubepress_addons_core_impl_ioc_compiler_LegacyOptionsPass();
        $this->_mockContainer = ehough_mockery_Mockery::mock('tubepress_api_ioc_ContainerBuilderInterface');
    }

    public function testPass()
    {
        $mockService = ehough_mockery_Mockery::mock();
        $mockOd = new tubepress_spi_options_OptionDescriptor('optionName');
        $mockOd->setLabel('label');
        $mockOd->setDescription('desc');
        $mockOd->setDefaultValue('default value');
        $mockOd->setDoNotPersist();
        $mockOd->setAcceptableValues(array(1, 2, 3));
        $mockOd->setCannotBeSetViaShortcode();
        $mockOds = array($mockOd);

        $this->_mockContainer->shouldReceive('findTaggedServiceIds')->once()->with(tubepress_spi_options_PluggableOptionDescriptorProvider::_)
            ->andReturn(array('serviceid' => array('tag1', 'tag2')));
        $this->_mockContainer->shouldReceive('get')->once()->with('serviceid')->andReturn($mockService);
        $this->_mockContainer->shouldReceive('setDefinition')->once()->with('serviceid__converted', ehough_mockery_Mockery::on(array($this, '__validateDefinition')));

        $mockService->shouldReceive('getOptionDescriptors')->once()->andReturn($mockOds);

        $this->_sut->process($this->_mockContainer);

        $this->assertTrue(true);
    }

    public function __validateDefinition($definition)
    {
        $ok = $definition instanceof tubepress_api_ioc_DefinitionInterface
            && $definition->getClass() === 'tubepress_impl_bc_LegacyOptionProvider'
            && $definition->getArguments() === array(
                array('optionName' => 'label'),
                array('optionName' => 'desc'),
                array('optionName' => 'default value')
            );

        $ok = $ok && $definition->getTags() === array(tubepress_spi_options_OptionProvider::_ => array(array()));

        $ok = $ok && $definition->getMethodCalls() === array(

            array('setOptionAsNonShortcodeSettable', array('optionName')),
            array('setOptionAsDoNotPersist',         array('optionName')),
            array('setAcceptableValues',             array('optionName', array(1,2,3))),
        );

        return $ok;
    }
}