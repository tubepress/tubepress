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
 * @covers tubepress_vimeo_impl_player_VimeoPlayerLocation
 */
class tubepress_test_vimeo_impl_player_VimeoPlayerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_vimeo_impl_player_VimeoPlayerLocation
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_vimeo_impl_player_VimeoPlayerLocation();
    }

    public function testBasics()
    {
        $this->assertEmpty($this->_sut->getTemplatePathsForStaticContent());
        $this->assertEquals('vimeo', $this->_sut->getName());
        $this->assertEquals('from the video\'s original Vimeo page', $this->_sut->getUntranslatedDisplayName());
    }
}
