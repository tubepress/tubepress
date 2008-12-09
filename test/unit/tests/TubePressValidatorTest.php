<?php
class TubePressValidatorTest extends PHPUnit_Framework_TestCase {
	
	
	public function testThumbHeightOk()
	{
		TubePressValidator::validate(TubePressDisplayOptions::THUMB_HEIGHT, 45);
	}
}
?>