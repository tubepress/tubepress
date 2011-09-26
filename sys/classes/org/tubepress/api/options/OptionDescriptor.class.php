<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../impl/classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_provider_Provider'
));

/**
 * TubePress option descriptor.
 */
class org_tubepress_api_options_OptionDescriptor
{
    const _ = 'org_tubepress_api_options_OptionDescriptor';
    
    /** What's the name, y'all? */
    private $_name;

    /** You got a label? */
    private $_label;

    /** Friendly description. */
    private $_description;

    /** Pro only? */
    private $_proOnly;

    /** Aliases. */
    private $_aliases;

    /** Providers for which this option does not work. */
    private $_excludedProviders;

    /** Regex describing valid values that this option can take on (from a string). */
    private $_validValueRegex;

    /** Can this option be set via shortcode? */
    private $_shortcodeSettable;

    /** Should we store this option in persistent storage? */
    private $_shouldPersist;

    /** What's the default value for this option? */
    private $_defaultValue;

    /** Associative array of label to values. */
    private $_valueMap;

    /**
     * Constructor.
     *
     * @param string       $name
     * @param string       $label
     * @param unknown_type $defaultValue
     * @param string       $description
     * @param boolean      $proOnly
     * @param array        $aliases
     * @param array        $excludedProviders
     * @param string       $validValueRegex
     * @param boolean      $canBeSetViaShortcode
     * @param boolean      $shouldPersist
     * @param array        $valueMap
     *
     * @throws Exception If any of the supplied values are of the wrong type.
     */
    public function __construct($name, $label, $defaultValue, $description, $proOnly, $aliases,
        $excludedProviders, $validValueRegex, $canBeSetViaShortcode, $shouldPersist, $valueMap)
    {
        if (! is_string($name) || ! isset($name)) {

            throw new Exception('Must supply an option name');
        }

        if (isset($label) && ! is_string($label)) {

            throw new Exception('Label must be a string for ' . $name);
        }

        if ($description !== null && ! is_string($description)) {

            throw new Exception('Description must be a string for ' . $name);
        }

        if (! is_bool($proOnly)) {

            throw new Exception('Pro-only must be a boolean for ' . $name);
        }

        if (! is_array($aliases)) {

            throw new Exception('Aliases must be an array for ' . $name);
        }

        if (! is_array($excludedProviders)) {

            throw new Exception('Excluded providers must be an array for ' . $name);
        }

        if ($validValueRegex !== null && ! is_string($validValueRegex)) {

            throw new Exception('Regex must be a string for ' . $name);
        }

        if (! is_bool($canBeSetViaShortcode)) {

            throw new Exception('"Can be set via shortcode" must be a boolean for ' . $name);
        }

        if (! is_bool($shouldPersist)) {

            throw new Exception('"Should persist" must be a boolean for ' . $name);
        }

        if (! is_array($valueMap) || (! empty($valueMap) && array_keys($valueMap) === range(0, count($valueMap) - 1))) {

            throw new Exception('Value map must be an empty or associative array');
        }

        $this->_name              = $name;
        $this->_label             = $label;
        $this->_defaultValue      = $defaultValue;
        $this->_description       = $description;
        $this->_proOnly           = (boolean) $proOnly;
        $this->_aliases           = $aliases;
        $this->_excludedProviders = $excludedProviders;
        $this->_validValueRegex   = (string) $validValueRegex;
        $this->_shortcodeSettable = (boolean) $canBeSetViaShortcode;
        $this->_shouldPersist     = (boolean) $shouldPersist;
        $this->_valueMap          = $valueMap;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getLabel()
    {
        return $this->_label;
    }

    public function getDefaultValue()
    {
        return $this->_defaultValue;
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function isProOnly()
    {
        return $this->_proOnly;
    }

    public function getAliases()
    {
        return $this->_aliases;
    }

    public function getValidValueRegex()
    {
        return $this->_validValueRegex;
    }

    public function isAbleToBeSetViaShortcode()
    {
        return $this->_shortcodeSettable;
    }

    public function isMeantToBePersisted()
    {
        return $this->_shouldPersist;
    }

    public function isApplicableToYouTube()
    {
        return ! in_array(org_tubepress_api_provider_Provider::YOUTUBE, $this->_excludedProviders);
    }

    public function isApplicableToVimeo()
    {
        return ! in_array(org_tubepress_api_provider_Provider::VIMEO, $this->_excludedProviders);
    }

    public function getValueMap()
    {
        return $this->_valueMap;
    }
}
