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
 * @covers tubepress_app_player_impl_BasePlayerLocation
 */
class tubepress_test_app_impl_player_BasePlayerLocationTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_player_impl_BasePlayerLocation
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplateFactory;

    public function onSetup()
    {
        $this->_mockEnvironmentDetector = $this->mock(tubepress_app_environment_api_EnvironmentInterface::_);
        $this->_mockTemplateFactory     = $this->mock(tubepress_lib_template_api_TemplateFactoryInterface::_);
        $this->_sut = new tubepress_app_player_impl_BasePlayerLocation(

             'popup',
            'in a popup window',
            array('static1', 'static2'),
            array('ajax1', 'ajax2'),
            'core/player/web/players/popup/popup.js',
            true,
            true
        );
    }

    public function testSelectMatch()
    {
        $mockEvent = $this->mock('tubepress_lib_event_api_EventInterface');

        $mockEvent->shouldReceive('getArgument')->once()->with('playerLocation')->andReturn($this->_sut);
        $mockEvent->shouldReceive('stopPropagation')->once();

        $this->_sut->onSelectPlayerLocation($mockEvent);

        $this->assertTrue(true);
    }

    public function testSelectNoMatch()
    {
        $mockEvent = $this->mock('tubepress_lib_event_api_EventInterface');

        $mockEvent->shouldReceive('getArgument')->once()->with('playerLocation')->andReturn('xyz');

        $this->_sut->onSelectPlayerLocation($mockEvent);

        $this->assertTrue(true);
    }

    public function testName()
    {
        $result = $this->_sut->getName();

        $this->assertEquals('popup', $result);
    }

    public function testFriendlyName()
    {
        $result = $this->_sut->getUntranslatedDisplayName();

        $this->assertEquals('in a popup window', $result);
    }

    public function testGetStaticTemplate()
    {
        $expected = array('static1', 'static2');

        $result = $this->_sut->getTemplatePathsForStaticContent();

        $this->assertSame($expected, $result);
    }

    public function testGetAjaxTemplates()
    {
        $expected = array('ajax1', 'ajax2');

        $result = $this->_sut->getTemplatePathsForAjaxContent();

        $this->assertSame($expected, $result);
    }
}
