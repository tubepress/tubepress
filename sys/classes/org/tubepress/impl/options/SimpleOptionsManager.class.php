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

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_options_OptionsManager',
    'org_tubepress_api_options_StorageManager',
    'org_tubepress_impl_options_OptionsReference',
    'org_tubepress_api_options_OptionValidator',
    'org_tubepress_impl_ioc_IocContainer'));

/**
 * Holds the current options for TubePress. This is the default options,
 * usually in persistent storage somewhere, and custom options parsed
 * from a shortcode
 */
class org_tubepress_impl_options_SimpleOptionsManager implements org_tubepress_api_options_OptionsManager
{
    private $_customOptions = array();
    private $_shortcode;
    private $_tpsm;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_tpsm = $ioc->get('org_tubepress_api_options_StorageManager');
    }

    /**
     * Gets the value of an option
     *
     * @param string $optionName The name of the option
     * 
     * @return unknown The option value
     */
    public function get($optionName)
    {
        /* get the value, either from the shortcode or the db */
        if (array_key_exists($optionName, $this->_customOptions)) {
            return $this->_customOptions[$optionName];
        }
        return $this->_tpsm->get($optionName);
    }

    /**
     * Sets the value of an option
     *
     * @param string  $optionName  The name of the option
     * @param unknown $optionValue The option value
     * 
     * @return void
     */
    public function set($optionName, $optionValue)
    {
        $this->_customOptions[$optionName] = $optionValue;
    }

    /**
     * Sets the options that differ from the default options.
     *
     * @param array $customOpts The custom options.
     * 
     * @return void
     */
    public function setCustomOptions($customOpts)
    {
        $this->_customOptions = $customOpts;
    }

    /**
     * Gets the options that differ from the default options.
     * 
     * @return array The options that differ from the default options.
     */
    public function getCustomOptions()
    {
        return $this->_customOptions;
    }

    /**
     * Set the current shortcode.
     *
     * @param string $newTagString The current shortcode
     * 
     * @return void
     */
    public function setShortcode($newTagString)
    {
        $this->_shortcode = $newTagString;
    }

    /**
     * Get the current shortcode
     *
     * @return string The current shortcode
     */
    public function getShortcode()
    {
        return $this->_shortcode;
    }
}
