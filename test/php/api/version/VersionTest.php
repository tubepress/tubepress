<?php

require_once BASE . '/sys/classes/org/tubepress/api/version/Version.class.php';

class org_tubepress_api_version_VersionTest extends TubePressUnitTest {

	function testToStringNoQualifier()
	{
	    $version = new org_tubepress_api_version_Version(1, 2, 3);

		$this->assertTrue("$version" === '1.2.3');
	}

	function testParseFull()
	{
	    $result = org_tubepress_api_version_Version::parse('7.3.4.fuzzy');

	    $this->assertTrue($result->compareTo(new org_tubepress_api_version_Version(7, 3, 4, 'fuzzy')) === 0);
	}

	function testParseTriple()
	{
	    $result = org_tubepress_api_version_Version::parse('7.3.4');

	    $this->assertTrue($result->compareTo(new org_tubepress_api_version_Version(7, 3, 4)) === 0);
	}

	function testParseDouble()
	{
	    $result = org_tubepress_api_version_Version::parse('7.3');

	    $this->assertTrue($result->compareTo(new org_tubepress_api_version_Version(7, 3)) === 0);
	}

	function testParseSingle()
	{
	    $result = org_tubepress_api_version_Version::parse('7');

	    $this->assertTrue($result->compareTo(new org_tubepress_api_version_Version(7)) === 0);
	}

	function testParseEmpty()
	{
	    $result = org_tubepress_api_version_Version::parse('');

	    $this->assertTrue($result->compareTo(new org_tubepress_api_version_Version(0)) === 0);
	}

	function testCompareQualifiers()
	{
	    $firstOne  = new org_tubepress_api_version_Version(1, 2, 80, 'bar');
	    $secondOne = new org_tubepress_api_version_Version(1, 2, 80, 'foo');

	    $this->assertTrue($firstOne->compareTo($secondOne) === strcmp('bar', 'foo'));
	    $this->assertTrue($secondOne->compareTo($firstOne) === strcmp('foo', 'bar'));
	}

	function testCompareMicros()
	{
	    $firstOne  = new org_tubepress_api_version_Version(1, 2, 80);
	    $secondOne = new org_tubepress_api_version_Version(1, 2, 15);

	    $this->assertTrue($firstOne->compareTo($secondOne) === 65);
	    $this->assertTrue($secondOne->compareTo($firstOne) === -65);
	}

	function testCompareMinors()
	{
	    $firstOne  = new org_tubepress_api_version_Version(1, 30);
	    $secondOne = new org_tubepress_api_version_Version(1, 25);

	    $this->assertTrue($firstOne->compareTo($secondOne) === 5);
	    $this->assertTrue($secondOne->compareTo($firstOne) === -5);
	}

	function testCompareMajors()
	{
	    $firstOne  = new org_tubepress_api_version_Version(100);
	    $secondOne = new org_tubepress_api_version_Version(50);

	    $this->assertTrue($firstOne->compareTo($secondOne) === 50);
	    $this->assertTrue($secondOne->compareTo($firstOne) === -50);
	}

	function testCompareTwoEqual()
	{
	    $firstOne  = new org_tubepress_api_version_Version(1);
	    $secondOne = new org_tubepress_api_version_Version(1);

	    $this->assertTrue($firstOne->compareTo($secondOne) === 0);
	    $this->assertTrue($secondOne->compareTo($firstOne) === 0);
	}

	function testCompareString()
	{
	    $firstOne  = new org_tubepress_api_version_Version(1);

	    $this->assertTrue($firstOne->compareTo('1') === 0);
	}

	/**
	 * @expectedException Exception
	 */
	function testParseNonString()
	{
	    org_tubepress_api_version_Version::parse(array());
	}

	/**
	 * @expectedException Exception
	 */
	function testConstructNegativeMicro()
	{
	    new org_tubepress_api_version_Version(1, 1, -1);
	}

	/**
	 * @expectedException Exception
	 */
	function testConstructNegativeMinor()
	{
	    new org_tubepress_api_version_Version(1, -1);
	}

	/**
	 * @expectedException Exception
	 */
	function testConstructNegativeMajor()
	{
	    new org_tubepress_api_version_Version(-1);
	}

	/**
	 * @expectedException Exception
	 */
	function testConstructInvalidQualifier()
	{
	    new org_tubepress_api_version_Version(1, 2, 3, '**&&');
	}

	/**
	 * @expectedException Exception
	 */
	function testParseInvalid()
	{
	    org_tubepress_api_version_Version::parse('1.2.3.4.5');
	}

	function testToStringWithQualifier()
	{
	    $version = new org_tubepress_api_version_Version(1, 2, 3, 'boogy');

	    $this->assertTrue("$version" === '1.2.3.boogy');
	}

	function testGettersWithQualifier()
	{
	    $version = new org_tubepress_api_version_Version('3', '2', '1', 'foobar');

	    $this->assertTrue(3 === $version->getMajor());
	    $this->assertTrue(2 === $version->getMinor());
	    $this->assertTrue(1 === $version->getMicro());
	    $this->assertTrue('foobar' === $version->getQualifier());
	}

	function testGettersNoQualifier()
	{
	    $version = new org_tubepress_api_version_Version('3', '2', '1');

	    $this->assertTrue(3 === $version->getMajor());
	    $this->assertTrue(2 === $version->getMinor());
	    $this->assertTrue(1 === $version->getMicro());
	    $this->assertTrue(null === $version->getQualifier());
	}
}
