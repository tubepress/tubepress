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
 * 
 */
interface tubepress_core_api_options_EasyProviderInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_core_api_options_EasyProviderInterface';

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     *
     * @api
     * @since 4.0.0
     */
    function getMapOfOptionNamesToUntranslatedLabels();

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     *
     * @api
     * @since 4.0.0
     */
    function getMapOfOptionNamesToUntranslatedDescriptions();

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding default values.
     *
     * @api
     * @since 4.0.0
     */
    function getMapOfOptionNamesToDefaultValues();

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding fixed acceptable values.
     *
     * @api
     * @since 4.0.0
     */
    function getMapOfOptionNamesToFixedAcceptableValues();

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding valid value regexes.
     *
     * @api
     * @since 4.0.0
     */
    function getMapOfOptionNamesToValidValueRegexes();

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that cannot be set via shortcode.
     *
     * @api
     * @since 4.0.0
     */
    function getOptionNamesThatCannotBeSetViaShortcode();

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that cannot be set via shortcode.
     *
     * @api
     * @since 4.0.0
     */
    function getOptionsNamesThatShouldNotBePersisted();

    /**
     * @return array An array, which may be empty but not null, of option names
     *               to that have
     *
     * @api
     * @since 4.0.0
     */
    function getOptionNamesWithDynamicDiscreteAcceptableValues();

    /**
     * @param $optionName string The option name.
     *
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding dynamic acceptable values.
     *
     * @api
     * @since 4.0.0
     */
    function getDynamicDiscreteAcceptableValuesForOption($optionName);

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that represent positive integers.
     *
     * @api
     * @since 4.0.0
     */
    function getOptionNamesOfPositiveIntegers();

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that represent non-negative integers.
     *
     * @api
     * @since 4.0.0
     */
    function getOptionNamesOfNonNegativeIntegers();

    /**
     * @return string[] An array, which may be empty but not null, of Pro option names from this provider.
     *
     * @api
     * @since 4.0.0
     */
    function getAllProOptionNames();
}
