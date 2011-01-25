<?php

require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/env/wordpress/Main.class.php';
require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';

defined('WP_PLUGIN_URL') || define('WP_PLUGIN_URL', 'fooby');

function add_option($name, $value)
{
    
}

class org_tubepress_impl_env_wordpress_MainTest extends TubePressUnitTest {
    
    private $_parseCount;
    
    function setUp()
    {
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
        $this->assertEquals($this->headElements(), $contents);
    }
    
    function testInit()
    {
        global $expectedScript;
        $expectedScript = 'jquery';
        org_tubepress_impl_env_wordpress_Main::initAction();
    }
    
    function headElements()
    {
        return <<<EOT

<script type="text/javascript">function getTubePressBaseUrl(){return "";}</script>
<script type="text/javascript" src="/ui/lib/tubepress.js"></script>
<link rel="stylesheet" href="/ui/themes/default/style.css" type="text/css" />

EOT;
    }
}
?>
