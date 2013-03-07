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
 * Displays a multi-select drop-down input.
 */
abstract class tubepress_impl_options_ui_fields_AbstractMultiSelectField extends tubepress_impl_options_ui_fields_AbstractPluggableOptionsPageField
{
    const TEMPLATE_VAR_DESCRIPTORS = 'tubepress_impl_options_ui_fields_AbstractMultiSelectField__descriptors';

    const TEMPLATE_VAR_CURRENTVALUES = 'tubepress_impl_options_ui_fields_AbstractMultiSelectField__currentValues';

    /** Array of option descriptors. */
    private $_optionDescriptors;

    /** Name. */
    private $_name;

    public function __construct(array $optionDescriptors, $name)
    {
        foreach ($optionDescriptors as $optionDescriptor) {

            if (! $optionDescriptor instanceof tubepress_spi_options_OptionDescriptor) {

                throw new InvalidArgumentException('Non option descriptor detected');
            }

            /** @noinspection PhpUndefinedMethodInspection */
            if (! $optionDescriptor->isBoolean()) {

                throw new InvalidArgumentException('Non-boolean option descriptor detected');
            }
        }

        if (! is_string($name)) {

            throw new InvalidArgumentException('Label must be a string');
        }

        $this->_optionDescriptors = $optionDescriptors;
        $this->_name              = $name;
    }

    /**
     * Handles form submission.
     *
     * @return array An array of failure messages if there's a problem, otherwise null.
     */
    public final function onSubmit()
    {
        $hrps           = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $storageManager = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();

        if (! $hrps->hasParam($this->_name)) {

            /* not submitted. */
            foreach ($this->_optionDescriptors as $optionDescriptor) {

                /** @noinspection PhpUndefinedMethodInspection */
                $storageManager->set($optionDescriptor->getName(), false);
            }

            return null;
        }

        $vals = $hrps->getParamValue($this->_name);

        if (! is_array($vals)) {

            /* this should never happen. */
            return null;
        }

        $errors         = array();


        foreach ($this->_optionDescriptors as $optionDescriptor) {

            /** @noinspection PhpUndefinedMethodInspection */
            $result = $storageManager->set($optionDescriptor->getName(), in_array($optionDescriptor->getName(), $vals));

            if ($result !== true) {

                $errors[] = $result;
            }
        }

        if (count($errors) === 0) {

            return null;
        }

        return $errors;
    }

    /**
     * Generates the HTML for the options form.
     *
     * @return string The HTML for the options form.
     */
    public final function getHtml()
    {
        $templateBuilder = tubepress_impl_patterns_sl_ServiceLocator::getTemplateBuilder();
        $template        = $templateBuilder->getNewTemplateInstance(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/fields/multiselect.tpl.php');
        $currentValues   = array();
        $storageManager  = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();

        foreach ($this->_optionDescriptors as $optionDescriptor) {

            /** @noinspection PhpUndefinedMethodInspection */
            if ($storageManager->get($optionDescriptor->getName())) {

                /** @noinspection PhpUndefinedMethodInspection */
                $currentValues[] = $optionDescriptor->getName();
            }
        }

        $template->setVariable(self::TEMPLATE_VAR_NAME,          $this->_name);
        $template->setVariable(self::TEMPLATE_VAR_DESCRIPTORS,   $this->_optionDescriptors);
        $template->setVariable(self::TEMPLATE_VAR_CURRENTVALUES, $currentValues);

        return $template->toString();
    }

    /**
     * Gets whether or not this field is TubePress Pro only.
     *
     * @return boolean True if this field is TubePress Pro only. False otherwise.
     */
    public final function isProOnly()
    {
        return false;
    }
}