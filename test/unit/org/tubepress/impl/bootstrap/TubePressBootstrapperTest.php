<?php
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/bootstrap/TubePressBootstrapper.class.php';

class org_tubepress_impl_bootstrap_TubePressBootstrapperTest extends TubePressUnitTest {

	private $_sut;

	function setUp()
	{
                parent::setUp();
		$this->_sut = new org_tubepress_impl_bootstrap_TubePressBootstrapper();
	}
	
	function testBoot()
	{
	    $this->_sut->boot();
	    
	    global $tubepress_base_url;
	    $this->assertEquals('<tubepressbaseurl>', $tubepress_base_url);
	}

}
