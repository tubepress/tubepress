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
 * @covers tubepress_platform_impl_boot_helper_uncached_Serializer<extended>
 */
class tubepress_test_platform_impl_boot_helper_uncached_SerializerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_platform_impl_boot_helper_uncached_Serializer
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockBootSettings;

    public function onSetup()
    {
        $this->_mockBootSettings = $this->mock(tubepress_platform_api_boot_BootSettingsInterface::_);
        $this->_sut              = new tubepress_platform_impl_boot_helper_uncached_Serializer($this->_mockBootSettings);
    }

    public function testUnserializeCannotUnserialize()
    {
        $this->setExpectedException('InvalidArgumentException', 'Failed to unserialize incoming data');

        $this->_mockBootSettings->shouldReceive('getSerializationEncoding')->once()->andReturn('none');

        $this->_sut->unserialize('asdf');
    }

    /**
     * @dataProvider getSerializeData
     */
    public function testSerialize($encoding, $nonSerialized, $serialized)
    {
        $this->_mockBootSettings->shouldReceive('getSerializationEncoding')->twice()->andReturn($encoding);

        $actualSerialized = $this->_sut->serialize($nonSerialized);

        $this->assertEquals($serialized, $actualSerialized);

        $actualDeserialized = $this->_sut->unserialize($serialized);

        $this->assertEquals($nonSerialized, $actualDeserialized);
    }

    public function getSerializeData()
    {
        return array(

            array('none', array('foo' => 'bar'), 'a:1:{s:3:"foo";s:3:"bar";}'),
            array('base64', array('foofoofoofoofoofoofoofoofoofoo' => 'bar'), 'YToxOntzOjMwOiJmb29mb29mb29mb29mb29mb29mb29mb29mb29mb28iO3M6MzoiYmFyIjt9'),
            array('urlencode', array('foo' => 'bar'), 'a%3A1%3A%7Bs%3A3%3A%22foo%22%3Bs%3A3%3A%22bar%22%3B%7D'),
            array('gzip-then-base64', array('foofoofoofoofoofoofoofoofoofoo' => 'bar'), 'eJxLtDK0qi62MjawUkrLz8eDlKyBqqyUkhKLlKxrAREmEz4=')
        );
    }
}