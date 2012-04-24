<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_api_options_OptionValidator',
    'org_tubepress_api_options_StorageManager',
    'org_tubepress_api_options_OptionValidator',
    'org_tubepress_api_plugin_PluginManager',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_impl_log_Log',
    'org_tubepress_impl_options_OptionsReference',
));

/**
 * Holds the current options for TubePress. This is the default options,
 * usually in persistent storage somewhere, and custom options parsed
 * from a shortcode
 */
class org_tubepress_impl_exec_MemoryExecutionContext implements org_tubepress_api_exec_ExecutionContext
{
    private static $_logPrefix = 'Memory Execution Context';

    /**
     * The user's "custom" options that differ from what's in storage.
     */
    private $_customOptions = array();

    /**
     * The actual shortcode used.
     */
    private $_shortcode;

    /**
     * The storage manager backing us.
     */
    private $_storageManager;

    /**
     * A handle to the validation service.
     */
    private $_validationService;

    /**
     * A handle to the plugin manager.
     */
    private $_pluginManager;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $ioc                      = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_storageManager    = $ioc->get(org_tubepress_api_options_StorageManager::_);
        $this->_validationService = $ioc->get(org_tubepress_api_options_OptionValidator::_);
        $this->_pluginManager     = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
    }

    /**
     * Resets the context.
     *
     * @return void
     */
    public function reset()
    {
        $this->_customOptions = array();
        $this->_shortcode     = '';
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

        return $this->_storageManager->get($optionName);
    }

    /**
     * Sets the value of an option
     *
     * @param string  $optionName  The name of the option
     * @param unknown $optionValue The option value
     *
     * @return True if the option was set normally, otherwise a string error message.
     */
    public function set($optionName, $optionValue)
    {
        /** First run it through the filters. */
        $filtered = $this->_pluginManager->runFilters(org_tubepress_api_const_plugin_FilterPoint::OPTION_SET_PRE_VALIDATION, $optionValue, $optionName);

        if ($this->_validationService->isValid($optionName, $filtered)) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Accepted valid value: %s = %s', $optionName, $filtered);

            $this->_customOptions[$optionName] = $filtered;

            return true;
        }

        $problemMessage = $this->_validationService->getProblemMessage($optionName, $filtered);

        org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Ignoring invalid value for "%s" (%s)', $optionName, $problemMessage);

        return $problemMessage;
    }

    /**
     * Sets the options that differ from the default options.
     *
     * @param array $customOpts The custom options.
     *
     * @return An array of error messages. May be empty, never null.
     */
    public function setCustomOptions($customOpts)
    {
    	if (! is_array($customOpts)) {

    	    //TODO: this should throw an exception or something...
    		return;
    	}

    	$this->_customOptions = array();
    	$problemMessages      = array();

    	foreach ($customOpts as $key => $value) {

            $result = $this->set($key, $value);

            if ($result === true) {

                continue;
            }

            $problemMessages[] = $result;
    	}

    	return $problemMessages;
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
    public function setActualShortcodeUsed($newTagString)
    {
        $this->_shortcode = $newTagString;
    }

    /**
     * Get the current shortcode
     *
     * @return string The current shortcode
     */
    public function getActualShortcodeUsed()
    {
        return $this->_shortcode;
    }

    /**
     * Reconstruct the current state of this execution context as a shortcode string.
     *
     * @return string This context as a shortcode string.
     */
    public function toShortcode()
    {
        $trigger  = $this->get(org_tubepress_api_const_options_names_Advanced::KEYWORD);
        $optPairs = array();

        foreach ($this->_customOptions as $name => $value) {

            $optPairs[] = $name . '="' . str_replace('"', '\"', $value) . '"';
        }

        $optString = implode($optPairs, ', ');

        return "[$trigger $optString]";
    }
}
