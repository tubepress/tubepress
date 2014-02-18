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
 * @covers tubepress_addons_wordpress_impl_actions_AdminMenu
 */
class tubepress_test_addons_wordpress_impl_actions_AdminMenuTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_wordpress_impl_actions_AdminMenu
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_wordpress_impl_actions_AdminMenu();

        $this->_mockWordPressFunctionWrapper = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);
    }

    public function testExecute()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_options_page')->once()->with(

            'TubePress Options', 'TubePress', 'manage_options',
            'tubepress', array($this->_sut, 'runOptionsPage')
        );

        $this->_sut->execute(array());

        $this->assertTrue(true);
    }

    public function testRunOptionsPage()
    {
        $mock = $this->createMockSingletonService('wordpress.optionsPage');

        $mock->shouldReceive('run')->once();

        $this->_sut->runOptionsPage();

        $this->assertTrue(true);
    }
}
