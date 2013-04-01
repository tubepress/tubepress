<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Base class for HTML fields.
 */
abstract class tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField extends tubepress_impl_options_ui_fields_AbstractPluggableOptionsPageField
{
    const TEMPLATE_VAR_VALUE = 'tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField__value';

    /** Option descriptor. */
    private $_optionDescriptor;

    public function __construct($name)
    {
        $odr = tubepress_impl_patterns_sl_ServiceLocator::getOptionDescriptorReference();

        $this->_optionDescriptor = $odr->findOneByName($name);

        if ($this->_optionDescriptor === null) {

            throw new InvalidArgumentException(sprintf('Could not find option with name "%s"', $name));
        }
    }

    /**
     * Get the untranslated title of this field.
     *
     * @return string The untranslated title of this field.
     */
    public final function getRawTitle()
    {
        return $this->_optionDescriptor->getLabel();
    }

    /**
     * Get the untranslated description of this field.
     *
     * @return string The untranslated description of this field.
     */
    public final function getRawDescription()
    {
        return $this->_optionDescriptor->getDescription();
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
     * Generates the HTML for the options form.
     *
     * @return string The HTML for the options form.
     */
    public final function getHtml()
    {
        $templateBuilder     = tubepress_impl_patterns_sl_ServiceLocator::getTemplateBuilder();
        $storageManager      = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();

        $template     = $templateBuilder->getNewTemplateInstance(TUBEPRESS_ROOT . '/' . $this->getTemplatePath());
        $currentValue = $storageManager->get($this->_optionDescriptor->getName());

        $template->setVariable(self::TEMPLATE_VAR_NAME, $this->_optionDescriptor->getName());
        $template->setVariable(self::TEMPLATE_VAR_VALUE, $currentValue);

        $this->populateTemplate($template, $currentValue);

        return $template->toString();
    }

    /**
     * Handles form submission.
     *
     * @return array An array of failure messages if there's a problem, otherwise null.
     */
    public final function onSubmit()
    {
        if ($this->_optionDescriptor->isBoolean()) {

            return $this->_onSubmitBoolean();
        }

        return $this->_onSubmitSimple();
    }

    /**
     * Get the path to the template for this field, relative
     * to TubePress's root.
     *
     * @return string The path to the template for this field, relative
     *                to TubePress's root.
     */
    protected abstract function getTemplatePath();

    /**
     * Override point.
     *
     * Allows subclasses to perform additional modifications to this
     * field's template.
     *
     * @param ehough_contemplate_api_Template $template     The field's template.
     * @param string                          $currentValue The current value of this field.
     *
     * @return void
     */
    protected function populateTemplate($template, $currentValue)
    {
        //override point
    }

    protected final function getOptionDescriptor()
    {
        return $this->_optionDescriptor;
    }

    private function _onSubmitSimple()
    {
        $name            = $this->_optionDescriptor->getName();
        $hrps            = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $optionValidator = tubepress_impl_patterns_sl_ServiceLocator::getOptionValidator();

        if (! $hrps->hasParam($name)) {

            /* not submitted. */
            return null;
        }

        $value = $hrps->getParamValue($name);

        /* run it through validation. */
        if (! $optionValidator->isValid($name, $value)) {

            return array($optionValidator->getProblemMessage($name, $value));
        }

        return $this->_setToStorage($name, $value);
    }

    private function _onSubmitBoolean()
    {
        $name = $this->_optionDescriptor->getName();
        $hrps = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();

        /* if the user checked the box, the option name will appear in the POST vars */
        return $this->_setToStorage($name, $hrps->hasParam($name));
    }

    private function _setToStorage($name, $value)
    {
        $storageManager = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();

        $result = $storageManager->set($name, $value);

        if ($result === true) {

            return null;
        }

        return array($result);
    }
}