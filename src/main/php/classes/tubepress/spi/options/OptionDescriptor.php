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
 * Describes an option that TubePress can work with.
 */
class tubepress_spi_options_OptionDescriptor
{
    const _ = 'tubepress_spi_options_OptionDescriptor';

    /**
     * @var string The globally unique name of this option.
     */
    private $_name;

    /**
     * @var array An array of acceptable values.
     */
    private $_acceptableValues;

    /**
     * @var callable A callback that produces a set of acceptable values.
     */
    private $_acceptableValuesCallback;

    /**
     * @var array Aliases. Currently not used.
     */
    private $_aliases = array();

    /**
     * @var mixed The default value for this option.
     */
    private $_defaultValue;

    /**
     * @var string The human-readable description of this option. May contain HTML.
     */
    private $_description;

    /**
     * @var bool Boolean flag to indicate if this option is boolean.
     */
    private $_isBoolean = false;

    /**
     * @var string The short label for this option. 30 chars or less.
     */
    private $_label;

    /**
     * @var bool Is this option Pro only?
     */
    private $_proOnly = false;

    /**
     * @var bool Should we store this option in persistent storage?
     */
    private $_shouldPersist = true;

    /**
     * @var bool Can this option be set via shortcode?
     */
    private $_shortcodeSettable = true;

    /**
     * @var string Regex describing valid values that this option can take on (from a string).
     */
    private $_validValueRegex;

    /**
     * Constructor.
     *
     * @param string $name The globally unique name of this option.
     *
     * @throws InvalidArgumentException If the name is null or empty.
     */
    public function __construct($name)
    {
        if (! is_string($name) || ! isset($name)) {

            throw new InvalidArgumentException('Must supply an option name');
        }

        $this->_name = $name;
    }

    /**
     * @return array An array of acceptable values that this option can take. May be null or empty.
     */
    public final function getAcceptableValues()
    {
        if (isset($this->_acceptableValuesCallback)) {

            return call_user_func($this->_acceptableValuesCallback, $this->getName());
        }

        return $this->_acceptableValues;
    }

    /**
     * @return array An array of aliases for this option. Not currently used. May be empty, never null.
     */
    public final function getAliases()
    {
        return $this->_aliases;
    }

    /**
     * @return mixed The default value for this option. May be null.
     */
    public final function getDefaultValue()
    {
        return $this->_defaultValue;
    }

    /**
     * @return string The human-readable description of this option. May be empty or null.
     */
    public final function getDescription()
    {
        return $this->_description;
    }

    /**
     * @return string The short label for this option. 30 chars or less.
     */
    public final function getLabel()
    {
        return $this->_label;
    }

    /**
     * @return string The globally unique name of this option.
     */
    public final function getName()
    {
        return $this->_name;
    }

    /**
     * @return string Regex describing valid values that this option can take on (from a string).
     */
    public final function getValidValueRegex()
    {
        return $this->_validValueRegex;
    }

    /**
     * @return bool True if this option has a description, false otherwise.
     */
    public final function hasDescription()
    {
        return $this->_description !== null;
    }

    /**
     * @return bool True if this option has a discrete set of acceptable values, false otherwise.
     */
    public final function hasDiscreteAcceptableValues()
    {
        return isset($this->_acceptableValuesCallback) || (! empty($this->_acceptableValues));
    }

    /**
     * @return bool True if this option has a label, false otherwise.
     */
    public final function hasLabel()
    {
        return $this->_label !== null;
    }

    /**
     * @return bool True if this option has a valid value regex, false otherwise.
     */
    public final function hasValidValueRegex()
    {
        return $this->_validValueRegex !== null;
    }

    /**
     * @return bool True if this option can be set via shortcode, false otherwise.
     */
    public final function isAbleToBeSetViaShortcode()
    {
        return $this->_shortcodeSettable;
    }

    /**
     * @return bool True if this option takes on only boolean values, false otherwise.
     */
    public final function isBoolean()
    {
        return $this->_isBoolean;
    }

    /**
     * @return bool Should we store this option in persistent storage?
     */
    public final function isMeantToBePersisted()
    {
        return $this->_shouldPersist;
    }

    /**
     * @return bool Is this option Pro only?
     */
    public final function isProOnly()
    {
        return $this->_proOnly;
    }

