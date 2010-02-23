<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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
    || require(dirname(__FILE__) . '/../../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_options_storage_StorageManager',
    'org_tubepress_options_Type',
    'org_tubepress_options_validation_InputValidationService',
    'org_tubepress_options_reference_OptionsReference'));

/**
 * Handles persistent storage of TubePress options
 *
 */
abstract class org_tubepress_options_storage_AbstractStorageManager implements org_tubepress_options_storage_StorageManager
{   
    private $_validationService;
    private $_optionsReference;
    
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
    public final function init()
    {
        $allOptionNames = $this->_optionsReference->getAllOptionNames();
        $vals = array();
        foreach ($allOptionNames as $optionName) {
            $vals[$optionName] = $this->_optionsReference->getDefaultValue($optionName);
        }
        
        foreach($vals as $val => $key) {
            $this->_init($val, $key);
        }
    }    

    private function _init($name, $value)
    {
        if (!$this->_optionsReference->shouldBePersisted($name)) {
            return;
        }

        if (!$this->exists($name)) {
            $this->delete($name);
            $this->create($name, $value);
        }
        if ($this->_optionsReference->getType($name) != org_tubepress_options_Type::BOOL
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
    public final function set($optionName, $optionValue)
    {
        if (!$this->_optionsReference->shouldBePersisted($optionName)) {
            return;
        }
        $this->_validationService->validate($optionName, $optionValue);
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
    
    /**
     * Set the org_tubepress_options_validation_InputValidationService
     *
     * @param org_tubepress_options_validation_InputValidationService $validationService The validation service
     */
    public function setInputValidationService(org_tubepress_options_validation_InputValidationService $validationService)
    {
        $this->_validationService = $validationService;
    }
    
    /**
     * Set the org_tubepress_options_reference_OptionsReference
     *
     * @param org_tubepress_options_reference_OptionsReference $reference The options reference
     */
    public function setOptionsReference(org_tubepress_options_reference_OptionsReference $reference)
    {
        $this->_optionsReference = $reference;
    }
}
