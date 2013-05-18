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
 * Displays a tab on the options page.
 */
abstract class tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab extends tubepress_impl_options_ui_AbstractDelegatingFormHandler implements tubepress_spi_options_ui_PluggableOptionsPageTab
{
    const TEMPLATE_VAR_PARTICIPANT_ARRAY = 'tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab__participantArray';
    const TEMPLATE_VAR_TAB_NAME          = 'tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab__tabName';

    /**
     * @var string
     */
    private $_templatePath;

    /**
     * @var tubepress_spi_options_ui_PluggableOptionsPageParticipant[]
     */
    private $_optionsPageParticipants;

    public function __construct($templatePath)
    {
        $this->_templatePath = $templatePath;
    }

    /**
     * Get the title of this tab.
     *
     * @return string The title of this tab.
     */
    public final function getTitle()
    {
        $messageService = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();

        return $messageService->_($this->getRawTitle());
    }

    /**
     * Generates the HTML for the options form.
     *
     * @return string The HTML for the options form.
     */
    public final function getHtml()
    {
        $templateBuilder     = tubepress_impl_patterns_sl_ServiceLocator::getTemplateBuilder();
        $eventDispatcher     = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $template            = $templateBuilder->getNewTemplateInstance($this->_templatePath);
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $tabParticipants     = array();

        /**
         * @var $optionsPageParticipant tubepress_spi_options_ui_PluggableOptionsPageParticipant
         */
        foreach ($this->_optionsPageParticipants as $optionsPageParticipant) {

            if (count($optionsPageParticipant->getFieldsForTab($this->getName())) > 0) {

                array_push($tabParticipants, $optionsPageParticipant);
            }
        }

        $template->setVariable(self::TEMPLATE_VAR_PARTICIPANT_ARRAY, $tabParticipants);
        $template->setVariable(self::TEMPLATE_VAR_TAB_NAME, $this->getName());
        $template->setVariable(tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL, $environmentDetector->getBaseUrl());

        $this->addToTemplate($template);

        $templateEvent = new tubepress_spi_event_EventBase($template, array('tabName' => $this->getName()));
        $eventDispatcher->dispatch(tubepress_api_const_event_EventNames::TEMPLATE_OPTIONS_UI_TABS_SINGLE, $templateEvent);

        $template = $templateEvent->getSubject();

        return $template->toString();
    }

    /**
     * Get the delegate form handlers.
     *
     * @return array An array of tubepress_spi_options_ui_FormHandler.
     */
    public final function getDelegateFormHandlers()
    {
        $fields = array();

        /**
         * @var $optionsPageParticipant tubepress_spi_options_ui_PluggableOptionsPageParticipant
         */
        foreach ($this->_optionsPageParticipants as $optionsPageParticipant) {

            $fields = array_merge($fields, $optionsPageParticipant->getFieldsForTab($this->getName()));
        }

        return $fields;
    }

    public function setPluggableOptionsPageParticipants(array $participants)
    {
        $this->_optionsPageParticipants = $participants;
    }

    /**
     * Get the untranslated title of this tab.
     *
     * @return string The untranslated title of this tab.
     */
    protected abstract function getRawTitle();

    /**
     * Override point.
     *
     * Allows subclasses to perform additional modifications to the template.
     *
     * @param ehough_contemplate_api_Template $template The template for this tab.
     */
    protected function addToTemplate(ehough_contemplate_api_Template $template)
    {
        //override point
    }
}