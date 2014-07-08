<?php
/**
 * Copyright 2006  2014 TubePress LLC (http://tubepress.com)
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
class tubepress_app_options_impl_easy_EasyReference implements tubepress_app_options_api_ReferenceInterface
{
    /**
     * @var array
     */
    private $_defaultValueMap;

    /**
     * @var array
     */
    private $_labelMap = array();

    /**
     * @var array
     */
    private $_descriptionMap = array();

    /**
     * @var string[]
     */
    private $_doNotPersistOptions = array();

    /**
     * @var string[]
     */
    private $_noShortcodeOptions = array();

    /**
     * @var string[]
     */
    private $_proNames = array();

    /**
     * @var tubepress_platform_api_util_LangUtilsInterface
     */
    private $_langUtils;

    public function __construct(array $defaultValueMap,
                                tubepress_platform_api_util_LangUtilsInterface $langUtils)
    {
        $this->_langUtils = $langUtils;
        $this->_checkNormalAssociativeArray($defaultValueMap, 'Default value map');

        $this->_defaultValueMap = $defaultValueMap;
    }
    /**
     * Fetch all the option names from this provider.
     *
     * @return string[]
     *
     * @api
     * @since 4.0.0
     */
    public function getAllOptionNames()
    {
        return array_keys($this->_defaultValueMap);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return mixed The default value for this option. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getDefaultValue($optionName)
    {
        return $this->_arraySearchOrNull($optionName, $this->_defaultValueMap);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return string The human-readable description of this option. May be empty or null.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedDescription($optionName)
    {
        return $this->_arraySearchOrNull($optionName, $this->_descriptionMap);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return string The short label for this option. 30 chars or less. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedLabel($optionName)
    {
        return $this->_arraySearchOrNull($optionName, $this->_labelMap);
    }

    /**
     * @param $optionName string The option name to lookup.
     *
     * @return bool True if the option exists, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function optionExists($optionName)
    {
        return array_key_exists($optionName, $this->_defaultValueMap);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool True if this option can be set via shortcode, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isAbleToBeSetViaShortcode($optionName)
    {
        return !in_array($optionName, $this->_noShortcodeOptions);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool True if this option takes on only boolean values, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isBoolean($optionName)
    {
        return isset($this->_defaultValueMap[$optionName]) && is_bool($this->_defaultValueMap[$optionName]);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool Should we store this option in persistent storage?
     *
     * @api
     * @since 4.0.0
     */
    public function isMeantToBePersisted($optionName)
    {
        return !in_array($optionName, $this->_doNotPersistOptions);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool Is this option Pro only?
     *
     * @api
     * @since 4.0.0
     */
    public function isProOnly($optionName)
    {
        return in_array($optionName, $this->_proNames);
    }

    public function setMapOfOptionNamesToUntranslatedLabels(array $map)
    {
        $this->_checkNormalAssociativeArray($map, 'Label map');
        $this->_labelMap = $map;
    }

    public function setMapOfOptionNamesToUntranslatedDescriptions(array $map)
    {
        $this->_checkNormalAssociativeArray($map, 'Description map');
        $this->_descriptionMap = $map;
    }

    public function setProOptionNames(array $names)
    {
        $this->_checkStringSet($names, 'Pro option names');
        $this->_proNames = $names;
    }

    public function setDoNotPersistOptions(array $names)
    {
        $this->_checkStringSet($names, 'No persist option names');
        $this->_doNotPersistOptions = $names;
    }

    public function setNoShortcodeOptions(array $names)
    {
        $this->_checkStringSet($names, 'No shortcode option names');
        $this->_noShortcodeOptions = $names;
    }

    private function _arraySearchOrNull($needle, array $haystack)
    {
        if (isset($haystack[$needle])) {

            return $haystack[$needle];
        }

        return null;
    }

    private function _checkNormalAssociativeArray($candidate, $name)
    {
        if (!$this->_langUtils->isAssociativeArray($candidate)) {

            throw new InvalidArgumentException($name . ' must be an associative array.');
        }

        $this->_checkStringSet(array_keys($candidate), $name . ' array keys');
    }

    private function _checkStringSet($candidate, $name)
    {
        if (!$this->_langUtils->isSimpleArrayOfStrings($candidate)) {

            throw new InvalidArgumentException($name . ' must be simple strings.');
        }
    }
}