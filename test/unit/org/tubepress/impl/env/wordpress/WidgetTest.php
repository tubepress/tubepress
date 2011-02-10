<?php

require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/env/wordpress/Widget.class.php';
require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once 'fake_wordpress_functions.inc.php';

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
        
        if ($className === 'org_tubepress_api_html_HtmlGenerator') {
            $mock->expects($this->any())
                 ->method('getHtmlForShortcode')
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
        org_tubepress_impl_env_wordpress_Widget::printWidget(array(
		'before_widget' => 'before_widget',
		'before_title' => 'before_title',
		'after_title' => 'after_title',
		'after_widget' => 'after_widget'
        ));
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('before_widgetbefore_titleTubePressafter_titlesomehtmlafter_widget', $contents);
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
<label for="tubepress-widget-title"><input class="widefat" id="tubepress-widget-title" name="tubepress-widget-title" type="text" value="foo" /></label>
</p>
<p>
<label for="tubepress-widget-tagstring">TubePress shortcode for the widget. See the <a href="http://tubepress.org/documentation"> documentation</a>.<textarea class="widefat" rows="9" cols="12" id="tubepress-widget-tagstring" name="tubepress-widget-tagstring">foo</textarea>
</label>
</p>
<input type="hidden" id="tubepress-widget-submit" name="tubepress-widget-submit" value="1" />

EOT;
    }
}
?>