    /**
     * @param array $values An array of acceptable values for this option.
     *
     * @throws InvalidArgumentException If this option is boolean, or a regular expression has already been set.
     *
     * @return void
     */
    public final function setAcceptableValues(array $values)
    {
        $this->_checkNotBoolean();
        $this->_checkRegexNotSet();
        $this->_checkAcceptableValuesCallbackNotSet();

        $this->_acceptableValues = $values;
    }

    /**
     * @param callable $callback The acceptable values callback.
     *
     * @throws InvalidArgumentException If the callback doesn't implement the right function.
     *
     * @return void
     */
    public final function setAcceptableValuesCallback($callback)
    {
        $this->_checkNotBoolean();
        $this->_checkRegexNotSet();
        $this->_checkAcceptableValuesNotSet();

        if (! is_callable($callback)) {

            throw new InvalidArgumentException('Acceptable values callback is not callable');
        }

        $this->_acceptableValuesCallback = $callback;
    }

    /**
     * @param array $aliases An array of aliases for this option.
     *
     * @throws InvalidArgumentException
     */
    public final function setAliases(array $aliases)
    {
        $this->_aliases = $aliases;
    }

    /**
     * Mark this option as boolean-only.
     *
     * @throws InvalidArgumentException If an array of acceptable values has already been set, or a regex has already
     *                                  been set.
     *
     * @return void
     */
    public final function setBoolean()
    {
        $this->_checkAcceptableValuesCallbackNotSet();
        $this->_checkAcceptableValuesNotSet();
        $this->_checkRegexNotSet();

        $this->_isBoolean = true;
    }

    /**
     * Mark this option as non-settable via shortcode.
     *
     * @return void
     */
    public final function setCannotBeSetViaShortcode()
    {
        $this->_shortcodeSettable = false;
    }

    /**
     * @param mixed $value The default value for this option. May be null.
     *
     * @return void
     */
    public final function setDefaultValue($value)
    {
        $this->_defaultValue = $value;
    }

    /**
     * @param string $description The description for this option.
     *
     * @throws InvalidArgumentException If a non-string description is supplied.
     *
     * @return void
     */
    public final function setDescription($description)
    {
        if (! is_string($description)) {

            throw new InvalidArgumentException('Description must be a string for ' . $this->getName());
        }

        $this->_description = $description;
    }

    /**
     * Mark this option as transient.
     *
     * @return void
     */
    public final function setDoNotPersist()
    {
        $this->_shouldPersist = false;
    }

    /**
     * @param string $label The short label for this option. 30 chars or less.
     *
     * @throws InvalidArgumentException If you supply a non-string for the label.
     *
     * @return void
     */
    public final function setLabel($label)
    {
        if (! is_string($label)) {

            throw new InvalidArgumentException('Label must be a string for ' . $this->getName());
        }

        $this->_label = $label;
    }

    /**
     * Mark this option as pro-only.
     *
     * @return void
     */
    public final function setProOnly()
    {
        $this->_proOnly = true;
    }

    /**
     * @param string $validValueRegex Regex describing valid values that this option can take on (from a string).
     *
     * @throws InvalidArgumentException If a non-string is supplied as the regex.
     *
     * @return void
     */
    public final function setValidValueRegex($validValueRegex)
    {
        if (! is_string($validValueRegex)) {

            throw new InvalidArgumentException('Regex must be a string for ' . $this->getName());
        }

        $this->_checkAcceptableValuesCallbackNotSet();
        $this->_checkAcceptableValuesNotSet();
        $this->_checkNotBoolean();

        $this->_validValueRegex = $validValueRegex;
    }

    private function _checkRegexNotSet()
    {
        if (isset($this->_validValueRegex)) {

            throw new InvalidArgumentException($this->getName() . ' already has a regex set');
        }
    }

    private function _checkAcceptableValuesNotSet()
    {
        if (! empty($this->_acceptableValues)) {

            throw new InvalidArgumentException($this->getName() . ' already has acceptable values set');
        }
    }

    private function _checkNotBoolean()
    {
        if ($this->_isBoolean === true) {

            throw new InvalidArgumentException($this->getName() . ' is set to be a boolean');
        }
    }

    private function _checkAcceptableValuesCallbackNotSet()
    {
        if (isset($this->_acceptableValuesCallback)) {

            throw new InvalidArgumentException($this->getName() . ' already has an acceptable values callback set');
        }
    }
}