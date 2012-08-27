<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

class tubepress_spi_version_VersionTest extends PHPUnit_Framework_TestCase
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
