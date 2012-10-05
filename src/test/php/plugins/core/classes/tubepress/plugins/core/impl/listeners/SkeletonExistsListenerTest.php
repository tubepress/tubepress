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
class tubepress_plugins_core_impl_listeners_SkeletonExistsListenerTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockEnvironmentDetector;

    private $_mockFileSystem;

    function setup()
    {
        $this->_sut = new tubepress_plugins_core_impl_listeners_SkeletonExistsListener();
        
        if (!defined('ABSPATH')) {

        	define('ABSPATH', '/value-of-abspath/');
        }

        $this->_mockEnvironmentDetector = Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockFileSystem = Mockery::mock('ehough_fimble_api_Filesystem');

        tubepress_impl_patterns_ioc_KernelServiceLocator::setEnvironmentDetector($this->_mockEnvironmentDetector);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setFileSystem($this->_mockFileSystem);
    }

    function testWordPress()
    {
        $this->_mockEnvironmentDetector->shouldReceive('isWordPress')->once()->andReturn(true);
        $this->_mockEnvironmentDetector->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath>>');

		$this->_mockFileSystem->shouldReceive('mirrorDirectoryPreventFileOverwrite')->once()->with('<<basepath>>/src/main/resources/user-content-skeleton/tubepress-content', '/value-of-abspath/wp-content');
        
        $this->_sut->onBoot(new ehough_tickertape_impl_GenericEvent());

        $this->assertTrue(true);
    }
    
    function testNonWordPress()
    {
        $this->_mockEnvironmentDetector->shouldReceive('isWordPress')->once()->andReturn(false);
        $this->_mockEnvironmentDetector->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath>>');

        $this->_mockFileSystem->shouldReceive('mirrorDirectoryPreventFileOverwrite')->once()->with('<<basepath>>/src/main/resources/user-content-skeleton/tubepress-content', '<<basepath>>');
    
    	$this->_sut->onBoot(new ehough_tickertape_impl_GenericEvent());

        $this->assertTrue(true);
    }
}
