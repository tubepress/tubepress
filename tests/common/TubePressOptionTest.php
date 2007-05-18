<?php

require_once(dirname(__FILE__) . '/../../common/TubePressOption.php');

class OptionTest extends UnitTestCase {
	var $o;
	
	function setUp() {
		$this->o = new TubePressOption("name", "title", "desc", "def");
		
	}
	
	function testConstructor() {
		$this->assertEqual($this->o->_name, "name", $this->o->container);
		$this->assertEqual($this->o->_title, "title", $this->o->container);
		$this->assertEqual($this->o->_description, "desc", $this->o->container);
		$this->assertEqual($this->o->_value, "def", $this->o->container);
	}
	function testGetTitle() {
		$this->assertEqual($this->o->getTitle(), "title");
	}
	function testGetValue() {
		$this->assertEqual($this->o->getValue(), "def");
	}
	function testGetDescription() {
		$this->assertEqual($this->o->getDescription(), "desc");
	}
}
?>
