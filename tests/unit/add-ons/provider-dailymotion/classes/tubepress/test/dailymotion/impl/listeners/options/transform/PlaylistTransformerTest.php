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
 * @covers tubepress_dailymotion_impl_listeners_options_transform_PlaylistTransformer
 */
class tubepress_test_dailymotion_impl_listeners_options_transform_PlaylistTransformerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_dailymotion_impl_listeners_options_transform_PlaylistTransformer
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_dailymotion_impl_listeners_options_transform_PlaylistTransformer(

            new tubepress_url_impl_puzzle_UrlFactory(),
            new tubepress_util_impl_StringUtils()
        );
    }

    /**
     * @dataProvider getDataDailymotionUser
     */
    public function testTransform($incoming, $expected)
    {
        $actual = $this->_sut->transform($incoming);

        $this->assertEquals($expected, $actual);
    }

    public function getDataDailymotionUser()
    {
        return array(

            array('foobar',                             'foobar'),
            array('https://www.dailymotion.com/playlist/foobar', 'foobar'),
            array('http://www.dailymotion.com/playlist/foobar',  'foobar'),
            array('http://dailymotion.com/playlist/foobar',      'foobar'),
            array('https://dailymotion.com/playlist/foobar',     'foobar'),
            array('https://dailymotion.com/playlist/foobar/',    'foobar'),

            //16
            array('', ''),
            array(0, ''),
            array('https://www.dailymotion.com/hi there', ''),
            array('https://www.dailymotion.com/foo/bar/some/thing', ''),
            array('https://www.dailymotion.com', ''),
            array('https://www.dailymotion.com/', ''),
        );
    }
}