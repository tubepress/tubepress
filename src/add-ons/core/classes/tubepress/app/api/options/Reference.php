<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Array-based implementation of tubepress_app_api_options_ReferenceInterface. Suggested that all add-ons
 * use this implementation.
 *
 * This class is final to prevent API breaks.
 */
final class tubepress_app_api_options_Reference implements tubepress_app_api_options_ReferenceInterface
{
    /**
     * @var array
     */
    private $_valueMap;

    /**
     * @var array
     */
    private $_boolMap;

    /**
     * mixed The default value for this option. May be null.
     */
    const PROPERTY_DEFAULT_VALUE = 'defaultValue';

    /**
     * bool True if this option takes on only boolean values, false otherwise.
     */
    const PROPERTY_IS_BOOLEAN = 'isBoolean';

    /**
     * bool Should we store this option in persistent storage?
     */
    const PROPERTY_NO_PERSIST = 'isMeantToBePersisted';

    /**
     * bool True if this option can be set via shortcode, false otherwise.
     */
    const PROPERTY_NO_SHORTCODE = 'isShortcodeSettable';

    /**
     * string The human-readable description of this option. May be empty or null.
     */
    const PROPERTY_UNTRANSLATED_DESCRIPTION = 'untranslatedDescription';

    /**
     * string The short label for this option. 30 chars or less. May be null.
     */
    const PROPERTY_UNTRANSLATED_LABEL = 'untranslatedLabel';

    /**
     * bool True if this option is pro only, false otherwise.
     */
    const PROPERTY_PRO_ONLY = 'proOnly';

    public function __construct(array $valueMap, array $booleanMap = array())
    {
        $this->_valueMap = $valueMap;
        $this->_boolMap  = $booleanMap;
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
        return array_keys($this->_valueMap[self::PROPERTY_DEFAULT_VALUE]);
    }

    /**
     * @param string $optionName The option name.
     *
     * @return bool True if an option with the given name exists, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function optionExists($optionName)
    {
        return array_key_exists($optionName, $this->_valueMap[self::PROPERTY_DEFAULT_VALUE]);
    }

    /**
     * Get a property for the given option.
     *
     * @param string $optionName   The option name.
     * @param string $propertyName The property name.
     *
     * @return tubepress_platform_api_collection_MapInterface
     *
     * @throws InvalidArgumentException If the option name does not exist, or no such property for the option.
     *
     * @api
     * @since 4.0.0
     */
    public function getProperty($optionName, $propertyName)
    {
        if (!$this->hasProperty($optionName, $propertyName)) {

            throw new InvalidArgumentException("$propertyName is not defined for $optionName");
        }

        if (isset($this->_boolMap[$propertyName])) {

            return in_array($optionName, $this->_boolMap[$propertyName]);
        }

        return $this->_valueMap[$propertyName][$optionName];
    }

    /**
     * @param string $optionName   The option name.
     * @param string $propertyName The property name.
     *
     * @return bool The property value as converted to boolean.
     *
     * @throws InvalidArgumentException If the option name does not exist, or no such property for the option.
     *
     * @api
     * @since 4.0.0
     */
    public function getPropertyAsBoolean($optionName, $propertyName)
    {
        return (bool) $this->getProperty($optionName, $propertyName);
    }

    /**
     * @param string $optionName   The option name.
     * @param string $propertyName The property name.
     *
     * @return bool True if this object contains a property with the given name, false otherwise.
     *
     * @throws InvalidArgumentException If the option name does not exist
     *
     * @api
     * @since 4.0.0
     */
    public function hasProperty($optionName, $propertyName)
    {
        $this->_assertOptionExists($optionName);

        if (isset($this->_valueMap[$propertyName]) && array_key_exists($optionName, $this->_valueMap[$propertyName])) {

            return true;
        }

        return isset($this->_boolMap[$propertyName]);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return mixed The default value for this option. May be null.
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    public function getDefaultValue($optionName)
    {
        $this->_assertOptionExists($optionName);

        return $this->_valueMap[self::PROPERTY_DEFAULT_VALUE][$optionName];
    }

    /**
     * @param $optionName string The option name.
     *
     * @return string The human-readable description of this option. May be empty or null.
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    public function getUntranslatedDescription($optionName)
    {
        $this->_assertOptionExists($optionName);

        return $this->_getOptionalProperty($optionName, self::PROPERTY_UNTRANSLATED_DESCRIPTION, null);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return string The short label for this option. 30 chars or less. May be null.
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    public function getUntranslatedLabel($optionName)
    {
        $this->_assertOptionExists($optionName);

        return $this->_getOptionalProperty($optionName, self::PROPERTY_UNTRANSLATED_LABEL, null);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool True if this option can be set via shortcode, false otherwise.
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    public function isAbleToBeSetViaShortcode($optionName)
    {
        $this->_assertOptionExists($optionName);

        return !$this->_getOptionalProperty($optionName, self::PROPERTY_NO_SHORTCODE, false);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool True if this option takes on only boolean values, false otherwise.
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    public function isBoolean($optionName)
    {
        $this->_assertOptionExists($optionName);

        return is_bool($this->getProperty($optionName, self::PROPERTY_DEFAULT_VALUE));
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool Should we store this option in persistent storage?
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    public function isMeantToBePersisted($optionName)
    {
        $this->_assertOptionExists($optionName);

        return !$this->_getOptionalProperty($optionName, self::PROPERTY_NO_PERSIST, false);
    }

    /**
     * @param $optionName string The option name.
     *
     * @return bool Is this option Pro only?
     *
     * @api
     * @since 4.0.0
     *
     * @throws InvalidArgumentException If the option does not exist.
     */
    public function isProOnly($optionName)
    {
        $this->_assertOptionExists($optionName);

        return $this->_getOptionalProperty($optionName, self::PROPERTY_PRO_ONLY, false);
    }

    private function _assertOptionExists($optionName)
    {
        if (!$this->optionExists($optionName)) {

            throw new InvalidArgumentException("$optionName is not a know option");
        }
    }

    private function _getOptionalProperty($optionName, $propertyName, $default)
    {
        if (!$this->hasProperty($optionName, $propertyName)) {

            return $default;
        }

        return $this->getProperty($optionName, $propertyName);
    }
}