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
 * Displays a multi-select drop-down input for video meta.
 */
class tubepress_impl_options_ui_fields_FilterMultiSelectField extends tubepress_impl_options_ui_fields_AbstractPluggableOptionsPageField
{
    const TEMPLATE_VAR_PROVIDERS = 'tubepress_impl_options_ui_fields_FilterMultiSelectField__providers';

    const TEMPLATE_VAR_CURRENTVALUES = 'tubepress_impl_options_ui_fields_FilterMultiSelectField__currentValues';

    /**
     * @var tubepress_spi_options_OptionDescriptor The underlying option descriptor.
     */
    private $_hideOptionDescriptor;

    public function __construct()
    {
        $odr = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionDescriptorReference();

        $this->_hideOptionDescriptor = $odr->findOneByName(tubepress_api_const_options_names_OptionsUi::PROVIDERS_TO_HIDE);
    }

    /**
     * Handles form submission.
     *
     * @return array An array of failure messages if there's a problem, otherwise null.
     */
    public final function onSubmit()
    {
        $hrps           = tubepress_impl_patterns_ioc_KernelServiceLocator::getHttpRequestParameterService();
        $storageManager = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionStorageManager();
        $optionName     = $this->_hideOptionDescriptor->getName();
        $providerNames  = array_keys($this->_hideOptionDescriptor->getAcceptableValues());

        /**
         * This means that they want to hide everything.
         */
        if (! $hrps->hasParam($optionName)) {

            $storageManager->set($optionName, implode(';', $providerNames));

            return null;
        }

        $vals = $hrps->getParamValue($optionName);

        if (! is_array($vals)) {

            /* this should never happen. */
            return null;
        }

        $toHide = array();
        foreach ($providerNames as $providerName) {

            /*
             * They checked the box, which means they want to show it.
             */
            if (in_array($providerName, $vals)) {

                continue;
            }

            /**
             * They don't want to show this provider, so hide it.
             */
            $toHide[] = $providerName;
        }

        $result = $storageManager->set($optionName, implode(';', $toHide));

        if ($result !== true) {

            return array($result);
        }

        return null;
    }

    /**
     * Generates the HTML for the options form.
     *
     * @return string The HTML for the options form.
     */
    public final function getHtml()
    {
        $templateBuilder  = tubepress_impl_patterns_ioc_KernelServiceLocator::getTemplateBuilder();
        $template         = $templateBuilder->getNewTemplateInstance(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/fields/multiselect-provider-filter.tpl.php');
        $storageManager   = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionStorageManager();
        $optionName       = $this->_hideOptionDescriptor->getName();
        $currentHides     = explode(';', $storageManager->get($optionName));
        $allProviders     = $this->_hideOptionDescriptor->getAcceptableValues();
        $currentShows     = array();

        foreach ($allProviders as $providerName => $providerLabel) {

            if (! in_array($providerName, $currentHides)) {

                $currentShows[] = $providerName;
            }
        }

        $template->setVariable(self::TEMPLATE_VAR_NAME,          $optionName);
        $template->setVariable(self::TEMPLATE_VAR_PROVIDERS,     $allProviders);
        $template->setVariable(self::TEMPLATE_VAR_CURRENTVALUES, $currentShows);

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
     * Get the untranslated title of this field.
     *
     * @return string The untranslated title of this field.
     */
    protected final function getRawTitle()
    {
        return $this->_hideOptionDescriptor->getLabel();
    }

    /**
     * Get the untranslated description of this field.
     *
     * @return string The untranslated description of this field.
     */
    protected final function getRawDescription()
    {
        return '';
    }

    public final function getDesiredTabName()
    {
        return null;
    }

    /**
     * Gets the providers to which this field applies.
     *
     * @return array An array of provider names to which this field applies. May be empty. Never null.
     */
    public final function getArrayOfApplicableProviderNames()
    {
        return $this->allProviders();
    }
}