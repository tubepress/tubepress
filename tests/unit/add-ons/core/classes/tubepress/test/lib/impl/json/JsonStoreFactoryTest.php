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
 * @covers tubepress_lib_impl_json_JsonStoreFactory<extended>
 */
class tubepress_test_lib_impl_json_JsonStoreFactoryTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_lib_impl_json_JsonStoreFactory
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut  = new tubepress_lib_impl_json_JsonStoreFactory();
    }

    /**
     * @dataProvider dataProviderBadJson
     */
    public function testBadJson($incoming)
    {
        $this->setExpectedException('InvalidArgumentException', 'Unable to decode JSON');
        $this->_sut->newInstance($incoming);
    }

    public function dataProviderBadJson()
    {
        return array(
            array('........'),
            array(''),
        );
    }
}