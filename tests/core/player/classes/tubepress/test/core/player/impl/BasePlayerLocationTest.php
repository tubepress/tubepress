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
 * @covers tubepress_core_player_impl_BasePlayerLocation
 */
class tubepress_test_core_impl_player_BasePlayerLocationTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_player_impl_BasePlayerLocation
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
        $this->_mockEnvironmentDetector = $this->mock(tubepress_core_environment_api_EnvironmentInterface::_);
        $this->_mockTemplateFactory     = $this->mock(tubepress_core_template_api_TemplateFactoryInterface::_);
        $this->_sut = new tubepress_core_player_impl_BasePlayerLocation(

             'popup',
            'in a popup window',
            array('players/popup.tpl.php', TUBEPRESS_ROOT . '/core/themes/web/default/players/popup.tpl.php'),
            'core/player/web/players/popup/popup.js',
            true,
            true
        );
    }

    public function testSelectMatch()
    {
        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');

        $mockEvent->shouldReceive('getArgument')->once()->with('requestedPlayerLocationName')->andReturn('popup');
        $mockEvent->shouldReceive('setArgument')->once()->with('playerLocation', $this->_sut);
        $mockEvent->shouldReceive('stopPropagation')->once();

        $this->_sut->onSelectPlayerLocation($mockEvent);

        $this->assertTrue(true);
    }

    public function testSelectNoMatch()
    {
        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');

        $mockEvent->shouldReceive('getArgument')->once()->with('requestedPlayerLocationName')->andReturn('xyz');

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
        $mockBaseUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockNewUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $mockNewUrl->shouldReceive('addPath')->once()->with('core/player/web/players/popup/popup.js');
        $mockBaseUrl->shouldReceive('getClone')->once()->andReturn($mockNewUrl);
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn($mockBaseUrl);


        $result = $this->_sut->getPlayerJsUrl($this->_mockEnvironmentDetector);

        $this->assertSame($mockNewUrl, $result);
    }

    public function testGetTemplate()
    {
        $expected = array('players/popup.tpl.php', TUBEPRESS_ROOT . '/core/themes/web/default/players/popup.tpl.php');

        $result = $this->_sut->getPathsForTemplateFactory();

        $this->assertSame($expected, $result);
    }
}
