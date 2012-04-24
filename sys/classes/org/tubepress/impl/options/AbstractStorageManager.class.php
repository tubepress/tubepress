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
    'org_tubepress_api_const_options_Type',
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_api_options_OptionDescriptor',
    'org_tubepress_api_options_OptionDescriptorReference',
    'org_tubepress_api_options_OptionValidator',
    'org_tubepress_api_options_StorageManager',
    'org_tubepress_api_plugin_PluginManager',
    'org_tubepress_impl_ioc_IocContainer',
));

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
        $ioc       = org_tubepress_impl_ioc_IocContainer::getInstance();
        $odr       = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);
        $validator = $ioc->get(org_tubepress_api_options_OptionValidator::_);
        $options   = $odr->findAll();

        foreach ($options as $option) {

            if (! $option->isMeantToBePersisted()) {

                continue;
            }

            $this->_init($option->getName(), $option->getDefaultValue(), $validator);
        }
    }

    /**
     * Initializes a single option.
     *
     * @param string $name         The option name.
     * @param string $defaultValue The option value.
     *
     * @return void
     */
    private function _init($name, $defaultValue, org_tubepress_api_options_OptionValidator $validator)
    {
        if (! $this->exists($name)) {

            $this->delete($name);
            $this->create($name, $defaultValue);
        }

        if (!$validator->isValid($name, $this->get($name))) {

            $this->setOption($name, $defaultValue);
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
        $ioc           = org_tubepress_impl_ioc_IocContainer::getInstance();
        $odr           = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);
        $validator     = $ioc->get(org_tubepress_api_options_OptionValidator::_);
        $pluginManager = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
        $descriptor    = $odr->findOneByName($optionName);
        $logPrefix     = 'Abstract storage manager';

        /** Do we even know about this option? */
        if ($descriptor === null) {

            org_tubepress_impl_log_Log::log($logPrefix, 'Could not find descriptor for option with name %s', $optionName);
            return;
        }

        /** Ignore any options that aren't meant to be persisted. */
        if (! $descriptor->isMeantToBePersisted()) {

            return;
        }

        /** First run it through the filters. */
        $filtered = $pluginManager->runFilters(org_tubepress_api_const_plugin_FilterPoint::OPTION_SET_PRE_VALIDATION, $optionValue, $optionName);

        /** OK, let's see if it's valid. */
        if ($validator->isValid($optionName, $filtered)) {

            org_tubepress_impl_log_Log::log($logPrefix, 'Accepted valid value: %s = %s', $optionName, $filtered);

            $this->setOption($optionName, $filtered);

            return true;
        }

        $problemMessage = $validator->getProblemMessage($optionName, $filtered);

        org_tubepress_impl_log_Log::log($logPrefix, 'Ignoring invalid value for "%s" (%s)', $optionName, $filtered);

        return $problemMessage;
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
