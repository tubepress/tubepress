<?php

require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/filters/feedresult/VideoPrepender.class.php';

class org_tubepress_impl_filters_feedresult_VideoPrependerTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_filters_feedresult_VideoPrepender();
	}
	
	function getMock($className)
	{
	    $mock = parent::getMock($className);

	    
	    return $mock;
	}

	function testNoCustomVideo()
	{
	    $arr = array();
	    $result = $this->_sut->filter(array(), 1);
	    $this->assertEquals($arr, $result);
	}
	
}
?>
