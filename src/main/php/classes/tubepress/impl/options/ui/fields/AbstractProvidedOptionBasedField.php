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
 * Base class for fields that are directly modeled by an option provider.
 */
abstract class tubepress_impl_options_ui_fields_AbstractProvidedOptionBasedField extends tubepress_impl_options_ui_fields_AbstractTemplateBasedOptionsPageField
{
    /**
     * @var tubepress_spi_options_OptionProvider
     */
    private $_optionProvider;

    public function __construct($optionName)
    {
        $this->_optionProvider = tubepress_impl_patterns_sl_ServiceLocator::getOptionProvider();

        if (!$this->_optionProvider->hasOption($optionName)) {

            throw new InvalidArgumentException(sprintf('Could not find option with name "%s"', $optionName));
        }

        $label       = $this->_optionProvider->getLabel($optionName);
        $description = $this->_optionProvider->getDescription($optionName);

        parent::__construct($optionName, $label, $description);
    }

    /**
     * Gets whether or not this field is TubePress Pro only.
     *
     * @return boolean True if this field is TubePress Pro only. False otherwise.
     */
    public final function isProOnly()
    {
        return $this->_optionProvider->isProOnly($this->getId());
    }

    /**
     * Handles form submission.
     *
     * @return array An array of failure messages if there's a problem, otherwise null.
     */
    public final function onSubmit()
    {
        $hrps      = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $id        = $this->getId();
        $isBoolean = $this->_optionProvider->isBoolean($this->getId());

        if ($isBoolean) {

            return $this->sendToStorage($id, $hrps->hasParam($id));
        }

        if (! $hrps->hasParam($id)) {

            /* not submitted. */
            return null;
        }

        $value = $hrps->getParamValue($id);

        return $this->sendToStorage($id, $this->convertIncomingStringValueToStorageFormat($value));
    }

    /**
     * @return array An associative array of template variables for this field.
     */
    protected function getTemplateVariables()
    {
        $storage = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();
        $id      = $this->getId();
        $value   = $this->convertStorageFormatToStringValueForHTML($storage->fetch($id));

        return array_merge(array(

            'id'    => $id,
            'value' => $value,

        ), $this->getAdditionalTemplateVariables());
    }

    /**
     * @return tubepress_spi_options_OptionProvider
     */
    protected function getOptionProvider()
    {
        return $this->_optionProvider;
    }

    /**
     * @return array An associative array of additional template variables for this field (besides name and value)
     */
    protected function getAdditionalTemplateVariables()
    {
        //override point
        return array();
    }

    protected function convertIncomingStringValueToStorageFormat($incomingStringValue)
    {
        //override point
        return $incomingStringValue;
    }

    /**
     * @param mixed $storageData The
     *
     * @return string The data suitable for display in an HTML field.
     */
    protected function convertStorageFormatToStringValueForHTML($storageData)
    {
        //override point
        return $storageData;
    }
}