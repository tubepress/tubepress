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
 * @covers tubepress_addons_wordpress_impl_actions_WpHead
 */
class tubepress_test_addons_wordpress_impl_actions_WpHeadTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_wordpress_impl_actions_WpHead
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHtmlGenerator;

    public function onSetup()
    {
        $this->_mockHtmlGenerator = $this->createMockSingletonService(tubepress_api_html_HtmlGeneratorInterface::_);
        $this->_sut = new tubepress_addons_wordpress_impl_actions_WpHead($this->_mockHtmlGenerator);

        $this->_mockWordPressFunctionWrapper = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);
    }

    public function testExecuteInsideAdmin()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(true);

        $mockEvent = ehough_mockery_Mockery::mock('tubepress_api_event_EventInterface');
        $this->_sut->action($mockEvent);
        $this->assertTrue(true);
    }

    public function testExecuteOutsideAdmin()
    {

        $this->_mockHtmlGenerator->shouldReceive('getCssHtml')->once()->andReturn('hello there');
        $this->_mockHtmlGenerator->shouldReceive('getJsHtml')->once()->andReturn('goodbye now');

        $this->_mockWordPressFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(false);

        $this->expectOutputString('hello theregoodbye now');

        $mockEvent = ehough_mockery_Mockery::mock('tubepress_api_event_EventInterface');
        $this->_sut->action($mockEvent);

        $this->assertTrue(true);
    }
}
