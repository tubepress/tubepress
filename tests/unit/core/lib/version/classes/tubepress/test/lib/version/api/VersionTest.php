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
 * @covers tubepress_lib_version_api_Version
 */
class tubepress_test_lib_version_api_VersionTest extends tubepress_test_TubePressUnitTest
{

    public function testToStringNoQualifier()
    {
        $version = new tubepress_lib_version_api_Version(1, 2, 3);

        $this->assertTrue("$version" === '1.2.3');
    }

    public function testParseFull()
    {
        $result = tubepress_lib_version_api_Version::parse('7.3.4.fuzzy');

        $this->assertTrue($result->compareTo(new tubepress_lib_version_api_Version(7, 3, 4, 'fuzzy')) === 0);
    }

    public function testParseTriple()
    {
        $result = tubepress_lib_version_api_Version::parse('7.3.4');

        $this->assertTrue($result->compareTo(new tubepress_lib_version_api_Version(7, 3, 4)) === 0);
    }

    public function testParseDouble()
    {
        $result = tubepress_lib_version_api_Version::parse('7.3');

        $this->assertTrue($result->compareTo(new tubepress_lib_version_api_Version(7, 3)) === 0);
    }

    public function testParseSingle()
    {
        $result = tubepress_lib_version_api_Version::parse('7');

        $this->assertTrue($result->compareTo(new tubepress_lib_version_api_Version(7)) === 0);
    }

    public function testParseEmpty()
    {
        $result = tubepress_lib_version_api_Version::parse('');

        $this->assertTrue($result->compareTo(new tubepress_lib_version_api_Version(0)) === 0);
    }

    public function testCompareQualifiers()
    {
        $firstOne  = new tubepress_lib_version_api_Version(1, 2, 80, 'bar');
        $secondOne = new tubepress_lib_version_api_Version(1, 2, 80, 'foo');

        $this->assertTrue($firstOne->compareTo($secondOne) === strcmp('bar', 'foo'));
        $this->assertTrue($secondOne->compareTo($firstOne) === strcmp('foo', 'bar'));
    }

    public function testCompareMicros()
    {
        $firstOne  = new tubepress_lib_version_api_Version(1, 2, 80);
        $secondOne = new tubepress_lib_version_api_Version(1, 2, 15);

        $this->assertTrue($firstOne->compareTo($secondOne) === 65);
        $this->assertTrue($secondOne->compareTo($firstOne) === -65);
    }

    public function testCompareMinors()
    {
        $firstOne  = new tubepress_lib_version_api_Version(1, 30);
        $secondOne = new tubepress_lib_version_api_Version(1, 25);

        $this->assertTrue($firstOne->compareTo($secondOne) === 5);
        $this->assertTrue($secondOne->compareTo($firstOne) === -5);
    }

    public function testCompareMajors()
    {
        $firstOne  = new tubepress_lib_version_api_Version(100);
        $secondOne = new tubepress_lib_version_api_Version(50);

        $this->assertTrue($firstOne->compareTo($secondOne) === 50);
        $this->assertTrue($secondOne->compareTo($firstOne) === -50);
    }

    public function testCompareTwoEqual()
    {
        $firstOne  = new tubepress_lib_version_api_Version(1);
        $secondOne = new tubepress_lib_version_api_Version(1);

        $this->assertTrue($firstOne->compareTo($secondOne) === 0);
        $this->assertTrue($secondOne->compareTo($firstOne) === 0);
    }

    public function testCompareString()
    {
        $firstOne  = new tubepress_lib_version_api_Version(1);

        $this->assertTrue($firstOne->compareTo('1') === 0);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testParseNonString()
    {
        tubepress_lib_version_api_Version::parse(array());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructNegativeMicro()
    {
        new tubepress_lib_version_api_Version(1, 1, -1);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructNegativeMinor()
    {
        new tubepress_lib_version_api_Version(1, -1);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructNegativeMajor()
    {
        new tubepress_lib_version_api_Version(-1);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructInvalidQualifier()
    {
        new tubepress_lib_version_api_Version(1, 2, 3, '**&&');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testParseInvalid()
    {
        tubepress_lib_version_api_Version::parse('1.2.3.4.5');
    }

    public function testToStringWithQualifier()
    {
        $version = new tubepress_lib_version_api_Version(1, 2, 3, 'boogy');

        $this->assertTrue("$version" === '1.2.3.boogy');
    }

    public function testGettersWithQualifier()
    {
        $version = new tubepress_lib_version_api_Version('3', '2', '1', 'foobar');

        $this->assertTrue(3 === $version->getMajor());
        $this->assertTrue(2 === $version->getMinor());
        $this->assertTrue(1 === $version->getMicro());
        $this->assertTrue('foobar' === $version->getQualifier());
    }

    public function testGettersNoQualifier()
    {
        $version = new tubepress_lib_version_api_Version('3', '2', '1');

        $this->assertTrue(3 === $version->getMajor());
        $this->assertTrue(2 === $version->getMinor());
        $this->assertTrue(1 === $version->getMicro());
        $this->assertTrue(null === $version->getQualifier());
    }
}
