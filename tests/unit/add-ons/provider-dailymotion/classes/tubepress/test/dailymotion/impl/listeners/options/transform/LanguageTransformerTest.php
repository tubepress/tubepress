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
 * @covers tubepress_dailymotion_impl_listeners_options_transform_LanguageTransformer
 */
class tubepress_test_dailymotion_impl_listeners_options_transformer_LanguageTransformerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_dailymotion_impl_listeners_options_transform_LanguageTransformer
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_dailymotion_impl_listeners_options_transform_LanguageTransformer();
    }

    /**
     * @dataProvider getDataLanguage
     */
    public function testTransform($incoming, $expected)
    {
        $actual = $this->_sut->transform($incoming);

        $this->assertEquals($expected, $actual);
    }

    public function getDataLanguage()
    {
        return array(

            array('it', 'it'),
            array('IT', 'it'),
            array('1234', null),
        );
    }
}