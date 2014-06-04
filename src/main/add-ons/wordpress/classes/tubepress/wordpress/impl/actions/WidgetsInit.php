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

class tubepress_wordpress_impl_actions_WidgetsInit
{
    /**
     * @var tubepress_core_translation_api_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_wordpress_spi_WpFunctionsInterface
     */
    private $_wpFunctions;

    /**
     * @var tubepress_wordpress_impl_Widget
     */
    private $_widget;

    public function __construct(tubepress_core_translation_api_TranslatorInterface $translator,
                                tubepress_wordpress_spi_WpFunctionsInterface       $wpFunctions,
                                tubepress_wordpress_impl_Widget                    $widget)
    {
        $this->_translator  = $translator;
        $this->_wpFunctions = $wpFunctions;
        $this->_widget      = $widget;
    }

    /**
     * Filter the content (which may be empty).
     */
    public final function action(tubepress_core_event_api_EventInterface $event)
    {
        $widgetOps = array('classname' => 'widget_tubepress', 'description' =>
            $this->_translator->_('Displays YouTube or Vimeo videos with TubePress'));  //>(translatable)<

        $this->_wpFunctions->wp_register_sidebar_widget('tubepress', 'TubePress', array($this, 'printWidgetHtml'), $widgetOps);
        $this->_wpFunctions->wp_register_widget_control('tubepress', 'TubePress', array($this, 'printControlHtml'));
    }

    public function printWidgetHtml($widgetOpts)
    {
        $this->_widget->printWidgetHtml($widgetOpts);
    }

    public function printControlHtml()
    {
        $this->_widget->printControlHtml();
    }
}
