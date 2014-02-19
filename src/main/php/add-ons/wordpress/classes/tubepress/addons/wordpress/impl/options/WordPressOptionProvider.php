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

/**
 * Registers a few extensions to allow TubePress to work inside WordPress.
 */
class tubepress_addons_wordpress_impl_options_WordPressOptionProvider extends tubepress_impl_options_AbstractOptionProvider
{
    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    protected function getMapOfOptionNamesToUntranslatedLabels()
    {
        return array();
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    protected function getMapOfOptionNamesToUntranslatedDescriptions()
    {
        return array();
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding default values.
     */
    protected function getMapOfOptionNamesToDefaultValues()
    {
        return array(

            tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_TITLE     => 'TubePress',
            tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_SHORTCODE => '[tubepress thumbHeight=\'105\' thumbWidth=\'135\']'
        );
    }
}