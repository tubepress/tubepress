<?php

require_once(dirname(__FILE__) . '/../../../../common/class/options/TubePressIntegerOpt.php');

class TubePressIntegerOptTest extends PHPUnit_Framework_TestCase {

	var $o;
	
	function setUp() {
		$this->o = new TubePressIntegerOpt("title", "desc", 4, 10);
	}
	
	function testSetInt() {
		$result = $this->o->setValue(9);
		$this->assertFalse(PEAR::isError($result));
		$result = $this->o->setValue("09");
		$this->assertFalse(PEAR::isError($result));
		$result = $this->o->setValue(0);
		$this->assertTrue(PEAR::isError($result));
		$result = $this->o->setValue(10);
		$this->assertFalse(PEAR::isError($result));
		$result = $this->o->setValue(11);
		$this->assertTrue(PEAR::isError($result));
	}
	
	
	function testSetStringOnInt() {
		$result = $this->o->setValue("testtest");
		$this->assertTrue(PEAR::isError($result));
	}
	
	function testBasics() {
		$this->assertEquals    ($this->o->_title, "title");
		$this->assertEquals    ($this->o->_description, "desc");
		$this->assertEquals    ($this->o->_value, 4);
		$this->assertEquals    ($this->o->getTitle(), "title");
		$this->assertEquals    ($this->o->getValue(), 4);
		$this->assertEquals    ($this->o->getDescription(), "desc");
	}
}
?>
