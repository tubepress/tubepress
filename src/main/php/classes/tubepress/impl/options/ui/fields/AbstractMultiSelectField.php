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
 * Displays a multi-select drop-down input.
 */
abstract class tubepress_impl_options_ui_fields_AbstractMultiSelectField extends tubepress_impl_options_ui_fields_AbstractField
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

            if (! $optionDescriptor instanceof tubepress_api_model_options_OptionDescriptor) {

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
        $hrps = tubepress_impl_patterns_ioc_KernelServiceLocator::getHttpRequestParameterService();

        if (! $hrps->hasParam($this->_name)) {

            /* not submitted. */
            foreach ($this->_optionDescriptors as $optionDescriptor) {

                /** @noinspection PhpUndefinedMethodInspection */
                $this->getStorageManager()->set($optionDescriptor->getName(), false);
            }

            return null;
        }

        $vals = $hrps->getParamValue($this->_name);

        if (! is_array($vals)) {

            /* this should never happen. */
            return null;
        }

        $errors         = array();
        $storageManager = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionStorageManager();

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
        $envDetector     = tubepress_impl_patterns_ioc_KernelServiceLocator::getEnvironmentDetector();
        $templateBuilder = tubepress_impl_patterns_ioc_KernelServiceLocator::getTemplateBuilder();
        $basePath        = $envDetector->getTubePressBaseInstallationPath();
        $template        = $templateBuilder->getNewTemplateInstance($basePath . '/src/main/resources/system-templates/options_page/fields/multiselect.tpl.php');
        $currentValues   = array();
        $storageManager  = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionStorageManager();

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

    /**
     * Gets the providers to which this field applies.
     *
     * @return array An array of provider names to which this field applies. May be empty. Never null.
     */
    public final function getArrayOfApplicableProviderNames()
    {
        return array(

            'youtube',
            'vimeo',
        );
    }
}