<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Displays a multi-select drop-down input for video meta.
 */
class tubepress_addons_core_impl_options_ui_fields_FilterMultiSelectField extends tubepress_impl_options_ui_fields_AbstractMultiSelectField
{
    const FIELD_ID = 'participant-filter-field';

    /**
     * @var tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface[]
     */
    private $_optionsPageParticipants = array();

    /**
     * @var tubepress_spi_options_OptionDescriptor The underlying option descriptor.
     */
    private $_disabledParticipantsOptionDescriptor;

    public function __construct(array $optionsPageParticipants)
    {
        $this->_optionsPageParticipants              = $optionsPageParticipants;
        $odr                                         = tubepress_impl_patterns_sl_ServiceLocator::getOptionDescriptorReference();
        $this->_disabledParticipantsOptionDescriptor = $odr->findOneByName(tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS);

        parent::__construct(

            self::FIELD_ID,
            $this->_disabledParticipantsOptionDescriptor->getName(),
            $this->_disabledParticipantsOptionDescriptor->getDescription()
        );

        $this->setUntranslatedDisplayName($this->_disabledParticipantsOptionDescriptor->getLabel());
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
     * @return string[] An array of currently selected values, which may be empty.
     */
    protected function getCurrentlySelectedValues()
    {
        $storageManager       = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();
        $optionName          = $this->_disabledParticipantsOptionDescriptor->getName();
        $currentHides        = explode(';', $storageManager->get($optionName));
        $participantsNameMap = $this->_getParticipantNamesToFriendlyNamesMap();
        $currentShows        = array();

        foreach ($participantsNameMap as $participantName => $participantFriendlyName) {

            if (! in_array($participantName, $currentHides)) {

                $currentShows[] = $participantName;
            }
        }

        return $currentShows;
    }

    /**
     * @return array An associative array of value => translated display names
     */
    protected function getUngroupedTranslatedChoicesArray()
    {
        return $this->_getParticipantNamesToFriendlyNamesMap();
    }

    protected function getAbsolutePathToTemplate()
    {
        return TUBEPRESS_ROOT . '/src/main/resources/admin-page-templates/fields/multiselect.tpl.php';
    }

    /**
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    protected function onSubmitAllMissing()
    {
        $storageManager = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();
        $participantIds = array_keys($this->_getParticipantNamesToFriendlyNamesMap());
        $newValue       = implode(';', $participantIds);

        $result = $storageManager->set($this->_disabledParticipantsOptionDescriptor->getName(), $newValue);

        if ($result !== true) {

            return $result;
        }

        return null;
    }

    /**
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    protected function onSubmitMixed()
    {
        $hrps                = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $storageManager      = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();
        $optionName          = $this->_disabledParticipantsOptionDescriptor->getName();
        $allParticipantNames = array_keys($this->_getParticipantNamesToFriendlyNamesMap());

        $vals = $hrps->getParamValue($optionName);

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

    private function _getParticipantNamesToFriendlyNamesMap()
    {
        $toReturn = array();

        foreach ($this->_optionsPageParticipants as $participant) {

            if ($participant->getId() === tubepress_addons_core_impl_options_ui_CoreOptionsPageParticipant::PARTICIPANT_ID) {

                continue;
            }

            $toReturn[$participant->getId()] = $participant->getTranslatedDisplayName();
        }

        return $toReturn;
    }
}