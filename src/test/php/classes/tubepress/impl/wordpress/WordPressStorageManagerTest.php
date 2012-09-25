<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_impl_options_WordPressStorageManagerTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockEnvironmentDetector;

    private $_mockEventDispatcher;

    private $_mockOptionValidator;

    private $_mockOptionsReference;

    private $_mockWordPressFunctionWrapper;

    public function setUp()
    {
        $this->_mockEnvironmentDetector      = \Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockEventDispatcher          = \Mockery::mock('ehough_tickertape_api_IEventDispatcher');
        $this->_mockOptionValidator          = \Mockery::mock(tubepress_spi_options_OptionValidator::_);
        $this->_mockOptionsReference         = \Mockery::mock(tubepress_api_service_options_OptionDescriptorReference::_);
        $this->_mockWordPressFunctionWrapper = \Mockery::mock(tubepress_plugins_wordpresscore_lib_spi_WordPressFunctionWrapper::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setEnvironmentDetector($this->_mockEnvironmentDetector);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionValidator($this->_mockOptionValidator);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionDescriptorReference($this->_mockOptionsReference);
        tubepress_plugins_wordpresscore_lib_impl_patterns_ioc_WordPressServiceLocator::setWordPressFunctionWrapper($this->_mockWordPressFunctionWrapper);

        $this->_sut = new tubepress_plugins_wordpresscore_lib_impl_options_WordPressStorageManager();
    }



    function testExists()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('get_option')->once()->with('tubepress-something')->andReturn(false);

        $this->assertFalse($this->_sut->exists('something'));
    }


    function testGetNotExists()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('get_option')->once()->with('tubepress-something')->andReturn(false);

        $result = $this->_sut->get('something');

        $this->assertTrue($result === false);
    }

    function testSetNotExists()
    {
        $this->_mockOptionsReference->shouldReceive('findOneByName')->with('something')->andReturn(null);

        $result = $this->_sut->set('something', 'value');

        $this->assertTrue($result);
    }

    function testSetDoNotPersist()
    {
        $od = new tubepress_api_model_options_OptionDescriptor('something');
        $od->setDoNotPersist();

        $this->_mockOptionsReference->shouldReceive('findOneByName')->with('something')->andReturn($od);

        $result = $this->_sut->set('something', 'value');

        $this->assertTrue($result);
    }

    function testSetFailsValidation()
    {
        $od = new tubepress_api_model_options_OptionDescriptor('something');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::PRE_VALIDATION_OPTION_SET, \Mockery::type('tubepress_api_event_TubePressEvent'));
        $this->_mockOptionsReference->shouldReceive('findOneByName')->with('something')->andReturn($od);
        $this->_mockOptionValidator->shouldReceive('isValid')->once()->with('something', 'value')->andReturn(false);
        $this->_mockOptionValidator->shouldReceive('getProblemMessage')->once()->with('something', 'value')->andReturn('xyz');

        $result = $this->_sut->set('something', 'value');

        $this->assertEquals('xyz', $result);
    }

    function testSetPassesValidation()
    {
        $od = new tubepress_api_model_options_OptionDescriptor('something');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::PRE_VALIDATION_OPTION_SET, \Mockery::type('tubepress_api_event_TubePressEvent'));
        $this->_mockOptionsReference->shouldReceive('findOneByName')->with('something')->andReturn($od);
        $this->_mockOptionValidator->shouldReceive('isValid')->once()->with('something', 'value')->andReturn(true);
        $this->_mockWordPressFunctionWrapper->shouldReceive('update_option')->once()->with('tubepress-something', 'value');

        $result = $this->_sut->set('something', 'value');

        $this->assertTrue($result);
    }


    function testInitMissingStored()
    {

        $this->_mockWordPressFunctionWrapper->shouldReceive('get_option')->once()->with('tubepress-version')->andReturn(false);

        $this->_setupInit();

        $this->_sut->init();

        $this->assertTrue(true);
    }



    private function _setupInit()
    {
        $this->_setupBaseInit();

        $this->_mockWordPressFunctionWrapper->shouldReceive('get_option')->twice()->with('tubepress-name2')->andReturn('abc');

        $this->_mockOptionValidator->shouldReceive('isValid')->once()->with('name2', 'abc')->andReturn(true);
    }

    private function _setupBaseInit()
    {
        $version = tubepress_spi_version_Version::parse('1.5.0');
        $this->_mockEnvironmentDetector->shouldReceive('getVersion')->once()->andReturn($version);

        $od1 = new tubepress_api_model_options_OptionDescriptor('name1');
        $od2 = new tubepress_api_model_options_OptionDescriptor('name2');

        $this->_mockWordPressFunctionWrapper->shouldReceive('add_option')->once()->with('tubepress-version', '1.5.0');
        $this->_mockWordPressFunctionWrapper->shouldReceive('update_option')->once()->with('tubepress-version', '1.5.0');

        $this->_mockOptionsReference->shouldReceive('findAll')->once()->andReturn(array($od1, $od2));

        $od1->setDoNotPersist();

        $od2->setDefaultValue('value2');
    }
}

