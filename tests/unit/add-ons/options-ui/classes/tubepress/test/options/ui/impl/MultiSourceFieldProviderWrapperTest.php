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
 * @covers tubepress_options_ui_impl_MultiSourceFieldProviderWrapper<extended>
 */
class tubepress_test_app_impl_options_ui_MultiSourceFieldProviderWrapperTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_options_ui_impl_MultiSourceFieldProviderWrapper
     */
    private $_sut;

    private $_mockDelegate;

    private $_mockFields;

    public function onSetup()
    {
        $this->_mockDelegate = $this->mock('tubepress_spi_options_ui_FieldProviderInterface');
        $this->_mockFields   = array($this->mock('tubepress_api_options_ui_FieldInterface'));

        $this->_mockDelegate->shouldReceive('getId')->once()->andReturn('foobar');

        $this->_sut = new tubepress_options_ui_impl_MultiSourceFieldProviderWrapper(
            $this->_mockDelegate,
            $this->_mockFields
        );
    }

    public function testId()
    {
        $actualId = $this->_sut->getId();

        $this->assertTrue(preg_match_all('/^foobar-wrapped-[0-9]+$/', $actualId, $matches) === 1);
    }
}