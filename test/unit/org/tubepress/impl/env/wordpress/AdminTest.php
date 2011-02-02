<?php

require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/env/wordpress/Admin.class.php';
require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once 'fake_wordpress_functions.inc.php';

class org_tubepress_impl_env_wordpress_AdminTest extends TubePressUnitTest {
    
    function setUp()
    {
        global $add_options_page_called;
        $add_options_page_called = false;
        $this->initFakeIoc();
    }
    
    function getMock($className)
    {
        $mock = parent::getMock($className);
        
        if ($className === 'org_tubepress_impl_options_FormHandler') {
            $mock->expects($this->any())
                 ->method('getHtml')
                 ->will($this->returnValue('yo'));
        }
        
        return $mock;
    }
    
    function testConditionalExecuteOptionsPage()
    {
        ob_start();
        org_tubepress_impl_env_wordpress_Admin::conditionalExecuteOptionsPage();
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('yo', $contents);
    }
    
    function testMenuAction()
    {
        global $add_options_page_called;
        org_tubepress_impl_env_wordpress_Admin::menuAction();
        $this->assertTrue($add_options_page_called);
    }
    
    function testInit()
    {
        global $enqueuedScripts, $enqueuedStyles, $registeredScripts, $registeredStyles;
        org_tubepress_impl_env_wordpress_Admin::initAction();
        $this->assertTrue($enqueuedScripts['jquery-ui-tabs'] === true);
        $this->assertTrue($enqueuedScripts['jscolor-tubepress'] === true);
        $this->assertTrue($enqueuedStyles['jquery-ui-flick'] === true);
        $this->assertTrue($registeredStyles['jquery-ui-flick'] === "tubepress_base_url/ui/lib/options_page/css/flick/jquery-ui-1.7.2.custom.css");
        $this->assertTrue($registeredScripts['jscolor-tubepress'] === "tubepress_base_url/ui/lib/options_page/js/jscolor/jscolor.js");
    }
}
?>
