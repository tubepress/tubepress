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

class tubepress_addons_wordpress_impl_filters_RowMeta
{
    /**
     * Filter the content (which may be empty).
     */
    public final function filter(array $args)
    {
        $links = $args[0];
        $file  = $args[1];

        $wordPressFunctionWrapper = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);

        $plugin = $wordPressFunctionWrapper->plugin_basename(basename(TUBEPRESS_ROOT) . '/tubepress.php');

        if ($file != $plugin) {

            return $links;
        }

        return array_merge($links, array(

            sprintf('<a href="options-general.php?page=tubepress.php">%s</a>', $wordPressFunctionWrapper->__('Settings', 'tubepress')),
            sprintf('<a href="http://docs.tubepress.com/">Documentation</a>'),
            sprintf('<a href="http://community.tubepress.com/">Support</a>'),
        ));
    }
}
