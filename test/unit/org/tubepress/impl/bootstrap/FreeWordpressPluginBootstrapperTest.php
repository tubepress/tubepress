<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/bootstrap/FreeWordPressPluginBootstrapper.class.php';
require_once dirname(__FILE__) . '/../../../../../../test/unit/TubePressUnitTest.php';

if (!function_exists('get_option')) {
    function get_option($something)
    {
        return 'foo';
    }
}

if (!function_exists('load_plugin_textdomain')) {
    function load_plugin_textdomain() {}
}

if (!function_exists('add_filter')) {
    function add_filter() {}
}

if (!function_exists('add_action')) {
    function add_action() {}
}

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
	    $this->assertEquals('foo/wp-content/plugins/tubepress', $tubepress_base_url);
	}

}
?>
