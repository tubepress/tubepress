<?php

require_once BASE . '/sys/classes/org/tubepress/impl/util/StringUtils.class.php';

class org_tubepress_impl_util_StringUtilsTest extends TubePressUnitTest
{
	function testCanReplaceFirstOnlyFirstOccurence()
	{
		$this->assertEquals("zxx", org_tubepress_impl_util_StringUtils::replaceFirst("x", "z", "xxx"));
	}
	
	function testCleanForSearch()
	{
		$val = "!@#$%^&*(){}[]abcdefghijk|\"\\':;?/>.<,~`lmnopqrstuvwxyz!@#$%^&*(){}[]abcdefghijk|\"\\':;?/>.<,~`lmnopqrstuvwxyz";
		$expected = "abcdefghijk|\"'lmnopqrstuvwxyzabcdefghijk|\"'lmnopqr";
		
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

