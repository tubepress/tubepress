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

class tubepress_spi_version_VersionTest extends TubePressUnitTest
{

	function testToStringNoQualifier()
	{
	    $version = new tubepress_spi_version_Version(1, 2, 3);

		$this->assertTrue("$version" === '1.2.3');
	}

	function testParseFull()
	{
	    $result = tubepress_spi_version_Version::parse('7.3.4.fuzzy');

	    $this->assertTrue($result->compareTo(new tubepress_spi_version_Version(7, 3, 4, 'fuzzy')) === 0);
	}

	function testParseTriple()
	{
	    $result = tubepress_spi_version_Version::parse('7.3.4');

	    $this->assertTrue($result->compareTo(new tubepress_spi_version_Version(7, 3, 4)) === 0);
	}

	function testParseDouble()
	{
	    $result = tubepress_spi_version_Version::parse('7.3');

	    $this->assertTrue($result->compareTo(new tubepress_spi_version_Version(7, 3)) === 0);
	}

	function testParseSingle()
	{
	    $result = tubepress_spi_version_Version::parse('7');

	    $this->assertTrue($result->compareTo(new tubepress_spi_version_Version(7)) === 0);
	}

	function testParseEmpty()
	{
	    $result = tubepress_spi_version_Version::parse('');

	    $this->assertTrue($result->compareTo(new tubepress_spi_version_Version(0)) === 0);
	}

	function testCompareQualifiers()
	{
	    $firstOne  = new tubepress_spi_version_Version(1, 2, 80, 'bar');
	    $secondOne = new tubepress_spi_version_Version(1, 2, 80, 'foo');

	    $this->assertTrue($firstOne->compareTo($secondOne) === strcmp('bar', 'foo'));
	    $this->assertTrue($secondOne->compareTo($firstOne) === strcmp('foo', 'bar'));
	}

	function testCompareMicros()
	{
	    $firstOne  = new tubepress_spi_version_Version(1, 2, 80);
	    $secondOne = new tubepress_spi_version_Version(1, 2, 15);

	    $this->assertTrue($firstOne->compareTo($secondOne) === 65);
	    $this->assertTrue($secondOne->compareTo($firstOne) === -65);
	}

	function testCompareMinors()
	{
	    $firstOne  = new tubepress_spi_version_Version(1, 30);
	    $secondOne = new tubepress_spi_version_Version(1, 25);

	    $this->assertTrue($firstOne->compareTo($secondOne) === 5);
	    $this->assertTrue($secondOne->compareTo($firstOne) === -5);
	}

	function testCompareMajors()
	{
	    $firstOne  = new tubepress_spi_version_Version(100);
	    $secondOne = new tubepress_spi_version_Version(50);

	    $this->assertTrue($firstOne->compareTo($secondOne) === 50);
	    $this->assertTrue($secondOne->compareTo($firstOne) === -50);
	}

	function testCompareTwoEqual()
	{
	    $firstOne  = new tubepress_spi_version_Version(1);
	    $secondOne = new tubepress_spi_version_Version(1);

	    $this->assertTrue($firstOne->compareTo($secondOne) === 0);
	    $this->assertTrue($secondOne->compareTo($firstOne) === 0);
	}

	function testCompareString()
	{
	    $firstOne  = new tubepress_spi_version_Version(1);

	    $this->assertTrue($firstOne->compareTo('1') === 0);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	function testParseNonString()
	{
	    tubepress_spi_version_Version::parse(array());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	function testConstructNegativeMicro()
	{
	    new tubepress_spi_version_Version(1, 1, -1);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	function testConstructNegativeMinor()
	{
	    new tubepress_spi_version_Version(1, -1);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	function testConstructNegativeMajor()
	{
	    new tubepress_spi_version_Version(-1);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	function testConstructInvalidQualifier()
	{
	    new tubepress_spi_version_Version(1, 2, 3, '**&&');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	function testParseInvalid()
	{
	    tubepress_spi_version_Version::parse('1.2.3.4.5');
	}

	function testToStringWithQualifier()
	{
	    $version = new tubepress_spi_version_Version(1, 2, 3, 'boogy');

	    $this->assertTrue("$version" === '1.2.3.boogy');
	}

	function testGettersWithQualifier()
	{
	    $version = new tubepress_spi_version_Version('3', '2', '1', 'foobar');

	    $this->assertTrue(3 === $version->getMajor());
	    $this->assertTrue(2 === $version->getMinor());
	    $this->assertTrue(1 === $version->getMicro());
	    $this->assertTrue('foobar' === $version->getQualifier());
	}

	function testGettersNoQualifier()
	{
	    $version = new tubepress_spi_version_Version('3', '2', '1');

	    $this->assertTrue(3 === $version->getMajor());
	    $this->assertTrue(2 === $version->getMinor());
	    $this->assertTrue(1 === $version->getMicro());
	    $this->assertTrue(null === $version->getQualifier());
	}
}
