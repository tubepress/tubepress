<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class org_tubepress_impl_template_templates_wordpress_WidgetControlsTemplateTest extends PHPUnit_Framework_TestCase
{
    public function test()
    {
        ${tubepress_impl_wordpress_DefaultWidgetHandler::WIDGET_SUBMIT_TAG}    = '<<widget-submit-tag>>';
        ${tubepress_impl_wordpress_DefaultWidgetHandler::WIDGET_CONTROL_TITLE} = '<<widget-control-title>>';
        ${tubepress_impl_wordpress_DefaultWidgetHandler::WIDGET_TITLE}         = '<<widget-title>>';
        ${tubepress_impl_wordpress_DefaultWidgetHandler::WIDGET_SHORTCODE}     = '<<widget-shortcode>>';
        ${tubepress_impl_wordpress_DefaultWidgetHandler::WIDGET_SUBMIT_TAG}    = '<<widget-submit-tag>>';
        ${tubepress_impl_wordpress_DefaultWidgetHandler::WIDGET_CONTROL_SHORTCODE} = '<<widget-control-shortcode>>';

        ob_start();
        include __DIR__ . '/../../../../../main/resources/system-templates/wordpress/widget_controls.tpl.php';
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