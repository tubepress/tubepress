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
 * Base class for options pages.
 */
class tubepress_core_options_ui_impl_Form implements tubepress_core_options_ui_api_FormInterface
{
    /**
     * @var tubepress_core_environment_api_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var tubepress_core_options_api_PersistenceInterface
     */
    private $_persistence;

    /**
     * @var tubepress_core_options_ui_api_FieldProviderInterface[] Categories.
     */
    private $_optionsPageParticipants;

    /**
     * @var tubepress_core_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_core_template_api_TemplateInterface
     */
    private $_template;

    public function __construct(tubepress_core_template_api_TemplateInterface       $template,
                                tubepress_core_environment_api_EnvironmentInterface $environment,
                                tubepress_core_options_api_PersistenceInterface     $persistence,
                                tubepress_core_event_api_EventDispatcherInterface   $eventDispatcher)
    {
        $this->_template        = $template;
        $this->_environment     = $environment;
        $this->_persistence     = $persistence;
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * @param array   $errors        An associative array, which may be empty, of field IDs to error messages.
     * @param boolean $justSubmitted True if the form was just submitted, false otherwise.
     *
     * @return string The HTML for the options page.
     */
    public function getHTML(array $errors = array(), $justSubmitted = false)
    {
        $fields                               = $this->_buildFieldsArray();
        $categories                           = $this->_buildCategoriesArray();
        $categoryIdToParticipantIdToFieldsMap = $this->_buildCategoryIdToParticipantIdToFieldsMap($categories);
        $participants                         = $this->_buildParticipantsArray();

        if (isset($fields[tubepress_core_options_ui_impl_fields_ParticipantFilterField::FIELD_ID])) {

            /**
             * @var $filterField tubepress_core_options_ui_impl_fields_ParticipantFilterField
             */
            $filterField = $fields[tubepress_core_options_ui_impl_fields_ParticipantFilterField::FIELD_ID];

            $filterField->setOptionsPageParticipants($this->_optionsPageParticipants);
        }

        $templateVariables = array(

            'categories'                           => $categories,
            'categoryIdToParticipantIdToFieldsMap' => $categoryIdToParticipantIdToFieldsMap,
            'errors'                               => $errors,
            'fields'                               => $fields,
            'isPro'                                => $this->_environment->isPro(),
            'justSubmitted'                        => $justSubmitted,
            'participants'                         => $participants,
            "successMessage"                       => 'Settings updated.',                     //>(translatable)<
            'tubePressBaseUrl'                     => $this->_environment->getBaseUrl()->toString(),
            "saveText"                             => 'Save'                                  //>(translatable)<
        );

        $this->_template->setVariables($templateVariables);

        $templateEvent = $this->_eventDispatcher->newEventInstance($this->_template);
        $this->_eventDispatcher->dispatch(tubepress_core_options_ui_api_Constants::EVENT_OPTIONS_UI_PAGE_TEMPLATE, $templateEvent);

        return $this->_template->toString();
    }

    /**
     * Invoked when the page is submitted by the user.
     *
     * @return array An associative array, which may be empty, of field IDs to error messages.
     */
    public function onSubmit()
    {
        /**
         * @var tubepress_core_options_ui_api_FieldInterface[] $fields
         */
        $fields = $this->_buildFieldsArray();
        $errors = array();

        foreach ($fields as $field) {

            $fieldError = $field->onSubmit();

            if ($fieldError) {

                $errors[$field->getId()] = $fieldError;
            }
        }

        /**
         * Let's save!
         */
        $this->_persistence->flushSaveQueue();

        return $errors;
    }

    /**
     * This function is called by the IOC container.
     *
     * @param tubepress_core_options_ui_api_FieldProviderInterface[] $participants
     */
    public function setOptionsPageParticipants(array $participants)
    {
        $this->_optionsPageParticipants = $participants;
    }

    private function _buildCategoriesArray()
    {
        $toReturn = array();

        foreach ($this->_optionsPageParticipants as $participant) {

            $toReturn = array_merge($toReturn, $participant->getCategories());
        }

        return $toReturn;
    }

    private function _buildFieldsArray()
    {
        $fields = array();

        foreach ($this->_optionsPageParticipants as $participant) {

            $fields = array_merge($fields, $participant->getFields());
        }

        $toReturn = array();

        /**
         * @var $fields tubepress_core_options_ui_api_FieldInterface[]
         */
        foreach ($fields as $field) {

            $toReturn[$field->getId()] = $field;
        }

        return $toReturn;
    }

    /**
     * @param tubepress_core_options_ui_api_ElementInterface[] $categories
     *
     * @return array
     */
    private function _buildCategoryIdToParticipantIdToFieldsMap(array $categories)
    {
        $toReturn = array();

        foreach ($categories as $category) {

            $categoryId = $category->getId();

            if (!isset($toReturn[$categoryId])) {

                $toReturn[$categoryId] = array();
            }

            foreach ($this->_optionsPageParticipants as $participant) {

                $map = $participant->getCategoryIdsToFieldIdsMap();

                if (!isset($map[$categoryId])) {

                    continue;
                }

                $toReturn[$categoryId][$participant->getId()] = $map[$categoryId];
            }

            uksort($toReturn[$categoryId], array($this, '__participantSorter'));
        }

        return $toReturn;
    }

    private function _buildParticipantsArray()
    {
        $toReturn = array();

        foreach ($this->_optionsPageParticipants as $participant) {

            $toReturn[$participant->getId()] = $participant;
        }

        uksort($toReturn, array($this, '__participantSorter'));

        return $toReturn;
    }

    public function __participantSorter($first, $second)
    {
        if ($first === 'core') {

            return -1;
        }

        if ($second === 'core') {

            return 1;
        }

        return strcmp($first, $second);
    }
}