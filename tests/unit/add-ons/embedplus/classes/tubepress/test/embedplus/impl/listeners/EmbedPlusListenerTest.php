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
 * @covers tubepress_embedplus_impl_listeners_EmbedPlusListener
 */
class tubepress_test_embedplus_impl_listeners_EmbedPlusListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_embedplus_impl_listeners_EmbedPlusListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    public function onSetup() {

        $this->_mockContext = $this->mock(tubepress_app_api_options_ContextInterface::_);

        $this->_sut = new tubepress_embedplus_impl_listeners_EmbedPlusListener($this->_mockContext);
    }

    public function testBuilt()
    {
        $this->assertInstanceOf('tubepress_embedplus_impl_listeners_EmbedPlusListener', $this->_sut);
    }
}

