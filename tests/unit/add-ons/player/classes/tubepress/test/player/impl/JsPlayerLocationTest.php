<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_player_impl_JsPlayerLocation<extended>
 */
class tubepress_test_player_impl_JsPlayerLocationTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_player_impl_JsPlayerLocation
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_player_impl_JsPlayerLocation('name', 'display name', 'static', 'ajax');
    }

    public function testBasics()
    {
        $mockMediaItem = $this->mock('tubepress_api_media_MediaItem');

        $this->assertEquals('name', $this->_sut->getName());
        $this->assertEquals('display name', $this->_sut->getUntranslatedDisplayName());
        $this->assertEquals('static', $this->_sut->getStaticTemplateName());
        $this->assertEquals('ajax', $this->_sut->getAjaxTemplateName());
        $this->assertEquals(array(), $this->_sut->getAttributesForInvocationAnchor($mockMediaItem));
    }
}

