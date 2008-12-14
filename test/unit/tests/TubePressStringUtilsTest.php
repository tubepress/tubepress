<?php
include_once dirname(__FILE__) . "/../../../tubepress_classloader.php";
class TubePressStringUtilsTest extends PHPUnit_Framework_TestCase
{
	function testCanReplaceFirstOnlyFirstOccurence()
	{
		$this->assertEquals("zxx", TubePressStringUtils::replaceFirst("x", "z", "xxx"));
	}
}
?>