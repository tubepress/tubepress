<?php

require_once(dirname(__FILE__) . '/../../../../common/class/options/TubePressBooleanOpt.php');

class TubePressBooleanOptTest extends PHPUnit_Framework_TestCase {

	var $o;
	
	function setUp() {
		$this->o = new TubePressBooleanOpt("title", "desc", true);
	}
	
	function testBasics() {
		$this->assertEquals    ($this->o->_title, "title");
		$this->assertEquals    ($this->o->_description, "desc");
		$this->assertEquals    ($this->o->_value, "def");
		$this->assertEquals    ($this->o->getTitle(), "title");
		$this->assertEquals    ($this->o->getValue(), true);
		$this->assertEquals    ($this->o->getDescription(), "desc");
	}
	
	function testSetBoolean() {
		$result = $this->o->setValue(true);
		$this->assertFalse(PEAR::isError($result));
		$result = $this->o->setValue(false);
		$this->assertFalse(PEAR::isError($result));
		$result = $this->o->setValue("true");
		$this->assertTrue(PEAR::isError($result));
		$result = $this->o->setValue("false");
		$this->assertTrue(PEAR::isError($result));
		$result = $this->o->setValue(34);
		$this->assertTrue(PEAR::isError($result));
		$result = $this->o->setValue("lskdfjlskfjd");
		$this->assertTrue(PEAR::isError($result));
		$result = $this->o->setValue(array());
		$this->assertTrue(PEAR::isError($result));
	}
	
	function testSetStringOnBool() {
		$result = $this->o->setValue("testtest");
		$this->assertTrue(PEAR::isError($result));
	}
}
?>
