<?php
class TubePressValidatorTest extends PHPUnit_Framework_TestCase {
	
	public function testThumbHeightTooSmall()
	{
		TubePressValidator::validate(TubePressDisplayOptions::THUMB_HEIGHT, 0);
	}
	
	public function testThumbHeightTooLarge()
	{
		TubePressValidator::validate(TubePressDisplayOptions::THUMB_HEIGHT, 91);
	}
	
	public function testThumbHeightOk()
	{
		TubePressValidator::validate(TubePressDisplayOptions::THUMB_HEIGHT, 45);
	}
}
?>