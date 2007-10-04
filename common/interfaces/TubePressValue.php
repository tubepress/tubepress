<?php
interface TubePressValue {
	public function printForOptionsPage();
	public function updateFromOptionsPage(array $post);
	public function updateManually($newValue);
}
?>