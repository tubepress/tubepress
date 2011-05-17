<?php

require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/env/wordpress/Main.class.php';

class org_tubepress_impl_env_wordpress_MainTest extends TubePressUnitTest {
    
    private $_parseCount;
    
    function setUp()
    {
        parent::setUp();
        
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
    }
    
    function getMock($className)
    {
        $mock = parent::getMock($className);
        
        if ($className === 'org_tubepress_api_shortcode_ShortcodeParser') {
            $mock->expects($this->any())
                 ->method('somethingToParse')
                 ->will($this->returnCallback(array($this, 'parserCallback')));
        }
        if ($className === 'org_tubepress_api_html_HeadHtmlGenerator') {
            $mock->expects($this->any())
                 ->method('getHeadInlineJs')
                 ->will($this->returnValue('inlinejs'));
            $mock->expects($this->any())
                 ->method('getHeadHtmlMeta')
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
        global $enqueuedStyles, $enqueuedScripts, $add_options_page_called, $registeredScripts, $registeredStyles, $isAdmin;;
        $isAdmin = false;
        
        org_tubepress_impl_env_wordpress_Main::initAction();

        $this->assertTrue($registeredScripts['tubepress'] === '<tubepressbaseurl>/sys/ui/static/js/tubepress.js');
        $this->assertTrue($enqueuedScripts['tubepress'] === true);
        $this->assertTrue($enqueuedScripts['jquery'] === true);
        $this->assertTrue($enqueuedStyles['tubepress'] === true);
        $this->assertTrue($registeredStyles['tubepress'] === '<tubepressbaseurl>/sys/ui/themes/default/style.css');
    }
}
