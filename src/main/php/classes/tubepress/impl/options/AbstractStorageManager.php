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

/**
 * Handles persistent storage of TubePress options
 */
abstract class tubepress_impl_options_AbstractStorageManager implements tubepress_spi_options_StorageManager
{
    private static $_dbVersionOptionName = 'version';

    private $_optionDescriptorReference;

    private $_optionValidator;

    private $_eventDispatcher;

    private $_environmentDetector;

    private $_logger;

    public function __construct(

        tubepress_spi_options_OptionDescriptorReference $reference,
        tubepress_spi_options_OptionValidator $validator,
        tubepress_spi_environment_EnvironmentDetector $environmentDetector,
        ehough_tickertape_api_IEventDispatcher $dispatcher)
    {
        $this->_optionDescriptorReference = $reference;
        $this->_optionValidator           = $validator;
        $this->_eventDispatcher           = $dispatcher;
        $this->_environmentDetector       = $environmentDetector;
        $this->_logger                    = ehough_epilog_api_LoggerFactory::getLogger('Abstract Storage Manager');
    }

    /**
     * Initialize the persistent storage
     *
     * @return void
     */
    public final function init()
    {
        $currentVersion = $this->_environmentDetector->getVersion();
        $storedVersion  = tubepress_spi_version_Version::parse('0.0.0');
        $needToInit     = false;

        if ($this->exists(self::$_dbVersionOptionName)) {

            $storedVersionString = $this->get(self::$_dbVersionOptionName);

            if (strpos($storedVersionString, ".") === false) {

                $needToInit = true;

            } else {

                try {

                    $storedVersion = tubepress_spi_version_Version::parse($storedVersionString);

                } catch (Exception $e) {

                    $needToInit = true;
                }
            }
        } else {

            $this->create(self::$_dbVersionOptionName, (string) $currentVersion);

            $needToInit = true;
        }

        if ($needToInit || $currentVersion->compareTo($storedVersion) !== 0) {

            $this->setOption(self::$_dbVersionOptionName, (string) $currentVersion);
        }

        $options = $this->_optionDescriptorReference->findAll();

        foreach ($options as $option) {

            /** @noinspection PhpUndefinedMethodInspection */
            if (! $option->isMeantToBePersisted()) {

                continue;
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $this->_init($option->getName(), $option->getDefaultValue());
        }
    }

    /**
     * Sets an option value
     *
     * @param string $optionName  The option name
     * @param mixed  $optionValue The option value
     *
     * @return boolean True on success, otherwise a string error message.
     */
    public final function set($optionName, $optionValue)
    {
        $descriptor = $this->_optionDescriptorReference->findOneByName($optionName);

        /** Do we even know about this option? */
        if ($descriptor === null) {

            $this->_logger->warn("Could not find descriptor for option with name '$optionName''");

            return true;
        }

        /** Ignore any options that aren't meant to be persisted. */
        if (! $descriptor->isMeantToBePersisted()) {

            return true;
        }

        /** Run it through the filters. */
        $event = new tubepress_api_event_PreValidationOptionSet($optionName, $optionValue);
        $this->_eventDispatcher->dispatch(tubepress_api_event_PreValidationOptionSet::EVENT_NAME, $event);
        $filteredValue = $event->optionValue;

        /** OK, let's see if it's valid. */
        if ($this->_optionValidator->isValid($optionName, $filteredValue)) {

            $this->_logger->info(sprintf("Accepted valid value: '%s' = '%s'", $optionName, $filteredValue));

            $this->setOption($optionName, $filteredValue);

            return true;
        }

        $problemMessage = $this->_optionValidator->getProblemMessage($optionName, $filteredValue);

        $this->_logger->info(sprintf("Ignoring invalid value: '%s' = '%s'", $optionName, $filteredValue));

        return $problemMessage;
    }

    /**
     * Sets an option to a new value, without validation
     *
     * @param string $optionName  The name of the option to update
     * @param mixed  $optionValue The new option value
     *
     * @return void
     */
    protected abstract function setOption($optionName, $optionValue);

    /**
     * Creates an option in storage
     *
     * @param mixed $optionName  The name of the option to create
     * @param mixed $optionValue The default value of the new option
     *
     * @return void
     */
    protected abstract function create($optionName, $optionValue);

    /**
     * Deletes an option from storage
     *
     * @param mixed $optionName The name of the option to delete
     *
     * @return void
     */
    protected abstract function delete($optionName);

    /**
     * Initializes a single option.
     *
     * @param string $name         The option name.
     * @param string $defaultValue The option value.
     *
     * @return void
     */
    private function _init($name, $defaultValue)
    {
        if (! $this->exists($name)) {

            $this->delete($name);
            $this->create($name, $defaultValue);
        }

        if (! $this->_optionValidator->isValid($name, $this->get($name))) {

            $this->setOption($name, $defaultValue);
        }
    }
}
