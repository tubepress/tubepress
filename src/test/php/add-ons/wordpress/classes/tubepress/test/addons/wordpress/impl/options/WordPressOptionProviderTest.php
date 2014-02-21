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
 * @covers tubepress_addons_wordpress_impl_options_WordPressOptionProvider<extended>
 */
class tubepress_test_addons_wordpress_impl_options_WordPressOptionProviderTest extends tubepress_test_impl_options_AbstractOptionProviderTest
{
    /**
     * @return tubepress_spi_options_OptionProvider
     */
    protected function buildSut()
    {
        return new tubepress_addons_wordpress_impl_options_WordPressOptionProvider();
    }

    protected function getMapOfOptionNamesToDefaultValues()
    {
        return array(

            tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_TITLE     => 'TubePress',
            tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_SHORTCODE => '[tubepress thumbHeight=\'105\' thumbWidth=\'135\']'
        );
    }

    protected function getMapOfOptionNamesToUntranslatedLabels()
    {
        return array();
    }

    protected function getMapOfOptionNamesToUntranslatedDescriptions()
    {
        return array();
    }
}