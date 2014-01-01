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
 * @covers tubepress_impl_options_ui_OptionsPageItem
 */
class tubepress_test_impl_options_ui_OptionsPageItemTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_options_ui_OptionsPageItem
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMessageService;

    public function onSetup()
    {
        $this->_mockMessageService = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);

        $this->_sut = new tubepress_impl_options_ui_OptionsPageItem('id', 'display name');
    }

    public function testGetId()
    {
        $this->assertEquals('id', $this->_sut->getId());
    }

    public function testGetDisplayName()
    {
        $this->_mockMessageService->shouldReceive('_')->once()->with('display name')->andReturn('foobar');

        $this->assertEquals('foobar', $this->_sut->getTranslatedDisplayName());
    }
}
