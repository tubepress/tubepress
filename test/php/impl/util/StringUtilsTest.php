<?php

require_once BASE . '/sys/classes/org/tubepress/impl/util/StringUtils.class.php';

class org_tubepress_impl_util_StringUtilsTest extends TubePressUnitTest
{
    function testStartsWith()
    {
        $this->assertTrue(org_tubepress_impl_util_StringUtils::startsWith('something', 'some'));
        $this->assertTrue(org_tubepress_impl_util_StringUtils::startsWith('some', 'some'));
        $this->assertFalse(org_tubepress_impl_util_StringUtils::startsWith(array(), 'some'));
        $this->assertFalse(org_tubepress_impl_util_StringUtils::startsWith('some', array()));
    }

    function testEndsWith()
    {
        $this->assertTrue(org_tubepress_impl_util_StringUtils::endsWith('something', 'thing'));
        $this->assertTrue(org_tubepress_impl_util_StringUtils::endsWith('some', 'some'));
        $this->assertFalse(org_tubepress_impl_util_StringUtils::endsWith(array(), 'some'));
        $this->assertFalse(org_tubepress_impl_util_StringUtils::endsWith('some', array()));
    }

	function testCanReplaceFirstOnlyFirstOccurence()
	{
		$this->assertEquals("zxx", org_tubepress_impl_util_StringUtils::replaceFirst("x", "z", "xxx"));
	}

	function testRemoveNewLines()
	{
	    $string = "this\r\n\r\n\n\n\nis\r\r\r\na\r\ntest\r\n\r\n";

	    $this->assertEquals('thisisatest', org_tubepress_impl_util_StringUtils::removeNewLines($string));
	}

	function testRemoveEmptyLines()
	{
		$val = "

		test

		two

		three
		";

		$this->assertEquals("\n		test\n		two\n		three\n		", org_tubepress_impl_util_StringUtils::removeEmptyLines($val));
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

	        $actual = org_tubepress_impl_util_StringUtils::stripslashes_deep($input);

            $this->assertTrue($actual === $expected, "$actual did not equal expected $expected");
	    }
	}
}

