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
 * @covers tubepress_core_impl_player_locations_BasePlayerLocation
 */
class tubepress_test_core_impl_players_BasePlayerLocationTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_player_locations_BasePlayerLocation
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
        $this->_mockEnvironmentDetector = $this->mock(tubepress_core_api_environment_EnvironmentInterface::_);
        $this->_mockTemplateFactory     = $this->mock(tubepress_core_api_template_TemplateFactoryInterface::_);
        $this->_sut = new tubepress_core_impl_player_locations_BasePlayerLocation(

             'popup',
            'in a popup window',
            array('players/popup.tpl.php', TUBEPRESS_ROOT . '/src/main/web/themes/default/players/popup.tpl.php'),
            'src/main/web/players/popup/popup.js',
            true
        );
    }

    public function testSelectMatch()
    {
        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');

        $mockEvent->shouldReceive('getArgument')->once()->with('requestedPlayerLocation')->andReturn('popup');
        $mockEvent->shouldReceive('setArgument')->once()->with('selected', $this->_sut);
        $mockEvent->shouldReceive('stopPropagation')->once();

        $this->_sut->onSelectPlayerLocation($mockEvent);

        $this->assertTrue(true);
    }

    public function testSelectNoMatch()
    {
        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');

        $mockEvent->shouldReceive('getArgument')->once()->with('requestedPlayerLocation')->andReturn('xyz');

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
        $result = $this->_sut->getUntranslatedFriendlyName();

        $this->assertEquals('in a popup window', $result);
    }

    public function testProducesHtml()
    {
        $result = $this->_sut->producesHtml();

        $this->assertTrue($result);
    }

    public function testJsUrl()
    {
        $mockBaseUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockNewUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $mockNewUrl->shouldReceive('addPath')->once()->with('src/main/web/players/popup/popup.js');
        $mockBaseUrl->shouldReceive('getClone')->once()->andReturn($mockNewUrl);
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);


        $result = $this->_sut->getPlayerJsUrl($this->_mockEnvironmentDetector);

        $this->assertSame($mockNewUrl, $result);
    }

    public function testGetTemplate()
    {
        $expected = array('players/popup.tpl.php', TUBEPRESS_ROOT . '/src/main/web/themes/default/players/popup.tpl.php');

        $result = $this->_sut->getPathsForTemplateFactory();

        $this->assertSame($expected, $result);
    }
}
