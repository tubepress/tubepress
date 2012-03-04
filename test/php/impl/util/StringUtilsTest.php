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

	function testCleanForSearch()
	{
		$val = "!@#$%^&*(){}[]abcdefghijk|\"\\':;?/>.<,~`lmnopqrstuvwxyz!@#$%^&*(){}[]abcdefghijk|\"\\':;?/>.<,~`lmnopqrstuvwxyz";
		$expected = "!@#$%^&amp;*(){}[]abcdefghijk|\"\':;?/&gt;.&lt;,~`lmnopqrstuvwxyz!@#$%^&amp;*(){}[]abcdefghijk|\"\':;?/&gt;.&lt;,~`lmnopqr";

		$this->assertEquals($expected, org_tubepress_impl_util_StringUtils::cleanForSearch($val));
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
}

