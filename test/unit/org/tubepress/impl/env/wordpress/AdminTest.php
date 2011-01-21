<?php

require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/env/wordpress/Admin.class.php';
require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';

define('WP_PLUGIN_URL', 'fooby');
global $wp_enqueue_style_called, $add_options_page_called, $expectedScript;
$wp_enqueue_style_called = false;
$add_options_page_called = false;

function add_options_page($one, $two, $three, $four, $five)
{
    if ($one !== 'TubePress Options') {
        throw new Exception("add_options_page called with wrong first arg: $one");
    }
    if ($two !== 'TubePress') {
        throw new Exception("add_options_page called with wrong second arg: $two");
    }
    if ($three !== 'manage_options') {
        throw new Exception("add_options_page called with wrong third arg: $three");
    }
    if (strpos($four, 'classes/org/tubepress/impl/env/wordpress/Admin.class.php') === false) {
        throw new Exception("Bad file path: $four");
    }
    if (!is_array($five)) {
        throw new Exception("non-array passed to add_options_page");
    }
    if ($five[0] !== 'org_tubepress_impl_env_wordpress_Admin') {
        throw new Exception("Bad callback");
    }
    if ($five[1] !== 'conditionalExecuteOptionsPage') {
        throw new Exception('Bad callback');
    }
    global $add_options_page_called;
    $add_options_page_called = true;
}

function wp_enqueue_style($one, $two) 
{
    if ($one !== 'jquery-ui-flick') {
        throw new Exception('wp_enqueue_style called with wrong args');
    }
    if ($two !== 'fooby/tubepress/ui/lib/options_page/css/flick/jquery-ui-1.7.2.custom.css') {
        throw new Exception("wp_enqueue_style called with wrong arg: $two");
    }
    global $wp_enqueue_style_called;
    $wp_enqueue_style_called = true;
}
        
function wp_enqueue_script($script)
{
    global $expectedScript;
    if ($script !== $expectedScript) {
        throw new Exception("Wrong script enqueued: $script");
    }
}

class org_tubepress_impl_env_wordpress_AdminTest extends TubePressUnitTest {
    
    function setUp()
    {
        global $wp_enqueue_style_called, $add_options_page_called;
        $wp_enqueue_style_called = false;
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
    
    function testHead()
    {
        ob_start();
        org_tubepress_impl_env_wordpress_Admin::headAction();
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('<script type="text/javascript" src="/ui/lib/options_page/js/jscolor/jscolor.js"></script>', $contents);
    }
    
    function testInit()
    {
        global $wp_enqueue_style_called;
        global $expectedScript;
        $expectedScript = 'jquery-ui-tabs';
        org_tubepress_impl_env_wordpress_Admin::initAction();
        $this->assertTrue($wp_enqueue_style_called);
    }
}
?>
