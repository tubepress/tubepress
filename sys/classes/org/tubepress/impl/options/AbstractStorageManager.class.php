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
tubepress_load_classes(array('org_tubepress_api_options_StorageManager',
    'org_tubepress_api_const_options_Type',
    'org_tubepress_api_options_OptionValidator',
    'org_tubepress_impl_options_OptionsReference',
    'org_tubepress_impl_ioc_IocContainer'));

/**
 * Handles persistent storage of TubePress options
 */
abstract class org_tubepress_impl_options_AbstractStorageManager implements org_tubepress_api_options_StorageManager
{
    /**
     * Creates an option in storage
     *
     * @param unknown_type $optionName  The name of the option to create
     * @param unknown_type $optionValue The default value of the new option
     * 
     * @return void
     */
    protected abstract function create($optionName, $optionValue);

    /**
     * Deletes an option from storage
     *
     * @param unknown_type $optionName The name of the option to delete
     * 
     * @return void
     */
    protected abstract function delete($optionName);

    /**
     * Initialize the persistent storage
     * 
     * @return void
     */
    public function init()
    {
        $allOptionNames = org_tubepress_impl_options_OptionsReference::getAllOptionNames();
        $vals           = array();
        foreach ($allOptionNames as $optionName) {
            $vals[$optionName] = org_tubepress_impl_options_OptionsReference::getDefaultValue($optionName);
        }

        foreach ($vals as $val => $key) {
            $this->_init($val, $key);
        }
    }

    /**
     * Initializes a single option.
     *
     * @param string $name  The option name.
     * @param string $value The option value.
     *
     * @return void
     */
    private function _init($name, $value)
    {
        if (!org_tubepress_impl_options_OptionsReference::shouldBePersisted($name)) {
            return;
        }

        if (!$this->exists($name)) {
            $this->delete($name);
            $this->create($name, $value);
        }
        if (org_tubepress_impl_options_OptionsReference::getType($name) != org_tubepress_api_const_options_Type::BOOL
            && $this->get($name) == "") {
            $this->setOption($name, $value);
        }
    }

    /**
     * Sets an option value
     *
     * @param string       $optionName  The option name
     * @param unknown_type $optionValue The option value
     * 
     * @return void
     */
    public function set($optionName, $optionValue)
    {
        if (!org_tubepress_impl_options_OptionsReference::shouldBePersisted($optionName)) {
            return;
        }

        $ioc               = org_tubepress_impl_ioc_IocContainer::getInstance();
        $validationService = $ioc->get('org_tubepress_api_options_OptionValidator');

        $validationService->validate($optionName, $optionValue);
        $this->setOption($optionName, $optionValue);
    }

    /**
     * Sets an option to a new value, without validation
     *
     * @param string       $optionName  The name of the option to update
     * @param unknown_type $optionValue The new option value
     * 
     * @return void
     */
    protected abstract function setOption($optionName, $optionValue);
}
