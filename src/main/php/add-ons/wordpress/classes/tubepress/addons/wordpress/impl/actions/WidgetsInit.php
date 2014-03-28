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

class tubepress_addons_wordpress_impl_actions_WidgetsInit
{
    /**
     * Filter the content (which may be empty).
     */
    public final function action(tubepress_api_event_EventInterface $event)
    {
        $msg               = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();
        $wpFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);

        $widgetOps = array('classname' => 'widget_tubepress', 'description' =>
            $msg->_('Displays YouTube or Vimeo videos with TubePress'));  //>(translatable)<

        $wpFunctionWrapper->wp_register_sidebar_widget('tubepress', 'TubePress', array($this, 'printWidgetHtml'), $widgetOps);
        $wpFunctionWrapper->wp_register_widget_control('tubepress', 'TubePress', array($this, 'printControlHtml'));
    }

    public function printWidgetHtml($widgetOpts)
    {
        $widget = tubepress_impl_patterns_sl_ServiceLocator::getService('wordpress.widget');

        $widget->printWidgetHtml($widgetOpts);
    }

    public function printControlHtml()
    {
        $widget = tubepress_impl_patterns_sl_ServiceLocator::getService('wordpress.widget');

        $widget->printControlHtml();
    }
}
