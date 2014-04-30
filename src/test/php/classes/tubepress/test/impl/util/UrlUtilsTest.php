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
 * @covers tubepress_impl_util_UrlUtils
 */
class tubepress_test_impl_util_UrlUtilsTest extends tubepress_test_TubePressUnitTest
{
    public function testStripAuthorityAndScheme()
    {
        $url    = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $url->shouldReceive('getScheme')->once()->andReturn('something');
        $url->shouldReceive('getAuthority')->once()->andReturn('bla.bla');
        $url->shouldReceive('toString')->once()->andReturn('something://bla.bla/one.php?hello=phrase');
        $result = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);

        $this->assertTrue(is_string($result));
        $this->assertEquals('/one.php?hello=phrase', $result);
    }

    public function testStripAuthorityAndSchem2()
    {
        $url    = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $url->shouldReceive('getScheme')->once()->andReturn('something');
        $url->shouldReceive('getAuthority')->once()->andReturn('bla.bla');
        $url->shouldReceive('toString')->once()->andReturn('something://bla.bla');
        $result = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);

        $this->assertTrue(is_string($result));
        $this->assertEquals('/', $result);
    }

    public function testStripAuthorityAndScheme3()
    {
        $url    = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $url->shouldReceive('getScheme')->once()->andReturn('something');
        $url->shouldReceive('getAuthority')->once()->andReturn('bla.bla:1234');
        $url->shouldReceive('toString')->once()->andReturn('something://bla.bla:1234/one.php?hello=phrase');
        $result = tubepress_impl_util_UrlUtils::getAsStringWithoutSchemeAndAuthority($url);

        $this->assertTrue(is_string($result));
        $this->assertEquals('/one.php?hello=phrase', $result);
    }
}

