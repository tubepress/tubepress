<?php

require_once BASE . '/sys/classes/org/tubepress/impl/plugin/listeners/SkeletonExistsListener.class.php';

class org_tubepress_impl_plugin_listeners_SkeletonExistsListenerTest extends TubePressUnitTest
{
    private $_sut;

    function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_plugin_listeners_SkeletonExistsListener();
        
        if (!defined('ABSPATH')) {
        	define('ABSPATH', '/value-of-abspath/');
        }
    }

    function testWordPress()
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();

        $ed  = $ioc->get('org_tubepress_api_environment_Detector');
        $ed->shouldReceive('isWordPress')->once()->andReturn(true);

        $fse  = $ioc->get('org_tubepress_api_filesystem_Explorer');
        $fse->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath>>');
		$fse->shouldReceive('copyDirectory')->once()->with('<<basepath>>/sys/skel/tubepress-content', '/value-of-abspath/wp-content');
        
        $this->_sut->on_boot();
    }
    
    function testNonWordPress()
    {
    	$ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
    
    	$ed  = $ioc->get('org_tubepress_api_environment_Detector');
    	$ed->shouldReceive('isWordPress')->once()->andReturn(false);
    
    	$fse  = $ioc->get('org_tubepress_api_filesystem_Explorer');
    	$fse->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath>>');
    	$fse->shouldReceive('copyDirectory')->once()->with('<<basepath>>/sys/skel/tubepress-content', '<<basepath>>');
    
    	$this->_sut->on_boot();
    }
}
