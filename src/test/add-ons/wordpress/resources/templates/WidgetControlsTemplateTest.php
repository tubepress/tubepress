<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

if (! function_exists('wp_nonce_field')) {

    function wp_nonce_field() { echo 'nonce'; }
}

class tubepress_test_wordpress_resources_templates_WidgetControlsTemplateTest extends tubepress_test_TubePressUnitTest
{
    public function test()
    {
        ${tubepress_wordpress_impl_Widget::WIDGET_SUBMIT_TAG}    = '<<widget-submit-tag>>';
        ${tubepress_wordpress_impl_Widget::WIDGET_CONTROL_TITLE} = '<<widget-control-title>>';
        ${tubepress_wordpress_impl_Widget::WIDGET_TITLE}         = '<<widget-title>>';
        ${tubepress_wordpress_impl_Widget::WIDGET_SHORTCODE}     = '<<widget-shortcode>>';
        ${tubepress_wordpress_impl_Widget::WIDGET_SUBMIT_TAG}    = '<<widget-submit-tag>>';
        ${tubepress_wordpress_impl_Widget::WIDGET_CONTROL_SHORTCODE} = '<<widget-control-shortcode>>';

        ob_start();
        include TUBEPRESS_ROOT . '/src/main/add-ons/wordpress/resources/templates/widget_controls.tpl.php';
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