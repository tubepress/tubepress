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
class tubepress_addons_wordpress_impl_options_WordPressOptionsProvider implements tubepress_spi_options_PluggableOptionDescriptorProvider
{
    /**
     * Fetch all the option descriptors from this provider.
     *
     * @return tubepress_spi_options_OptionDescriptor[]
     */
    public function getOptionDescriptors()
    {
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $toReturn            = array();

        if ($environmentDetector->isWordPress()) {

            $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_TITLE);
            $option->setDefaultValue('TubePress');
            $toReturn[] = $option;

            $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_SHORTCODE);
            $option->setDefaultValue('[tubepress thumbHeight=\'105\' thumbWidth=\'135\']');
            $toReturn[] = $option;
        }

        return $toReturn;
    }
}