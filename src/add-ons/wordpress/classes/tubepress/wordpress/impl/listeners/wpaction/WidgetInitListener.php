<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_wordpress_impl_listeners_wpaction_WidgetInitListener
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var tubepress_api_translation_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions       $wpFunctions,
                                tubepress_api_translation_TranslatorInterface $translator,
                                tubepress_api_event_EventDispatcherInterface  $eventDispatcher)
    {
        $this->_wpFunctions     = $wpFunctions;
        $this->_translator      = $translator;
        $this->_eventDispatcher = $eventDispatcher;
    }

    public function onAction_widgets_init(tubepress_api_event_EventInterface $event)
    {
        if (!$event->hasArgument('unit-testing') && !class_exists('tubepress_wordpress_impl_wp_WpWidget')) {

            require TUBEPRESS_ROOT . '/src/add-ons/wordpress/classes/tubepress/wordpress/impl/wp/WpWidget.php';
        }

        $this->_wpFunctions->register_widget('tubepress_wordpress_impl_wp_WpWidget');

        /*
         * These next three lines are deprecated!
         */
        $widgetOps = array('classname' => 'widget_tubepress', 'description' =>
            $this->_translator->trans('Displays YouTube or Vimeo videos with TubePress. Limited to a single instance per site. Use the other TubePress widget instead!'));  //>(translatable)<
        $this->_wpFunctions->wp_register_sidebar_widget('tubepress', 'TubePress (legacy)', array($this, '__fireWidgetHtmlEvent'), $widgetOps);
        $this->_wpFunctions->wp_register_widget_control('tubepress', 'TubePress (legacy)', array($this, '__fireWidgetControlEvent'));
    }

    /**
     * @deprecated
     */
    public function __fireWidgetHtmlEvent($widgetOpts)
    {
        $event = $this->_eventDispatcher->newEventInstance($widgetOpts);

        $this->_eventDispatcher->dispatch(tubepress_wordpress_api_Constants::EVENT_WIDGET_PUBLIC_HTML, $event);
    }

    /**
     * @deprecated
     */
    public function __fireWidgetControlEvent()
    {
        $this->_eventDispatcher->dispatch(tubepress_wordpress_api_Constants::EVENT_WIDGET_PRINT_CONTROLS);
    }
}
