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

class tubepress_wordpress_impl_filters_RowMeta
{
    /**
     * @var tubepress_wordpress_spi_WpFunctionsInterface
     */
    private $_wpFunctions;

    public function __construct(tubepress_wordpress_spi_WpFunctionsInterface $wpFunctions)
    {
        $this->_wpFunctions = $wpFunctions;
    }

    /**
     * Filter the content (which may be empty).
     */
    public final function filter(tubepress_core_event_api_EventInterface $event)
    {
        $links = $event->getSubject();
        $args  = $event->getArgument('args');
        $file  = $args[0];

        $plugin = $this->_wpFunctions->plugin_basename(basename(TUBEPRESS_ROOT) . '/tubepress.php');

        if ($file != $plugin) {

            return $links;
        }

        $toReturn = array_merge($links, array(

            sprintf('<a href="options-general.php?page=tubepress.php">%s</a>', $this->_wpFunctions->__('Settings', 'tubepress')),
            sprintf('<a href="http://docs.tubepress.com/">Documentation</a>'),
            sprintf('<a href="http://community.tubepress.com/">Support</a>'),
        ));

        $event->setSubject($toReturn);
    }
}
