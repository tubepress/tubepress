<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_internal_boot_helper_uncached_Compiler<extended>
 */
class tubepress_test_internal_boot_helper_uncached_CompilerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_internal_boot_helper_uncached_Compiler
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockIocContainer;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    public function onSetup()
    {
        $this->_mockLogger       = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockIocContainer = $this->mock('tubepress_internal_ioc_ContainerBuilder');

        $this->_mockLogger->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->_mockLogger->shouldReceive('debug')->atLeast(1);

        $this->_sut = new tubepress_internal_boot_helper_uncached_Compiler($this->_mockLogger);
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        require_once TUBEPRESS_ROOT . '/tests/unit/php/classes/internal/fixtures/mock-classes/FakeCompilerPass.php';
        require_once TUBEPRESS_ROOT . '/tests/unit/php/classes/internal/fixtures/mock-classes/FakeExtension.php';
    }

    public function testCompile()
    {
        $this->_mockLogger->shouldReceive('error')->once()->with('(Add-on 1 of 2: mock add-on 1) Failed to load bogus class as an IoC container extension: Class bogus class does not exist');
        $this->_mockLogger->shouldReceive('error')->once()->with('(Add-on 2 of 2: mock add-on 2) Failed to load Hello as an IoC container extension: Class Hello does not exist');
        $this->_mockLogger->shouldReceive('error')->once()->with('(Add-on 2 of 2: mock add-on 2) Failed to load There as an IoC container extension: Class There does not exist');
        $this->_mockLogger->shouldReceive('error')->once()->with('Failed to load no such class as an IoC compiler pass: Class no such class does not exist');

        $mockAddon1 = $this->mock(tubepress_api_contrib_AddonInterface::_);
        $mockAddon2 = $this->mock(tubepress_api_contrib_AddonInterface::_);
        $mockAddon1->shouldReceive('getName')->andReturn('mock add-on 1');
        $mockAddon2->shouldReceive('getName')->andReturn('mock add-on 2');

        $mockAddon1IocContainerExtensions = array('FakeExtension', 'bogus class');
        $mockAddon2IocContainerExtensions = array('Hello', 'There');
        $mockAddon2IocCompilerPasses = array('FakeCompilerPass' => 5, 'no such class' => 4);

        $mockAddon1->shouldReceive('getExtensionClassNames')->once()->andReturn($mockAddon1IocContainerExtensions);
        $mockAddon1->shouldReceive('getMapOfCompilerPassClassNamesToPriorities')->once()->andReturn(array());
        $mockAddon2->shouldReceive('getExtensionClassNames')->once()->andReturn($mockAddon2IocContainerExtensions);
        $mockAddon2->shouldReceive('getMapOfCompilerPassClassNamesToPriorities')->once()->andReturn($mockAddon2IocCompilerPasses);

        $mockAddons = array($mockAddon1, $mockAddon2);

        $this->_mockIocContainer->shouldReceive('compile')->once();
        $this->_mockIocContainer->shouldReceive('addCompilerPass')->once()->with(ehough_mockery_Mockery::any('FakeCompilerPass'));
        $this->_mockIocContainer->shouldReceive('registerExtension')->once()->with(ehough_mockery_Mockery::any('FakeExtension'));

        $this->_sut->compile($this->_mockIocContainer, $mockAddons);

        $this->assertTrue(true);
    }

}