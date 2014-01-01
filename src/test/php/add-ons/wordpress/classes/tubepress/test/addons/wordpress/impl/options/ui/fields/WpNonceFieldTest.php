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
 * @covers tubepress_addons_wordpress_impl_options_ui_fields_WpNonceField
 */
class tubepress_test_addons_wordpress_impl_options_ui_fields_WpNonceFieldTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_wordpress_impl_options_ui_fields_WpNonceField
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWpFunctionWrapper;

    public function onSetup()
    {
        $this->_mockWpFunctionWrapper = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);

        $this->_sut = new tubepress_addons_wordpress_impl_options_ui_fields_WpNonceField();
    }

    public function testIsPro()
    {
        $this->assertFalse($this->_sut->isProOnly());
    }

    public function testGetName()
    {
        $this->assertEquals('', $this->_sut->getTranslatedDisplayName());
    }

    public function testGetDescription()
    {
        $this->assertEquals('', $this->_sut->getTranslatedDescription());
    }

    public function testSubmit()
    {
        $this->_mockWpFunctionWrapper->shouldReceive('check_admin_referer')->once()->with('tubepress-save', 'tubepress-nonce');

        $this->assertNull($this->_sut->onSubmit());
    }

    public function testGetWidgetHtml()
    {
        $this->_mockWpFunctionWrapper->shouldReceive('wp_nonce_field')->once()->with('tubepress-save', 'tubepress-nonce', true, false)->andReturn('foo');

        $result = $this->_sut->getWidgetHTML();

        $this->assertEquals('foo', $result);
    }
}