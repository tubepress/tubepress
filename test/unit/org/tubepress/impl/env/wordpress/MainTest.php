<?php

require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/env/wordpress/Main.class.php';
require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once 'fake_wordpress_functions.inc.php';

class org_tubepress_impl_env_wordpress_MainTest extends TubePressUnitTest {
    
    private $_parseCount;
    
    function setUp()
    {
        global $enqueuedStyles,
        $enqueuedScripts,
        $add_options_page_called,
        $registeredScripts,
        $registeredStyles;
         
        $enqueuedStyles          = array();
        $enqueuedScripts         = array();
        $registeredStyles        = array();
        $registeredScripts       = array();
        $add_options_page_called = false;

        $this->_parseCount = 0;
        $this->initFakeIoc();
    }
    
    function getMock($className)
    {
        $mock = parent::getMock($className);
        
        if ($className === 'org_tubepress_api_shortcode_ShortcodeParser') {
            $mock->expects($this->any())
                 ->method('somethingToParse')
                 ->will($this->returnCallback(array($this, 'parserCallback')));
        }
        if ($className === 'org_tubepress_api_html_HtmlHandler') {
            $mock->expects($this->any())
                 ->method('getHeadInlineJavaScriptString')
                 ->will($this->returnValue('inlinejs'));
            $mock->expects($this->any())
                 ->method('getHeadMetaString')
                 ->will($this->returnValue('headmeta'));
        }
        return $mock;
    }
    
    function testContentFilterSomethingToParse()
    {
        $this->assertEquals('stuff', org_tubepress_impl_env_wordpress_Main::contentFilter('stuff'));
    }
    
    function testContentFilterNothingToParse()
    {
        $this->assertEquals('stuff', org_tubepress_impl_env_wordpress_Main::contentFilter('stuff'));
    }
    
    function parserCallback()
    {
        if ($this->_parseCount < 2) {
            $this->_parseCount++;
            return true;
        }
        return false;
    }
    
    function testHeadAction()
    {
        ob_start();
        org_tubepress_impl_env_wordpress_Main::headAction();
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertEquals("inlinejs\nheadmeta", $contents);
    }
    
    function testInit()
    {
        global $enqueuedStyles, $enqueuedScripts, $add_options_page_called, $registeredScripts, $registeredStyles;
        
        org_tubepress_impl_env_wordpress_Main::initAction();
        
        $this->assertTrue($registeredScripts['tubepress'] === 'tubepress_base_url/ui/lib/tubepress.js');
        $this->assertTrue($enqueuedScripts['tubepress'] === true);
        $this->assertTrue($enqueuedScripts['jquery'] === true);
        $this->assertTrue($enqueuedStyles['tubepress'] === true);
        $this->assertTrue($registeredStyles['tubepress'] === 'tubepress_base_url/ui/themes/default/style.css');
    }
}
?>
