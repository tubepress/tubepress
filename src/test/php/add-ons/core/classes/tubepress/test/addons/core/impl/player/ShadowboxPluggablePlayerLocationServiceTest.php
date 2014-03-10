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
 * @covers tubepress_addons_core_impl_player_ShadowboxPluggablePlayerLocationService
 */
class tubepress_test_addons_core_impl_players_ShadowboxPluggablePlayerLocationServiceTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_player_ShadowboxPluggablePlayerLocationService
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    public function onSetup()
    {
        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);

        $this->_sut = new tubepress_addons_core_impl_player_ShadowboxPluggablePlayerLocationService();
    }

    public function testName()
    {
        $result = $this->_sut->getName();

        $this->assertEquals('shadowbox', $result);
    }

    public function testFriendlyName()
    {
        $result = $this->_sut->getFriendlyName();

        $this->assertEquals('with Shadowbox', $result);
    }

    public function testProducesHtml()
    {
        $result = $this->_sut->producesHtml();

        $this->assertTrue($result);
    }

    public function testJsUrl()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getBaseUrl')->once()->andReturn('xyz');

        $result = $this->_sut->getPlayerJsUrl();

        $this->assertEquals('xyz/src/main/web/players/shadowbox/shadowbox.js', $result);
    }

    public function testGetTemplate()
    {
        $template = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');

        $themeHandler = ehough_mockery_Mockery::mock(tubepress_spi_theme_ThemeHandlerInterface::_);

        $themeHandler->shouldReceive('getTemplateInstance')->once()->with('players/shadowbox.tpl.php', TUBEPRESS_ROOT . '/src/main/resources/default-themes/default')->andReturn($template);

        $result = $this->_sut->getTemplate($themeHandler);

        $this->assertSame($template, $result);
    }
}
