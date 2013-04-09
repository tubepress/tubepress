<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_impl_player_AddonBaseTest extends TubePressUnitTest
{
    public function testBuildsCorrectly1()
    {
        $sut = new tubepress_impl_addon_AddonBase(

            'name',
            '1.0.0',
            'description',
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.org')),
            'tubepress_impl_player_AddonBaseTest'
        );
        
        $this->assertEquals('name', $sut->getName());
        $this->assertEquals('1.0.0', (string) $sut->getVersion());
        $this->assertEquals('description', $sut->getTitle());
        $this->assertEquals(array('name' => 'eric'), $sut->getAuthor());
        $this->assertEquals(array(array('url' => 'http://tubepress.org')), $sut->getLicenses());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBuildsCorrectly2()
    {
        $sut = new tubepress_impl_addon_AddonBase(

            'name',
            'x.y.z',
            'description',
            array('name' => 'eric'),
            array(array('url' => 'http://tubepress.org')),
            'tubepress_impl_player_AddonBaseTest'
        );

        $this->assertEquals('name', $sut->getName());
        $this->assertEquals('1.0.0', (string) $sut->getVersion());
        $this->assertEquals('description', $sut->getTitle());
        $this->assertEquals(array('name' => 'eric'), $sut->getAuthor());
        $this->assertEquals(array(array('url' => 'http://tubepress.org')), $sut->getLicenses());
    }

    public function boot()
    {

    }

}