<?php
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/bootstrap/FreeWordPressPluginBootstrapper.class.php';
require_once dirname(__FILE__) . '/../../../../../../test/unit/TubePressUnitTest.php';

class org_tubepress_impl_bootstrap_FreeWordPressPluginBootstrapperTest extends TubePressUnitTest {

	private $_sut;

	function setUp()
	{
	    $this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_bootstrap_FreeWordPressPluginBootstrapper();
	}
	
	function testBoot()
	{
	    $this->_sut->boot();
	    
	    global $tubepress_base_url;
	    $this->assertEquals('valueOf-siteurl/wp-content/plugins/tubepress', $tubepress_base_url);
	}

}
?>
