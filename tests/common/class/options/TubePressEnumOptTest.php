<?php

require_once(dirname(__FILE__) . '/../../../../common/class/options/TubePressEnumOpt.php');

class TubePressEnumOptTest extends PHPUnit_Framework_TestCase {

	var $o;
	
	function setUp() {
		$this->o = new TubePressEnumOpt("title", "desc", "one", array("one", "two", "bear"));
	}
	
	function testSetValue() {
		$result = $this->o->setValue("one");
		$this->assertFalse(PEAR::isError($result));
		$result = $this->o->setValue("bear");
		$this->assertFalse(PEAR::isError($result));
		$result = $this->o->setValue("three");
		$this->assertTrue(PEAR::isError($result));
	}
	
	function testBasics() {
		$this->assertEquals    ($this->o->_title, "title");
		$this->assertEquals    ($this->o->_description, "desc");
		$this->assertEquals    ($this->o->_value, "one");
		$this->assertEquals    ($this->o->getTitle(), "title");
		$this->assertEquals    ($this->o->getValue(), "one");
		$this->assertEquals    ($this->o->getDescription(), "desc");
	}
}
?>
