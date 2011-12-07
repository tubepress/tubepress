<?php
require_once BASE . '/sys/classes/org/tubepress/impl/bootstrap/TubePressBootstrapper.class.php';
require_once BASE . '/sys/classes/org/tubepress/impl/env/wordpress/Widget.class.php';

class org_tubepress_impl_template_templates_wordpress_WidgetControlsTemplateTest extends TubePressUnitTest {

    public function test()
    {
        ${org_tubepress_impl_env_wordpress_Widget::WIDGET_SUBMIT_TAG}    = '<<widget-submit-tag>>';
        ${org_tubepress_impl_env_wordpress_Widget::WIDGET_CONTROL_TITLE} = '<<widget-control-title>>';
        ${org_tubepress_impl_env_wordpress_Widget::WIDGET_TITLE}         = '<<widget-title>>';
        ${org_tubepress_impl_env_wordpress_Widget::WIDGET_SHORTCODE}     = '<<widget-shortcode>>';
        ${org_tubepress_impl_env_wordpress_Widget::WIDGET_SUBMIT_TAG}    = '<<widget-submit-tag>>';
        ${org_tubepress_impl_env_wordpress_Widget::WIDGET_CONTROL_SHORTCODE} = '<<widget-control-shortcode>>';


        ob_start();
        include BASE . '/sys/ui/templates/wordpress/widget_controls.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($this->_expected(), $result);
    }

    private function _expected()
    {
        return <<<EOT
<p>
<label for="tubepress-widget-title"><<widget-control-title>><input class="widefat" id="tubepress-widget-title" name="tubepress-widget-title" type="text" value="<<widget-title>>" /></label>
</p>
<p>
<label for="tubepress-widget-tagstring"><<widget-control-shortcode>><textarea class="widefat" rows="9" cols="12" id="tubepress-widget-tagstring" name="tubepress-widget-tagstring"><<widget-shortcode>></textarea>
</label>
</p>
<input type="hidden" id="<<widget-submit-tag>>" name="<<widget-submit-tag>>" value="1" />

EOT;
    }

}