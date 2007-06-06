<?php

require_once(dirname(__FILE__) . '/../../common/class/TubePressOption.php');

class OptionTest extends PHPUnit_Framework_TestCase {
	var $o;
	
	function setUp() {
		$this->o = new TubePressOption("name", "title", "desc", "def");
		
	}
	
	function testConstructor() {
		$this->assertEquals    ($this->o->_name, "name", $this->o->container);
		$this->assertEquals    ($this->o->_title, "title", $this->o->container);
		$this->assertEquals    ($this->o->_description, "desc", $this->o->container);
		$this->assertEquals    ($this->o->_value, "def", $this->o->container);
	}
	function testGetTitle() {
		$this->assertEquals    ($this->o->getTitle(), "title");
	}
	function testGetValue() {
		$this->assertEquals    ($this->o->getValue(), "def");
	}
	function testGetDescription() {
		$this->assertEquals    ($this->o->getDescription(), "desc");
	}
}
?>
