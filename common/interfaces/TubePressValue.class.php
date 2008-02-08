<?php
interface TubePressValue {
	public function printForOptionsPage(HTML_Template_IT &$tpl);
	public function updateFromOptionsPage(array $post);
	public function updateManually($newValue);
}
?>