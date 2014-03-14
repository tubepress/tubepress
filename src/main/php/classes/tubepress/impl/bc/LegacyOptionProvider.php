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
 * Provides BC for legacy add-ons.
 */
class tubepress_impl_bc_LegacyOptionProvider extends tubepress_impl_options_AbstractOptionProvider
{
    /**
     * @var array
     */
    private $_mapOfOptionNamesToUntranslatedLabels;

    /**
     * @var array
     */
    private $_mapOfOptionNamesToUntranslatedDescriptions;

    /**
     * @var array
     */
    private $_mapOfOptionNamesToDefaultValues;

    /**
     * @var array
     */
    private $_optionNamesThatCannotBeSetViaShortcode = array();

    /**
     * @var array
     */
    private $_optionNamesThatShouldNotBePersisted = array();

    /**
     * @var array
     */
    private $_mapOfOptionNamesToFixedAcceptableValues = array();

    /**
     * @var array
     */
    private $_mapOfOptionNamesToValidValueRegexes = array();

    public function __construct(array $mapOfOptionNamesToUntranslatedLabels,
        array $mapOfOptionNamesToUntranslatedDescriptions, array $mapOfOptionNamesToDefaultValues)
    {
        $this->_mapOfOptionNamesToUntranslatedLabels       = $mapOfOptionNamesToUntranslatedLabels;
        $this->_mapOfOptionNamesToUntranslatedDescriptions = $mapOfOptionNamesToUntranslatedDescriptions;
        $this->_mapOfOptionNamesToDefaultValues            = $mapOfOptionNamesToDefaultValues;
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    protected function getMapOfOptionNamesToUntranslatedLabels()
    {
        return $this->_mapOfOptionNamesToUntranslatedLabels;
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    protected function getMapOfOptionNamesToUntranslatedDescriptions()
    {
        return $this->_mapOfOptionNamesToUntranslatedDescriptions;
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding default values.
     */
    protected function getMapOfOptionNamesToDefaultValues()
    {
        return $this->_mapOfOptionNamesToDefaultValues;
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that cannot be set via shortcode.
     */
    protected function getOptionNamesThatCannotBeSetViaShortcode()
    {
        return $this->_optionNamesThatCannotBeSetViaShortcode;
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that cannot be set via shortcode.
     */
    protected function getOptionsNamesThatShouldNotBePersisted()
    {
        return $this->_optionNamesThatShouldNotBePersisted;
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding fixed acceptable values.
     */
    protected function getMapOfOptionNamesToFixedAcceptableValues()
    {
        return $this->_mapOfOptionNamesToFixedAcceptableValues;
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding valid value regexes.
     */
    protected function getMapOfOptionNamesToValidValueRegexes()
    {
        return $this->_mapOfOptionNamesToValidValueRegexes;
    }

    public function setOptionAsNonShortcodeSettable($optionName)
    {
        $this->_optionNamesThatCannotBeSetViaShortcode[] = $optionName;
    }

    public function setOptionAsDoNotPersist($optionName)
    {
        $this->_optionNamesThatShouldNotBePersisted[] = $optionName;
    }

    public function setAcceptableValues($optionName, array $values)
    {
        $this->_mapOfOptionNamesToFixedAcceptableValues[$optionName] = $values;
    }

    public function setValidValueRegex($optionName, $regex)
    {
        $this->_mapOfOptionNamesToValidValueRegexes[$optionName] = $regex;
    }
}