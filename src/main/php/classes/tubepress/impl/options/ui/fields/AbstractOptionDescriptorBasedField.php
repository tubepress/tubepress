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
 * Base class for fields that are directly modeled by an option descriptor.
 */
abstract class tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField extends tubepress_impl_options_ui_fields_AbstractTemplateBasedOptionsPageField
{
    /**
     * @var tubepress_spi_options_OptionDescriptor
     */
    private $_optionDescriptor;

    public function __construct($optionDescriptorName)
    {
        $odr = tubepress_impl_patterns_sl_ServiceLocator::getOptionDescriptorReference();

        $this->_optionDescriptor = $odr->findOneByName($optionDescriptorName);

        if ($this->_optionDescriptor === null) {

            throw new InvalidArgumentException(sprintf('Could not find option with name "%s"', $optionDescriptorName));
        }

        parent::__construct($optionDescriptorName, $this->_optionDescriptor->getLabel(), $this->_optionDescriptor->getDescription());
    }

    /**
     * Gets whether or not this field is TubePress Pro only.
     *
     * @return boolean True if this field is TubePress Pro only. False otherwise.
     */
    public final function isProOnly()
    {
        return $this->_optionDescriptor->isProOnly();
    }

    /**
     * Handles form submission.
     *
     * @return array An array of failure messages if there's a problem, otherwise null.
     */
    public final function onSubmit()
    {
        $hrps = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $id   = $this->getId();

        if ($this->_optionDescriptor->isBoolean()) {

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

    protected final function getOptionDescriptor()
    {
        return $this->_optionDescriptor;
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