<?php
require_once BASE . '/sys/classes/org/tubepress/impl/bootstrap/TubePressBootstrapper.php';
require_once BASE . '/sys/classes/org/tubepress/impl/env/wordpress/DefaultWidgetHandler.php';

class org_tubepress_impl_template_templates_wordpress_WidgetControlsTemplateTest extends TubePressUnitTest {

    public function test()
    {
        ${org_tubepress_impl_env_wordpress_Widget::WIDGET_SUBMIT_TAG}    = '<<widget-submit-tag>>';
        ${org_tubepress_impl_env_wordpress_Widget::WIDGET_CONTROL_TITLE} = '<<widget-control-title>>';
        ${org_tubepress_impl_env_wordpress_Widget::WIDGET_TITLE}         = '<<widget-title>>';
        ${org_tubepress_impl_env_wordpress_Widget::WIDGET_SHORTCODE}     = '<<widget-shortcode>>';
        ${org_tubepress_impl_env_wordpress_Widget::WIDGET_SUBMIT_TAG}    = '<<widget-submit-tag>>';
        ${org_tubepress_impl_env_wordpress_Widget::WIDGET_CONTROL_SHORTCODE} = '<<widget-control-shortcode>>';

        $nonceMock = new PHPUnit_Extensions_MockFunction('wp_nonce_field');
        $nonceMock->expects($this->once())->will($this->returnCallback(array($this, 'doNonce')));

        ob_start();
        include BASE . '/sys/ui/templates/wordpress/widget_controls.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($this->_expected(), $result);
    }

    public function doNonce()
    {
        echo 'nonce';
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
<input type="hidden" id="tubepress-widget-submit" name="tubepress-widget-submit" value="1" />
nonce
EOT;
    }

}