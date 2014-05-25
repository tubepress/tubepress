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
 * @covers tubepress_wordpress_impl_actions_AdminMenu
 */
class tubepress_test_wordpress_impl_actions_AdminMenuTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_actions_AdminMenu
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionsPage;

    public function onSetup()
    {

        $this->_mockWordPressFunctionWrapper = $this->mock(tubepress_wordpress_spi_WpFunctionsInterface::_);
        $this->_mockOptionsPage              = $this->mock('tubepress_wordpress_impl_OptionsPage');

        $this->_sut = new tubepress_wordpress_impl_actions_AdminMenu(

            $this->_mockWordPressFunctionWrapper,
            $this->_mockOptionsPage
        );
    }

    public function testExecute()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_options_page')->once()->with(

            'TubePress Options', 'TubePress', 'manage_options',
            'tubepress', array($this->_sut, 'runOptionsPage')
        );

        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');

        $this->_sut->action($mockEvent);

        $this->assertTrue(true);
    }

    public function testRunOptionsPage()
    {
        $this->_mockOptionsPage->shouldReceive('run')->once();

        $this->_sut->runOptionsPage();

        $this->assertTrue(true);
    }
}
