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

class tubepress_impl_util_StringUtilsTest extends PHPUnit_Framework_TestCase
{
    function testStartsWith()
    {
        $this->assertTrue(tubepress_impl_util_StringUtils::startsWith('something', 'some'));
        $this->assertTrue(tubepress_impl_util_StringUtils::startsWith('some', 'some'));
        $this->assertFalse(tubepress_impl_util_StringUtils::startsWith(array(), 'some'));
        $this->assertFalse(tubepress_impl_util_StringUtils::startsWith('some', array()));
    }

    function testEndsWith()
    {
        $this->assertTrue(tubepress_impl_util_StringUtils::endsWith('something', 'thing'));
        $this->assertTrue(tubepress_impl_util_StringUtils::endsWith('some', 'some'));
        $this->assertFalse(tubepress_impl_util_StringUtils::endsWith(array(), 'some'));
        $this->assertFalse(tubepress_impl_util_StringUtils::endsWith('some', array()));
    }

	function testCanReplaceFirstOnlyFirstOccurence()
	{
		$this->assertEquals("zxx", tubepress_impl_util_StringUtils::replaceFirst("x", "z", "xxx"));
	}

	function testRemoveNewLines()
	{
	    $string = "this\r\n\r\n\n\n\nis\r\r\r\na\r\ntest\r\n\r\n";

	    $this->assertEquals('thisisatest', tubepress_impl_util_StringUtils::removeNewLines($string));
	}

	function testRemoveEmptyLines()
	{
		$val = "

		test

		two

		three
		";

		$this->assertEquals("\n		test\n		two\n		three\n		", tubepress_impl_util_StringUtils::removeEmptyLines($val));
	}

	function testStripSlashesDeep()
	{
	    $testPatterns = array(

            '\"Hello\"' => '"Hello"',
	        '\\"Hi\\"'  => '"Hi"',
	        "\\\\\\x"    => 'x',
	        "\'you\\'"   => "'you'"
	    );

	    foreach ($testPatterns as $input => $expected) {

	        $actual = tubepress_impl_util_StringUtils::stripslashes_deep($input);

            $this->assertTrue($actual === $expected, "$actual did not equal expected $expected");
	    }
	}
}

