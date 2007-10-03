<?php
interface TubePressValue {
	public function printValueForHTML();
	public function updateValueFromHTML($newValue);
	public function setValue($newValue);
}
?>