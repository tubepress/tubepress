<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_dailymotion_impl_listeners_options_transform_CsvTransformer
 */
class tubepress_test_dailymotion_impl_listeners_options_transform_AbstractCsvTransformerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_dailymotion_impl_listeners_options_transform_CsvTransformer
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_dailymotion_impl_listeners_options_transform_CsvTransformer(

            $this
        );
    }

    public function transform($incoming)
    {
        if ("$incoming" === 'x') {

            return 'X';
        }

        return null;
    }

    /**
     * @dataProvider getDataCsv
     */
    public function testTransform($incoming, $expected)
    {
        $actual = $this->_sut->transform($incoming);

        $this->assertEquals($expected, $actual);
    }

    public function getDataCsv()
    {
        return array(

            array('', ''),
            array('a,b,c', ''),
            array('a,x,c', 'X'),
            array('x  ,  x  , c', 'X,X'),
        );
    }
}
