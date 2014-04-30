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
 * @covers tubepress_addons_puzzle_impl_url_UrlFactory<extended>
 */
class tubepress_test_addons_puzzle_impl_url_UrlFactoryTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_puzzle_impl_url_UrlFactory
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_puzzle_impl_url_UrlFactory();
    }

    public function testValid()
    {
        $u = $this->_sut->fromString('https://foo.bar/one/two.php?test=true#frag');
        $this->assertInstanceOf('tubepress_api_url_UrlInterface', $u);
        $this->assertEquals('https://foo.bar/one/two.php?test=true#frag', "$u");
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage tubepress_addons_puzzle_impl_url_UrlFactory::fromString() can only accept strings.
     */
    public function testInvalid()
    {
        $this->_sut->fromString(array('bad!'));
    }
}