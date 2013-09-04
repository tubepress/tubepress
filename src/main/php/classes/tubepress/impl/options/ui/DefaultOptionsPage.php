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
 * Base class for options pages.
 */
class tubepress_impl_options_ui_DefaultOptionsPage implements tubepress_spi_options_ui_OptionsPageInterface
{
    /**
     * @var string The absolute path to the template for the form.
     */
    private $_templatePath;

    /**
     * @var tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface[] Categories.
     */
    private $_optionsPageParticipants;

    public function __construct($templatePath)
    {
        $this->_templatePath = $templatePath;
    }

    /**
     * @param array $errors An associative array, which may be empty, of field IDs to error messages.
     *
     * @return string The HTML for the options page.
     */
    public function getHTML(array $errors = array())
    {
        $templateBldr                         = tubepress_impl_patterns_sl_ServiceLocator::getTemplateBuilder();
        $eventDispatcher                      = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $template                             = $templateBldr->getNewTemplateInstance($this->_templatePath);
        $fields                               = $this->_buildFieldsArray();
        $categories                           = $this->_buildCategoriesArray();
        $categoryIdToParticipantIdToFieldsMap = $this->_buildCategoryIdToParticipantIdToFieldsMap($categories);
        $participants                         = $this->_buildParticipantsArray();

        $template->setVariable('errors', $errors);
        $template->setVariable('fields', $fields);
        $template->setVariable('inlineJS', $this->_getInlineJS());
        $template->setVariable('categories', $categories);
        $template->setVariable('participants', $participants);
        $template->setVariable('categoryIdToParticipantIdToFieldsMap', $categoryIdToParticipantIdToFieldsMap);

        $templateEvent = new tubepress_spi_event_EventBase($template);
        $eventDispatcher->dispatch(tubepress_api_const_event_EventNames::OPTIONS_PAGE_TEMPLATE_TOSTRING, $templateEvent);

        return $template->toString();
    }

    /**
     * Invoked when the page is submitted by the user.
     *
     * @return array An associative array, which may be empty, of field IDs to error messages.
     */
    public function onSubmit()
    {
        /**
         * @var tubepress_spi_options_ui_OptionsPageFieldInterfaOptionsPageItce[] $fields
         */
        $fields = $this->_buildFieldsArray();
        $errors = array();

        foreach ($fields as $field) {

            $fieldError = $field->onSubmit();

            if ($fieldError) {

                $errors[$field->getId()] = $fieldError;
            }
        }

        return $errors;
    }

    /**
     * This function is called by the IOC container.
     *
     * @param tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface[] $participants
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
         * @var $fields tubepress_spi_options_ui_OptionsPageFieldInterface[]
         */
        foreach ($fields as $field) {

            $toReturn[$field->getId()] = $field;
        }

        return $toReturn;
    }

    /**
     * @param tubepress_spi_options_ui_OptionsPageItemInterface[] $categories
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
        }

        return $toReturn;
    }

    private function _getInlineJS()
    {
        $toReturn = '';

        foreach ($this->_optionsPageParticipants as $participant) {

            $toReturn .= $participant->getInlineJs();
        }

        return $toReturn;
    }

    private function _buildParticipantsArray()
    {
        $toReturn = array();

        foreach ($this->_optionsPageParticipants as $participant) {

            $toReturn[$participant->getId()] = $participant;
        }

        return $toReturn;
    }

}