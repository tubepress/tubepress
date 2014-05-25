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
class tubepress_wordpress_impl_options_WordPressOptionProvider implements tubepress_core_api_options_EasyProviderInterface
{
    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    public function getMapOfOptionNamesToUntranslatedLabels()
    {
        return array();
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    public function getMapOfOptionNamesToUntranslatedDescriptions()
    {
        return array();
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding default values.
     */
    public function getMapOfOptionNamesToDefaultValues()
    {
        return array(

            tubepress_wordpress_api_const_OptionNames::WIDGET_TITLE     => 'TubePress',
            tubepress_wordpress_api_const_OptionNames::WIDGET_SHORTCODE => '[tubepress thumbHeight=\'105\' thumbWidth=\'135\']'
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding fixed acceptable values.
     */
    public function getMapOfOptionNamesToFixedAcceptableValues()
    {
        return array();
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding valid value regexes.
     */
    public function getMapOfOptionNamesToValidValueRegexes()
    {
        return array();
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that cannot be set via shortcode.
     */
    public function getOptionNamesThatCannotBeSetViaShortcode()
    {
        return array();
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that cannot be set via shortcode.
     */
    public function getOptionsNamesThatShouldNotBePersisted()
    {
        return array();
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               to that have
     */
    public function getOptionNamesWithDynamicDiscreteAcceptableValues()
    {
        return array();
    }

    /**
     * @param $optionName string The option name.
     *
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding dynamic acceptable values.
     */
    public function getDynamicDiscreteAcceptableValuesForOption($optionName)
    {
        return array();
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that represent positive integers.
     */
    public function getOptionNamesOfPositiveIntegers()
    {
        return array();
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that represent non-negative integers.
     */
    public function getOptionNamesOfNonNegativeIntegers()
    {
        return array();
    }

    /**
     * @return string[] An array, which may be empty but not null, of Pro option names from this provider.
     */
    public function getAllProOptionNames()
    {
        return array();
    }
}