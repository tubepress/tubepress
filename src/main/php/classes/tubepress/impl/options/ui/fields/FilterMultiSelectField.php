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
 * Displays a multi-select drop-down input for video meta.
 */
class tubepress_impl_options_ui_fields_FilterMultiSelectField
    extends tubepress_impl_options_ui_fields_AbstractPluggableOptionsPageField
{
    const TEMPLATE_VAR_PROVIDERS = 'tubepress_impl_options_ui_fields_FilterMultiSelectField__providers';

    const TEMPLATE_VAR_CURRENTVALUES = 'tubepress_impl_options_ui_fields_FilterMultiSelectField__currentValues';

    /**
     * @var tubepress_spi_options_OptionDescriptor The underlying option descriptor.
     */
    private $_disabledParticipantsOptionDescriptor;

    public function __construct()
    {
        $odr = tubepress_impl_patterns_sl_ServiceLocator::getOptionDescriptorReference();

        $this->_disabledParticipantsOptionDescriptor = $odr->findOneByName(tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS);
    }

    /**
     * Handles form submission.
     *
     * @return array An array of failure messages if there's a problem, otherwise null.
     */
    public final function onSubmit()
    {
        $hrps                 = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $storageManager      = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();
        $optionName          = $this->_disabledParticipantsOptionDescriptor->getName();
        $allParticipantNames = array_keys($this->_getParticipantNamesToFriendlyNamesMap());

        /**
         * This means that they want to hide everything.
         */
        if (! $hrps->hasParam($optionName)) {

            $storageManager->set($optionName, implode(';', $allParticipantNames));

            return null;
        }

        $vals = $hrps->getParamValue($optionName);

        if (! is_array($vals)) {

            /* this should never happen. */
            return null;
        }

        $toHide = array();

        foreach ($allParticipantNames as $participantName) {

            /*
             * They checked the box, which means they want to show it.
             */
            if (in_array($participantName, $vals)) {

                continue;
            }

            /**
             * They don't want to show this provider, so hide it.
             */
            $toHide[] = $participantName;
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
        $templateBuilder     = tubepress_impl_patterns_sl_ServiceLocator::getTemplateBuilder();
        $storageManager      = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();
        $template            = $templateBuilder->getNewTemplateInstance(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/fields/multiselect-provider-filter.tpl.php');
        $optionName          = $this->_disabledParticipantsOptionDescriptor->getName();
        $currentHides        = explode(';', $storageManager->get($optionName));
        $participantsNameMap = $this->_getParticipantNamesToFriendlyNamesMap();
        $currentShows        = array();

        foreach ($participantsNameMap as $participantName => $participantFriendlyName) {

            if (! in_array($participantName, $currentHides)) {

                $currentShows[] = $participantName;
            }
        }

        $template->setVariable(self::TEMPLATE_VAR_NAME,          $optionName);
        $template->setVariable(self::TEMPLATE_VAR_PROVIDERS,     $participantsNameMap);
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
        return $this->_disabledParticipantsOptionDescriptor->getLabel();
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

    private function _getParticipantNamesToFriendlyNamesMap()
    {
        $participants = tubepress_impl_patterns_sl_ServiceLocator::getOptionsPageParticipants();

        $toReturn = array();

        foreach ($participants as $participant) {

            if ($participant->getName() === 'core') {

                continue;
            }

            $toReturn[$participant->getName()] = $participant->getFriendlyName();
        }

        return $toReturn;
    }
}