<?php

require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/env/wordpress/Widget.class.php';
require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';

define('WP_PLUGIN_URL', 'fooby');

function __($something)
{
    return $something;
}

function wp_register_sidebar_widget($one, $two, $three, $four)
{
    if ($one !== 'tubepress') {
        throw new Exception("bad first arg to wp_register_sidebar_widget");
    }
    if ($two !== 'TubePress') {
        throw new Exception('bad second arg to wp_register_sidebar_widget');
    }
    if (!is_array($three) || count($three) !== 2 || $three[0] !== 'org_tubepress_impl_env_wordpress_Widget' || $three[1] !== 'printWidget') {
        throw new Exception('bad third arg to wp_register_sidebar_widget');
    }
    if (!isset($four)) {
        throw new Exception('missing fourth arg to wp_register_sidebar_widget');
    }
    global $wp_register_sidebar_widget_called;
    $wp_register_sidebar_widget_called = true;
}

function wp_register_widget_control($one, $two, $three)
{
    if ($one !== 'tubepress') {
        throw new Exception("bad first arg to wp_register_sidebar_widget");
    }
    if ($two !== 'TubePress') {
        throw new Exception('bad second arg to wp_register_sidebar_widget');
    }
    if (!is_array($three) || count($three) !== 2 || $three[0] !== 'org_tubepress_impl_env_wordpress_Widget' || $three[1] !== 'printControlPanel') {
        throw new Exception('bad third arg to wp_register_sidebar_widget');
    }
    global $wp_register_widget_control;
    $wp_register_widget_control = true;
}

class org_tubepress_impl_env_wordpress_WidgetTest extends TubePressUnitTest {
    
    function setUp()
    {
        global $wp_register_widget_control, $wp_register_sidebar_widget_called;
        $wp_register_widget_control = false;
        $wp_register_sidebar_widget_called = false;
        $this->initFakeIoc();
    }
    
    function getMock($className)
    {
        $mock = parent::getMock($className);
        
        if ($className === 'org_tubepress_api_gallery_Gallery') {
            $mock->expects($this->any())
                 ->method('getHtml')
                 ->will($this->returnValue('somehtml'));
        }
        
        return $mock;
    }
    
    function testPrintWidgetControl()
    {
        ob_start();
        org_tubepress_impl_env_wordpress_Widget::printControlPanel();
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertEquals($this->widgetControlPanelHtml(), $contents);
    }
    
    function testPrintWidget()
    {
        ob_start();
        org_tubepress_impl_env_wordpress_Widget::printWidget(array());
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('TubePresssomehtml', $contents);
    }
    
    function testInit()
    {
        global $wp_register_widget_control, $wp_register_sidebar_widget_called;
        org_tubepress_impl_env_wordpress_Widget::initAction();
        $this->assertTrue($wp_register_sidebar_widget_called);
        $this->assertTrue($wp_register_widget_control);
    }
    
    function widgetControlPanelHtml()
    {
        return <<<EOT
<p>
<label for="tubepress-widget-title"><input class="widefat" id="tubepress-widget-title" name="tubepress-widget-title" type="text" value="" /></label>
</p>
<p>
<label for="tubepress-widget-tagstring">TubePress shortcode for the widget. See the <a href="http://tubepress.org/documentation"> documentation</a>.<textarea class="widefat" rows="9" cols="12" id="tubepress-widget-tagstring" name="tubepress-widget-tagstring"></textarea>
</label>
</p>
<input type="hidden" id="tubepress-widget-submit" name="tubepress-widget-submit" value="1" />

EOT;
    }
}
?>
