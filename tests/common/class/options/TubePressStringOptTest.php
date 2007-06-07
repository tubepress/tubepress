<?php

require_once(dirname(__FILE__) . '/../../../../common/class/options/TubePressStringOpt.php');

class TubePressStringOptTest extends PHPUnit_Framework_TestCase {

	var $o;
	
	function setUp() {
		$this->o = new TubePressStringOpt("title", "desc", "hello");
	}
	
	function testSetValue() {
		$this->o->setValue("bye");
		$this->assertEquals("bye", $this->o->getValue());
	}
	
	function testSetIntOnString() {
		$result = $this->o->setValue(33);
		$this->assertTrue(PEAR::isError($result));
	}
	
	function testSetBoolOnString() {
		$result = $this->o->setValue(true);
		$this->assertTrue(PEAR::isError($result));
	}
	
	function testBasics() {
		$this->assertEquals    ($this->o->_title, "title");
		$this->assertEquals    ($this->o->_description, "desc");
		$this->assertEquals    ($this->o->_value, "hello");
		$this->assertEquals    ($this->o->getTitle(), "title");
		$this->assertEquals    ($this->o->getValue(), "hello");
		$this->assertEquals    ($this->o->getDescription(), "desc");
	}
}
?>
